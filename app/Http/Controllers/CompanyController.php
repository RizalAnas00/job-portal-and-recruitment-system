<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->company) {
            return redirect()->route('companies.edit', Auth::user()->company->id);
        }
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'company_description' => 'nullable|string',
            'industry' => 'nullable|string|max:100',
        ]);

        // Tambahkan user_id dari user yang sedang login
        $validatedData['user_id'] = $user->id;

        // Gunakan updateOrCreate untuk mencegah duplikat profil oleh user yang sama
        $company = Company::updateOrCreate(
            ['user_id' => $user->id],
            $validatedData
        );

        return redirect()->route('companies.show', $company)->with('success', 'Profil perusahaan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load('jobPostings');
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->id === $company->user_id) {
            return view('companies.edit', compact('company'));
        }

        abort(403, 'AKSES DITOLAK');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && $user->id !== $company->user_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'company_description' => 'nullable|string',
            'industry' => 'nullable|string|max:100',
        ]);

        $company->update($validatedData);

        return redirect()->route('companies.show', $company)->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->id === $company->user_id) {
            $company->delete();
            return redirect()->route('companies.index')->with('success', 'Profil perusahaan berhasil dihapus.');
        }

        abort(403, 'AKSES DITOLAK');
    }
}
