<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientManagementController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()
            ->with('user:id,name,email')
            ->latest()
            ->get();

        return view('admin.clients', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'industry' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'client',
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'industry' => $validated['industry'],
            'status' => $validated['status'],
        ]);

        AuditLog::record(
            $request->user(),
            'admin.client_profile_created',
            'clients',
            $client->id,
            'Admin created client profile.',
            ['company_name' => $client->company_name, 'user_id' => $client->user_id],
            $request
        );

        return redirect()->route('admin.clients.index')->with('status', 'Client profile created.');
    }
}
