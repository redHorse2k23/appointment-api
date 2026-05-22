<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use App\Models\Courts;

class CourtController extends Controller
{
    public function allCourt(Request $request){

        $courts = Courts::where('user_id', auth()->id())->paginate(10);
        return response()->json($courts);

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

        $create = Courts::create($court);
        $create->save();

        return response()->json(['message' => 'Court created successfully', 'court' => $court], 201);


    }
}
