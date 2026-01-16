<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenueController extends Controller
{
    public function index()
    {
        $lecturer = auth('lecturer')->user();
        $venues = Venue::where('department_id', $lecturer->department_id)
            ->orWhereNull('department_id')
            ->latest()
            ->get();
            
        return view('lecturer.venues.index', compact('venues'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.01',
            'is_active' => 'nullable|boolean',
        ]);

        $lecturer = auth('lecturer')->user();

        Venue::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'department_id' => $lecturer->department_id,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->back()->with('success', 'Venue created successfully.');
    }

    public function update(Request $request, Venue $venue)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.01',
            'is_active' => 'boolean',
        ]);

        $venue->update($request->all());

        return redirect()->back()->with('success', 'Venue updated successfully.');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();
        return redirect()->back()->with('success', 'Venue deleted successfully.');
    }
}
