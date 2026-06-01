<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Deliverable;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeliverableManagementController extends Controller
{
    /**
     * Resolve absolute path to the public storage deliverables directory.
     */
    private function deliverablesDiskPath(): string
    {
        return storage_path('app/public/deliverables');
    }

    /**
     * Build public URL for a relative storage path (e.g. "deliverables/file.jpg").
     * Does NOT use Storage facade so it avoids the finfo/fileinfo extension.
     */
    private function publicUrl(string $relativePath): string
    {
        return rtrim(config('app.url'), '/') . '/storage/' . ltrim($relativePath, '/');
    }

    /**
     * Move uploaded file to storage/app/public/deliverables and return the relative path.
     * Uses native move_uploaded_file() — no Flysystem / finfo required.
     */
    private function storeUploadedFile(\Illuminate\Http\UploadedFile $file): ?string
    {
        try {
            $dir = $this->deliverablesDiskPath();

            if (! is_dir($dir)) {
                if (! @mkdir($dir, 0775, true) && ! is_dir($dir)) {
                    return null;
                }
            }

            $filename = time() . '_' . preg_replace('/[^\w.\-]/', '_', $file->getClientOriginalName());
            $file->move($dir, $filename);

            if (! file_exists($dir . '/' . $filename)) {
                return null;
            }

            return 'deliverables/' . $filename;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('File upload storage error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from public storage by its relative path.
     */
    private function deleteStoredFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        $absolute = storage_path('app/public/' . ltrim($relativePath, '/'));

        if (file_exists($absolute)) {
            @unlink($absolute);
        }
    }

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
            'clients'      => $clients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'            => ['required', 'integer', 'exists:clients,id'],
            'project_id'           => ['nullable', 'integer', 'exists:projects,id'],
            'title'                => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:5000'],
            'status'               => ['required', Rule::in(['draft', 'review', 'published'])],
            'is_visible_to_client' => ['nullable', 'boolean'],
            'file'                 => ['required', 'file', 'max:20480'],
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
        $relativePath = $this->storeUploadedFile($file);

        if ($relativePath === null) {
            return back()->withErrors(['file' => 'Gagal menyimpan file. Periksa izin direktori storage.'])->withInput();
        }

        $isVisible = (bool) ($validated['is_visible_to_client'] ?? false);
        $status    = $validated['status'];

        $deliverable = Deliverable::create([
            'client_id'            => $validated['client_id'],
            'project_id'           => $validated['project_id'] ?? null,
            'uploaded_by'          => $request->user()->id,
            'title'                => $validated['title'],
            'description'          => $validated['description'] ?? null,
            'file_name'            => $file->getClientOriginalName(),
            'file_type'            => $file->getMimeType() ?? $file->getClientMimeType(),
            'storage_path'         => $relativePath,
            'file_url'             => $this->publicUrl($relativePath),
            'version'              => $nextVersion,
            'status'               => $status,
            'is_visible_to_client' => $status === 'published' ? $isVisible : false,
            'published_at'         => $status === 'published' ? now() : null,
        ]);

        AuditLog::record(
            $request->user(),
            'admin.deliverable_created',
            'deliverables',
            $deliverable->id,
            'Admin created deliverable and uploaded file.',
            [
                'title'                => $deliverable->title,
                'version'              => $deliverable->version,
                'status'               => $deliverable->status,
                'is_visible_to_client' => $deliverable->is_visible_to_client,
            ],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable published successfully.');
    }

    public function update(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $validated = $request->validate([
            'status'               => ['required', Rule::in(['draft', 'review', 'published'])],
            'is_visible_to_client' => ['nullable', 'boolean'],
            'description'          => ['nullable', 'string', 'max:5000'],
        ]);

        $oldStatus     = $deliverable->status;
        $oldVisibility = $deliverable->is_visible_to_client;

        $newStatus     = $validated['status'];
        $newVisibility = (bool) ($validated['is_visible_to_client'] ?? false);

        $deliverable->update([
            'status'               => $newStatus,
            'is_visible_to_client' => $newStatus === 'published' ? $newVisibility : false,
            'published_at'         => $newStatus === 'published' && ! $deliverable->published_at ? now() : $deliverable->published_at,
            'description'          => $validated['description'] ?? $deliverable->description,
        ]);

        AuditLog::record(
            $request->user(),
            'admin.deliverable_updated',
            'deliverables',
            $deliverable->id,
            'Admin updated deliverable publishing state.',
            [
                'old_status'      => $oldStatus,
                'new_status'      => $deliverable->status,
                'old_visibility'  => $oldVisibility,
                'new_visibility'  => $deliverable->is_visible_to_client,
            ],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable updated.');
    }

    public function replaceFile(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $file        = $request->file('file');
        $newPath     = $this->storeUploadedFile($file);

        if ($newPath === null) {
            return back()->withErrors(['file' => 'Gagal menyimpan file. Periksa izin direktori storage.']);
        }

        // Delete old file
        $this->deleteStoredFile($deliverable->storage_path);

        $deliverable->update([
            'file_name'            => $file->getClientOriginalName(),
            'file_type'            => $file->getMimeType() ?? $file->getClientMimeType(),
            'storage_path'         => $newPath,
            'file_url'             => $this->publicUrl($newPath),
            'version'              => $deliverable->version + 1,
            'status'               => $deliverable->status === 'published' ? 'review' : $deliverable->status,
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
                'title'       => $deliverable->title,
            ],
            $request
        );

        return redirect()->route('admin.deliverables.index')->with('status', 'Deliverable file replaced successfully.');
    }

    public function destroy(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $this->deleteStoredFile($deliverable->storage_path);

        $title = $deliverable->title;
        $id    = $deliverable->id;
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
