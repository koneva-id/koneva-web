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

        $clientUsers = User::query()
            ->where('role', 'client')
            ->whereDoesntHave('clientProfile')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.clients', [
            'clients' => $clients,
            'clientUsers' => $clientUsers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:clients,user_id'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'industry' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $client = Client::create($validated);

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
