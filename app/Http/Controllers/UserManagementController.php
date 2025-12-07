<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function __construct()
    {
        // Middleware untuk memastikan hanya admin yang bisa akses
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of users (Job Seeker and Company).
     */
    public function index(Request $request)
    {
        // Start with base query - ensure users have role_id
        $query = User::with(['role', 'jobSeeker', 'company'])
            ->whereNotNull('role_id')
            ->whereHas('role'); // Ensure role exists

        // Filter berdasarkan role
        if ($request->filled('role')) {
            // Filter by specific role (user or company)
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        } else {
            // Default: Exclude admin users, only show user and company roles
            $query->whereHas('role', function($q) {
                $q->whereIn('name', ['user', 'company']);
            });
        }

        // Filter berdasarkan status aktif
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search functionality - search in email, job seeker name, company name
        if ($request->filled('search')) {
            $searchTerm = '%' . trim($request->search) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('email', 'like', $searchTerm)
                  ->orWhereHas('jobSeeker', function($q) use ($searchTerm) {
                      $q->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm)
                        ->orWhere('phone_number', 'like', $searchTerm);
                  })
                  ->orWhereHas('company', function($q) use ($searchTerm) {
                      $q->where('company_name', 'like', $searchTerm)
                        ->orWhere('phone_number', 'like', $searchTerm)
                        ->orWhere('industry', 'like', $searchTerm);
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get role counts for filter (with current filters applied except role filter)
        // Base query: exclude admin, only user and company
        $countQuery = User::with(['role', 'jobSeeker', 'company'])
            ->whereNotNull('role_id')
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['user', 'company']);
            });
        
        // Apply same filters except role for accurate counts
        if ($request->filled('status')) {
            $countQuery->where('is_active', $request->status === 'active');
        }
        
        if ($request->filled('search')) {
            $searchTerm = '%' . trim($request->search) . '%';
            $countQuery->where(function($q) use ($searchTerm) {
                $q->where('email', 'like', $searchTerm)
                  ->orWhereHas('jobSeeker', function($q) use ($searchTerm) {
                      $q->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm)
                        ->orWhere('phone_number', 'like', $searchTerm);
                  })
                  ->orWhereHas('company', function($q) use ($searchTerm) {
                      $q->where('company_name', 'like', $searchTerm)
                        ->orWhere('phone_number', 'like', $searchTerm)
                        ->orWhere('industry', 'like', $searchTerm);
                  });
            });
        }
        
        $roleCounts = [
            'user' => (clone $countQuery)->whereHas('role', fn($q) => $q->where('name', 'user'))->count(),
            'company' => (clone $countQuery)->whereHas('role', fn($q) => $q->where('name', 'company'))->count(),
            'all' => (clone $countQuery)->count(),
        ];

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    /**
     * Toggle user active status (activate/deactivate).
     */
    public function toggleStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        // Prevent deactivating other admins
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Tidak dapat mengubah status akun admin lain.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.users.index')
                         ->with('success', "Akun {$user->email} berhasil {$status}.");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', "Password untuk akun {$user->email} berhasil direset.");
    }

    /**
     * Show reset password form.
     */
    public function showResetPasswordForm(User $user)
    {
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Delete user (Hard Delete).
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting other admins
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Tidak dapat menghapus akun admin lain.');
        }

        $email = $user->email;
        $user->delete(); // Hard delete

        return redirect()->route('admin.users.index')
                         ->with('success', "Akun {$email} berhasil dihapus.");
    }
}

