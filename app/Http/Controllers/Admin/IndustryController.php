<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of industries.
     */
    public function index(Request $request)
    {
        $query = Industry::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $industries = $query->orderBy('name')->paginate(15);

        return view('admin.industries.index', compact('industries'));
    }

    /**
     * Show the form for creating a new industry.
     */
    public function create()
    {
        return view('admin.industries.create');
    }

    /**
     * Store a newly created industry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:industries,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $industry = Industry::create($validated);

        AuditLog::log(
            'industry.created',
            $industry,
            "Industry {$industry->name} created"
        );

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industri berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified industry.
     */
    public function edit(Industry $industry)
    {
        return view('admin.industries.edit', compact('industry'));
    }

    /**
     * Update the specified industry.
     */
    public function update(Request $request, Industry $industry)
    {
        $oldValues = $industry->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:industries,name,' . $industry->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $industry->update($validated);

        AuditLog::log(
            'industry.updated',
            $industry,
            "Industry {$industry->name} updated",
            $oldValues,
            $industry->fresh()->toArray()
        );

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industri berhasil diperbarui.');
    }

    /**
     * Remove the specified industry.
     */
    public function destroy(Industry $industry)
    {
        // Check if industry is being used
        if ($industry->companies()->exists()) {
            return back()->with('error', 'Industri tidak dapat dihapus karena sedang digunakan oleh perusahaan.');
        }

        $industryName = $industry->name;
        $industry->delete();

        AuditLog::log(
            'industry.deleted',
            $industry,
            "Industry {$industryName} deleted"
        );

        return redirect()->route('admin.industries.index')
            ->with('success', "Industri {$industryName} berhasil dihapus.");
    }
}
