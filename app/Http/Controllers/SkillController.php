<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct()
    {
        // Middleware untuk memastikan hanya admin yang bisa akses
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the skills.
     */
    public function index(Request $request)
    {
        $query = Skill::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('skill_name', 'like', '%' . $request->search . '%');
        }

        $skills = $query->orderBy('skill_name', 'asc')->paginate(15);

        return view('skill.index', compact('skills'));
    }

    /**
     * Show the form for creating a new skill.
     */
    public function create()
    {
        return view('skill.create');
    }

    /**
     * Store a newly created skill in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'skill_name' => 'required|string|max:255|unique:skills,skill_name',
        ]);

        Skill::create($validatedData);

        return redirect()->route('admin.skill.index')
                         ->with('success', 'Skill berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified skill.
     */
    public function edit(Skill $skill)
    {
        return view('skill.edit', compact('skill'));
    }

    /**
     * Update the specified skill in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $validatedData = $request->validate([
            'skill_name' => 'required|string|max:255|unique:skills,skill_name,' . $skill->id,
        ]);

        $skill->update($validatedData);

        return redirect()->route('admin.skill.index')
                        ->with('success', 'Skill berhasil diperbarui.');
    }

    /**
     * Remove the specified skill from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->route('admin.skill.index')
                         ->with('success', 'Skill berhasil dihapus.');
    }
}

