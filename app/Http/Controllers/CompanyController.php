<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan; // Import model SubscriptionPlan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function create()
    {
        if (Auth::user()->company) {
            return redirect()->route('companies.edit', Auth::user()->company->id);
        }
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:100',
            'company_description' => 'required|string',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:companies,phone_number',
            'website' => 'required|url|max:255',
            'industry' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
        }

        try {
            DB::transaction(function () use ($validatedData, $logoPath) {
                $companyData = collect($validatedData)->except('logo')->toArray();
                
                if ($logoPath) {
                    $companyData['logo_path'] = $logoPath;
                }
                 
                $company = Auth::user()->company()->create($companyData);

                $defaultPlan = SubscriptionPlan::find(1);
                
                if (!$defaultPlan) {
                    throw new \Exception("Default subscription plan (ID: 1) not found.");
                }

                CompanySubscription::create([
                    'id_company' => $company->id,
                    'id_plan' => $defaultPlan->id,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays($defaultPlan->duration_days),
                    'status' => 'active',
                ]);
            });

            return redirect()->route('dashboard')->with('success', 'Profil perusahaan dan langganan awal berhasil dibuat.');

        } catch (\Exception $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            Log::error('Company creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat profil perusahaan. Hubungi admin.');
        }
    }

    public function show(Company $company)
    {
        return redirect()->route('dashboard'); 
    }

    public function edit(Company $company)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        
        if ($user->hasRole('admin') || $user->id === $company->user_id) {
            return view('companies.edit', compact('company'));
        }

        abort(403, 'AKSES DITOLAK');
    }

    public function update(Request $request, Company $company)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && $user->id !== $company->user_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $validatedData = $request->validate([
            'company_name' => 'required|string|max:100',
            'company_description' => 'required|string',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:companies,phone_number,' . $company->id,
            'website' => 'required|url|max:255',
            'industry' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $updateData = collect($validatedData)->except('logo')->toArray();

        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $updateData['logo_path'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($updateData);

        return redirect()->route('companies.edit', $company)->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }

        $company->delete();
        
        return redirect()->route('dashboard')->with('success', 'Perusahaan berhasil dihapus.');
    }
}
