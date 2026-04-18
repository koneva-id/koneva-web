<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ClientRequest;
use App\Models\ClientRequestHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RequestManagementController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $requests = ClientRequest::query()
            ->with(['client.user:id,name,email', 'project:id,title'])
            ->when($status !== '' && $status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('admin.requests', [
            'requests' => $requests,
            'statusFilter' => $status === '' ? 'all' : $status,
            'statuses' => ['all', 'submitted', 'in_review', 'in_progress', 'waiting_client', 'completed', 'rejected'],
        ]);
    }

    public function show(ClientRequest $clientRequest): View
    {
        $clientRequest->load([
            'client.user:id,name,email',
            'project:id,title',
            'histories.actor:id,name,email',
        ]);

        return view('admin.request-detail', [
            'clientRequest' => $clientRequest,
            'statuses' => ['submitted', 'in_review', 'in_progress', 'waiting_client', 'completed', 'rejected'],
        ]);
    }

    public function update(Request $request, ClientRequest $clientRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['submitted', 'in_review', 'in_progress', 'waiting_client', 'completed', 'rejected'])],
            'internal_note' => ['nullable', 'string', 'max:5000'],
            'client_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $oldStatus = $clientRequest->status;

        $clientRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['internal_note'] ?? $clientRequest->admin_note,
        ]);

        ClientRequestHistory::create([
            'client_request_id' => $clientRequest->id,
            'actor_id' => $request->user()->id,
            'entry_type' => $oldStatus !== $clientRequest->status ? 'status_change' : 'note',
            'old_status' => $oldStatus,
            'new_status' => $clientRequest->status,
            'internal_note' => $validated['internal_note'] ?? null,
            'client_note' => $validated['client_note'] ?? null,
        ]);

        AuditLog::record(
            $request->user(),
            'admin.request_triaged',
            'client_requests',
            $clientRequest->id,
            'Admin triaged client request.',
            [
                'old_status' => $oldStatus,
                'new_status' => $clientRequest->status,
                'has_internal_note' => ! empty($validated['internal_note']),
                'has_client_note' => ! empty($validated['client_note']),
            ],
            $request
        );

        return redirect()->route('admin.requests.show', $clientRequest)->with('status', 'Request updated successfully.');
    }
}
