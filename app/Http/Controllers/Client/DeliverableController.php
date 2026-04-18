<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliverableController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->clientProfile;

        if (! $client) {
            abort(403, 'Client profile not found.');
        }

        $deliverables = Deliverable::query()
            ->where('client_id', $client->id)
            ->where('status', 'published')
            ->where('is_visible_to_client', true)
            ->with('project:id,title')
            ->latest()
            ->get();

        return view('client.deliverables', [
            'client' => $client,
            'deliverables' => $deliverables,
        ]);
    }
}
