<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectManagementController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->with(['client.user:id,name,email'])
            ->latest()
            ->get();

        $clients = Client::query()
            ->with('user:id,name,email')
            ->orderBy('company_name')
            ->get();

        return view('admin.projects', [
            'projects' => $projects,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'package_type' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:planning,active,on_hold,completed'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $validated['created_by'] = $request->user()->id;

        $project = Project::create($validated);

        AuditLog::record(
            $request->user(),
            'admin.project_created',
            'projects',
            $project->id,
            'Admin created project.',
            ['title' => $project->title, 'client_id' => $project->client_id],
            $request
        );

        return redirect()->route('admin.projects.index')->with('status', 'Project created successfully.');
    }
}
