<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionPlan; // Import model SubscriptionPlan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        // Validasi disesuaikan dengan skema tabel companies
        $request->validate([
            'company_name' => 'required|string|max:100',
            'company_description' => 'required|string',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:companies,phone_number',
            'website' => 'required|url|max:255',
            'industry' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat profil perusahaan
                $company = Auth::user()->company()->create([
                    'company_name' => $request->company_name,
                    'company_description' => $request->company_description,
                    'address' => $request->address,
                    'phone_number' => $request->phone_number,
                    'website' => $request->website,
                    'industry' => $request->industry,
                ]);

                // 2. Cari paket langganan default (id=1)
                $defaultPlan = SubscriptionPlan::find(1);
                if (!$defaultPlan) {
                    // Jika plan tidak ditemukan, batalkan transaksi
                    throw new \Exception("Default subscription plan (ID: 1) not found.");
                }

                // 3. Buat langganan default berdasarkan durasi dari plan
                CompanySubscription::create([
                    'id_company' => $company->id,
                    'id_plan' => $defaultPlan->id,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays($defaultPlan->duration_days), // Gunakan durasi dari plan
                    'status' => 'active',
                ]);
            });

            return redirect()->route('dashboard')->with('success', 'Profil perusahaan dan langganan awal berhasil dibuat.');

        } catch (\Exception $e) {
            // Jika terjadi error, catat log dan kembalikan pesan error
            \Log::error('Company creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat profil perusahaan. Silakan coba lagi atau hubungi administrator.');
        }
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

        // --- PERBAIKAN VALIDASI DI SINI ---
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:100',
            'company_description' => 'required|string',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:companies,phone_number,' . $company->id,
            'website' => 'required|url|max:255',
            'industry' => 'required|string|max:255',
        ]);

        $company->update($validatedData);

        return redirect()->route('companies.show', $company)->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil dihapus.');
    }
}
