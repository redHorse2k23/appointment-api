<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Court;
use App\Cache\Owner\CourtCache;

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
            // 'description' => 'nullable|string',
            'court_number' => 'required|integer|max:255',
            'type'=>'required|in:indoor,outdoor',
            'hourly_rate'=>'required|numeric',
        ]);

        $court = [
            'user_id'=> auth()->id(),
            'name' => $request->name,
            'location' => $request->location,
            // 'description' => $request->description,
            'court_number'=>$request->court_number,
            'type'=>$request->type,
            'hourly_rate'=>$request->hourly_rate,
        ];

        $create = Court::create($court);
        $create->save();

        //clear cache after creating a new court
        CourtCache::clearCourts(auth()->id());
        
        return response()->json(['message' => 'Court created successfully', 'court' => $court], 201);


    }
}
