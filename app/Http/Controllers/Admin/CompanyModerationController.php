<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of companies for moderation.
     */
    public function index(Request $request)
    {
        $query = Company::with(['user', 'industry']);

        // Filter by verification status
        if ($request->filled('verification_status')) {
            if ($request->verification_status === 'unverified') {
                $query->where('is_verified', false);
            } elseif ($request->verification_status === 'verified') {
                $query->where('is_verified', true);
            }
        } else {
            // Default: show unverified first
            $query->orderBy('is_verified', 'asc');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }

        $companies = $query->latest()->paginate(15);

        $stats = [
            'total' => Company::count(),
            'verified' => Company::where('is_verified', true)->count(),
            'unverified' => Company::where('is_verified', false)->count(),
        ];

        return view('admin.companies.index', compact('companies', 'stats'));
    }

    /**
     * Show the form for editing the company profile.
     */
    public function edit(Company $company)
    {
        $company->load(['user', 'industry']);
        $industries = \App\Models\Industry::active()->orderBy('name')->get();
        
        return view('admin.companies.edit', compact('company', 'industries'));
    }

    /**
     * Update the company profile.
     */
    public function update(Request $request, Company $company)
    {
        $oldValues = $company->toArray();

        $validated = $request->validate([
            'company_name' => 'required|string|max:100',
            'company_description' => 'required|string',
            'phone_number' => 'required|string|max:15|unique:companies,phone_number,' . $company->id,
            'website' => 'nullable|url|max:255',
            'industry_id' => 'nullable|exists:industries,id',
            'industry' => 'nullable|string|max:100', // Fallback if industry_id not set
            'address' => 'required|string',
            'is_verified' => 'boolean',
        ]);

        // If industry_id is provided, use it; otherwise use industry string
        if (isset($validated['industry_id'])) {
            $validated['industry'] = \App\Models\Industry::find($validated['industry_id'])->name ?? $validated['industry'] ?? null;
        }

        $company->update($validated);

        // Log the action
        AuditLog::log(
            'company.profile.updated',
            $company,
            "Admin updated company profile: {$company->company_name}",
            $oldValues,
            $company->fresh()->toArray()
        );

        return redirect()->route('admin.companies.index')
            ->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Verify a company.
     */
    public function verify(Company $company)
    {
        $oldVerified = $company->is_verified;
        $company->update(['is_verified' => true]);

        AuditLog::log(
            'company.verified',
            $company,
            "Company {$company->company_name} verified by admin"
        );

        return back()->with('success', 'Perusahaan berhasil diverifikasi.');
    }

    /**
     * Unverify a company.
     */
    public function unverify(Company $company)
    {
        $company->update(['is_verified' => false]);

        AuditLog::log(
            'company.unverified',
            $company,
            "Company {$company->company_name} unverified by admin"
        );

        return back()->with('success', 'Status verifikasi perusahaan dihapus.');
    }

    /**
     * Delete a company (soft delete).
     */
    public function destroy(Company $company)
    {
        $companyName = $company->company_name;
        $company->delete();

        AuditLog::log(
            'company.deleted',
            $company,
            "Company {$companyName} deleted by admin"
        );

        return redirect()->route('admin.companies.index')
            ->with('success', "Perusahaan {$companyName} berhasil dihapus.");
    }
}
