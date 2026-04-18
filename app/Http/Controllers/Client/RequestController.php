<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ClientRequest;
use App\Models\ClientRequestHistory;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $client = $user->clientProfile;

        if (! $client) {
            abort(403, 'Client profile not found.');
        }

        $clientRequests = ClientRequest::query()
            ->where('client_id', $client->id)
            ->with('project:id,title')
            ->with(['histories' => fn ($query) => $query
                ->whereNotNull('client_note')
                ->latest()
                ->limit(1),
            ])
            ->latest()
            ->get();

        $projects = Project::query()
            ->where('client_id', $client->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('client.requests', [
            'client' => $client,
            'clientRequests' => $clientRequests,
            'projects' => $projects,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $client = $user->clientProfile;

        if (! $client) {
            return back()->withErrors(['request' => 'Client profile not found.']);
        }

        $validated = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if (! empty($validated['project_id'])) {
            $isClientProject = Project::query()
                ->where('id', $validated['project_id'])
                ->where('client_id', $client->id)
                ->exists();

            if (! $isClientProject) {
                return back()->withErrors([
                    'project_id' => 'Selected project is not linked to your account.',
                ])->withInput();
            }
        }

        $clientRequest = ClientRequest::create([
            'client_id' => $client->id,
            'project_id' => $validated['project_id'] ?? null,
            'submitted_by' => $user->id,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'status' => 'submitted',
        ]);

        ClientRequestHistory::create([
            'client_request_id' => $clientRequest->id,
            'actor_id' => $user->id,
            'entry_type' => 'submission',
            'old_status' => null,
            'new_status' => 'submitted',
            'client_note' => 'Initial request submitted by client.',
        ]);

        AuditLog::record(
            $request->user(),
            'client.request_submitted',
            'client_requests',
            $clientRequest->id,
            'Client submitted request.',
            ['title' => $clientRequest->title, 'project_id' => $clientRequest->project_id],
            $request
        );

        return redirect()->route('client.requests.index')->with('status', 'Request submitted successfully.');
    }
}
