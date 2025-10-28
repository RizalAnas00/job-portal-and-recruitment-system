<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:user,company'],
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign default role 'user' unless 'company' explicitly selected
        $selectedRoleName = $request->input('role', 'user');
        if (!in_array($selectedRoleName, ['user', 'company'])) {
            $selectedRoleName = 'user';
        }

        try {
            $user->assignRole($selectedRoleName);
        } catch (\Exception $e) {
            // Fallback: ensure roles seeded; default to user role if exists
            $role = Role::where('name', 'user')->first();
            if ($role) {
                $user->update(['role_id' => $role->id]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        if ($user->hasRole('company')) {
            return redirect()->route('companies.create');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
