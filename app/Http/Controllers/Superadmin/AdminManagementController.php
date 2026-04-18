<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function index(): View
    {
        return view('superadmin.admins', [
            'admins' => User::query()
                ->whereIn('role', ['admin', 'superadmin'])
                ->orderBy('role')
                ->orderBy('name')
                ->get(),
            'clients' => User::query()
                ->where('role', 'client')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'is_active']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,superadmin'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        AuditLog::record(
            $request->user(),
            'superadmin.admin_created',
            'users',
            $user->id,
            'Superadmin created a privileged account.',
            ['created_role' => $user->role, 'created_email' => $user->email],
            $request
        );

        return redirect()->route('superadmin.admins.index')->with('status', 'Admin account created successfully.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:client,admin,superadmin'],
        ]);

        if ($user->role === 'superadmin' && $validated['role'] !== 'superadmin') {
            $activeSuperadminCount = User::query()
                ->where('role', 'superadmin')
                ->where('is_active', true)
                ->count();

            if ($activeSuperadminCount <= 1 && $user->is_active) {
                return redirect()->route('superadmin.admins.index')->withErrors([
                    'role' => 'Cannot demote the last active superadmin.',
                ]);
            }
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        AuditLog::record(
            $request->user(),
            'superadmin.role_updated',
            'users',
            $user->id,
            'Superadmin updated user role.',
            ['old_role' => $oldRole, 'new_role' => $user->role, 'target_email' => $user->email],
            $request
        );

        return redirect()->route('superadmin.admins.index')->with('status', 'User role updated.');
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('superadmin.admins.index')->withErrors([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($user->role === 'superadmin' && $user->is_active) {
            $activeSuperadminCount = User::query()
                ->where('role', 'superadmin')
                ->where('is_active', true)
                ->count();

            if ($activeSuperadminCount <= 1) {
                return redirect()->route('superadmin.admins.index')->withErrors([
                    'status' => 'Cannot deactivate the last active superadmin.',
                ]);
            }
        }

        $newStatus = ! $user->is_active;
        $user->update(['is_active' => $newStatus]);

        AuditLog::record(
            $request->user(),
            'superadmin.status_toggled',
            'users',
            $user->id,
            'Superadmin changed user active status.',
            ['is_active' => $newStatus, 'target_email' => $user->email],
            $request
        );

        return redirect()->route('superadmin.admins.index')->with('status', 'User status updated.');
    }
}
