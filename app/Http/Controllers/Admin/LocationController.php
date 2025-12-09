<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of locations.
     */
    public function index(Request $request)
    {
        $query = Location::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('city', 'like', "%{$request->search}%")
                  ->orWhere('province', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $locations = $query->orderBy('province')->orderBy('city')->orderBy('name')->paginate(15);

        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:locations,name',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location = Location::create($validated);

        AuditLog::log(
            'location.created',
            $location,
            "Location {$location->name} created"
        );

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Location $location)
    {
        $oldValues = $location->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:locations,name,' . $location->id,
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location->update($validated);

        AuditLog::log(
            'location.updated',
            $location,
            "Location {$location->name} updated",
            $oldValues,
            $location->fresh()->toArray()
        );

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Location $location)
    {
        // Check if location is being used
        if ($location->jobPostings()->exists()) {
            return back()->with('error', 'Lokasi tidak dapat dihapus karena sedang digunakan oleh lowongan pekerjaan.');
        }

        $locationName = $location->name;
        $location->delete();

        AuditLog::log(
            'location.deleted',
            $location,
            "Location {$locationName} deleted"
        );

        return redirect()->route('admin.locations.index')
            ->with('success', "Lokasi {$locationName} berhasil dihapus.");
    }
}
