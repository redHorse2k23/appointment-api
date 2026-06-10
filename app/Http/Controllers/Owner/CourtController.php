<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Court;
use App\Models\CourtAttachment;
use App\Cache\Owner\CourtCache;
use App\Models\CourtSchedule;
use Illuminate\Validation\Rule;
use App\Services\Limiter;
use Illuminate\Support\Facades\Storage;

class CourtController extends Controller
{
    public function allCourt(Request $request){

        $this->authorize('canViewCourts', User::class);

        $courts = CourtCache::getCourts(auth()->id());

        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $paginatedCourts = $courts->forPage($page, $perPage)->values();
        return response()->json($paginatedCourts);
    }

    public function createCourt(Request $request){
        $this->authorize('canCreateCourt', User::class);

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'court_number' => 'required|integer|max:255',
            'type'=>'required|in:indoor,outdoor',
            'hourly_rate'=>'required|numeric',
            'policy' => 'nullable|string'
        ]);

        $court = [
            'user_id'=> auth()->id(),
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'court_number'=>$request->court_number,
            'type'=>$request->type,
            'hourly_rate'=>$request->hourly_rate,
            'policy'=>$request->policy
        ];

        $create = Court::create($court);
        $create->save();

        //clear cache after creating a new court
        CourtCache::clearCourts(auth()->id());
        
        return response()->json(['message' => 'Court created successfully', 'court' => $court], 201);


    }


    public function showCourt($courtId){
        $this->authorize('canCreateCourt',User::class);
        
        $court = $this->validateCourt($courtId);

        if ($court instanceof \Illuminate\Http\JsonResponse) {
            return $court;
        }

        $court = CourtCache::showCourt($courtId);
        return response()->json($court);
    }

    public function createCourtSchedule(Request $request, $courtId)
    {
        $this->authorize('canCreateCourt', User::class);

        $request->validate([
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'nullable|in:available,unavailable,maintenance',
        ]);

        $court = $this->validateCourt($courtId);

        if ($court instanceof \Illuminate\Http\JsonResponse) {
            return $court;
        }

       
        $limiter = Limiter::handle('create-schedule-'.$courtId.'-'.$request->day,3);

        if (!$limiter['allowed']) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later (in ' . $limiter['seconds'] . ' seconds).'
            ], 429);
        }

        $schedule = $court->schedules()->updateOrCreate(
            [
                'day' => $request->day,
                'court_id' => $courtId,
            ],
            [
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status ?? 'available',
            ]
        );

        CourtCache::clearSchedule($courtId);
        
        return response()->json([
            'message' => 'Schedule saved successfully',
            'schedule' => $schedule
        ], 200);
    }

    public function getCourtSchedules($courtId)
    {

        $court = $this->validateCourt($courtId);

        if ($court instanceof \Illuminate\Http\JsonResponse) {
            return $court;
        }

        return response()->json(
            CourtCache::getCourtSchedule($court->id),
            200
        );
    }


    public function uploadAttachment(Request $request, $courtId)
    {
        $this->authorize('canCreateCourt', User::class);

        $court = $this->validateCourt($courtId);

        if ($court instanceof \Illuminate\Http\JsonResponse) {
            return $court;
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $attachmentCount = $court->attachments()->count();

        if ($attachmentCount >= 5) {
            return response()->json([
                'message' => 'Maximum 5 images allowed per court.',
            ], 422);
        }

        $file = $request->file('image');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('court_attachments/' . $courtId, $fileName, 'public');

        $attachment = CourtAttachment::create([
            'court_id' => $courtId,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $url = Storage::url($attachment->file_path);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'attachment' => $attachment,
            'url' => $url,
        ], 201);
    }

    public function deleteAttachment($courtId, $attachmentId)
    {
        $this->authorize('canCreateCourt', User::class);

        $court = $this->validateCourt($courtId);

        if ($court instanceof \Illuminate\Http\JsonResponse) {
            return $court;
        }

        $attachment = CourtAttachment::find($attachmentId);

        if (!$attachment || ((int) $attachment->court_id !== (int) $courtId)) {
            return response()->json(['message' => 'Attachment not found'], 404);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }

    public function getAttachments($courtId)
    {
        $court = $this->validateCourt($courtId);

        if ($court instanceof \Illuminate\Http\JsonResponse) {
            return $court;
        }

        $attachments = $court->attachments()->get();

        return response()->json($attachments);
    }

    public function validateCourt($courtId)
    {
        $court = Court::find($courtId);

        if (!$court) {
            return response()->json(['message' => 'Court not found'], 404);
        }

        if ($court->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden: you do not own this court'], 403);
        }

        return $court;
    }
}