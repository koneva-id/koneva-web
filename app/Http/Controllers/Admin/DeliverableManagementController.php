<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Deliverable;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeliverableManagementController extends Controller
{
    public function index(): View
    {
        $deliverables = Deliverable::query()
            ->with(['client.user:id,name,email', 'project:id,title'])
            ->latest()
            ->get();

        $clients = Client::query()
            ->with(['user:id,name,email', 'projects:id,client_id,title'])
            ->orderBy('company_name')
            ->get();

        return view('admin.deliverables', [
            'deliverables' => $deliverables,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['draft', 'review', 'published'])],
            'is_visible_to_client' => ['nullable', 'boolean'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        if (! empty($validated['project_id'])) {
            $belongsToClient = Project::query()
                ->where('id', $validated['project_id'])
                ->where('client_id', $validated['client_id'])
                ->exists();

            if (! $belongsToClient) {
                return back()->withErrors([
                    'project_id' => 'Project does not belong to the selected client.',
                ])->withInput();
            }
        }

        $latestVersion = Deliverable::query()
            ->where('client_id', $validated['client_id'])
            ->where('project_id', $validated['project_id'] ?? null)
            ->where('title', $validated['title'])
            ->max('version');

        $nextVersion = ((int) $latestVersion) + 1;

        $file = $request->file('file');
        $path = $file->store('deliverables', 'public');

        $isVisible = (bool) ($validated['is_visible_to_client'] ?? false);
        $status = $validated['status'];

        $deliverable = Deliverable::create([
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'] ?? null,
            'uploaded_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'storage_path' => $path,
            'file_url' => Storage::url($path),
            'version' => $nextVersion,
            'status' => $status,
            'is_visible_to_client' => $status === 'published' ? $isVisible : false,
            'published_at' => $status === 'published' ? now() : null,
        ]);

        AuditLog::record(
            $request->user(),
            'admin.deliverable_created',
            'deliverables',
            $deliverable->id,
            'Admin created deliverable and uploaded file.',
            [
                'title' => $deliverable->title,
                'version' => $deliverable->version,
                'status' => $deliverable->status,
                'is_visible_to_client' => $deliverable->is_visible_to_client,
            ],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable published successfully.');
    }

    public function update(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'review', 'published'])],
            'is_visible_to_client' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $oldStatus = $deliverable->status;
        $oldVisibility = $deliverable->is_visible_to_client;

        $newStatus = $validated['status'];
        $newVisibility = (bool) ($validated['is_visible_to_client'] ?? false);

        $deliverable->update([
            'status' => $newStatus,
            'is_visible_to_client' => $newStatus === 'published' ? $newVisibility : false,
            'published_at' => $newStatus === 'published' && ! $deliverable->published_at ? now() : $deliverable->published_at,
            'description' => $validated['description'] ?? $deliverable->description,
        ]);

        AuditLog::record(
            $request->user(),
            'admin.deliverable_updated',
            'deliverables',
            $deliverable->id,
            'Admin updated deliverable publishing state.',
            [
                'old_status' => $oldStatus,
                'new_status' => $deliverable->status,
                'old_visibility' => $oldVisibility,
                'new_visibility' => $deliverable->is_visible_to_client,
            ],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable updated.');
    }

    public function replaceFile(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $file = $validated['file'];
        $newPath = $file->store('deliverables', 'public');

        $oldPath = $deliverable->storage_path;
        if (! empty($oldPath) && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $deliverable->update([
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'storage_path' => $newPath,
            'file_url' => Storage::url($newPath),
            'version' => $deliverable->version + 1,
            'status' => $deliverable->status === 'published' ? 'review' : $deliverable->status,
            'is_visible_to_client' => false,
        ]);

        AuditLog::record(
            $request->user(),
            'admin.deliverable_file_replaced',
            'deliverables',
            $deliverable->id,
            'Admin replaced deliverable file and incremented version.',
            [
                'new_version' => $deliverable->version,
                'title' => $deliverable->title,
            ],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable file replaced successfully.');
    }

    public function destroy(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $oldPath = $deliverable->storage_path;
        if (! empty($oldPath) && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $title = $deliverable->title;
        $id = $deliverable->id;
        $deliverable->delete();

        AuditLog::record(
            $request->user(),
            'admin.deliverable_deleted',
            'deliverables',
            $id,
            'Admin deleted deliverable and cleaned storage file.',
            ['title' => $title],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable deleted successfully.');
    }
}
