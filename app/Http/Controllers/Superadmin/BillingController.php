<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BillingRecord;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use TCPDF;

class BillingController extends Controller
{
    public function index(): View
    {
        return view('superadmin.billing', [
            'billingRecords' => BillingRecord::query()
                ->with(['client.user:id,name,email', 'project:id,title'])
                ->latest('issued_at')
                ->latest()
                ->get(),
            'clients' => Client::query()
                ->with(['user:id,name,email', 'projects:id,client_id,title'])
                ->orderBy('company_name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'invoice_number' => ['required', 'string', 'max:60', 'unique:billing_records,invoice_number'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', Rule::in(['draft', 'issued', 'paid', 'overdue', 'cancelled'])],
            'issued_at' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $billing = BillingRecord::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
            'paid_at' => $validated['status'] === 'paid' ? now() : null,
            'currency' => strtoupper($validated['currency']),
        ]);

        AuditLog::record(
            $request->user(),
            'superadmin.billing_created',
            'billing_records',
            $billing->id,
            'Superadmin created a billing record.',
            [
                'invoice_number' => $billing->invoice_number,
                'amount' => $billing->amount,
                'status' => $billing->status,
            ],
            $request
        );

        return redirect()->route('superadmin.billing.index')->with('status', 'Billing record created.');
    }

    public function updateStatus(Request $request, BillingRecord $billing): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'issued', 'paid', 'overdue', 'cancelled'])],
        ]);

        $oldStatus = $billing->status;

        $billing->update([
            'status' => $validated['status'],
            'paid_at' => $validated['status'] === 'paid' && ! $billing->paid_at ? now() : $billing->paid_at,
            'updated_by' => $request->user()->id,
        ]);

        AuditLog::record(
            $request->user(),
            'superadmin.billing_status_updated',
            'billing_records',
            $billing->id,
            'Superadmin updated billing status.',
            [
                'invoice_number' => $billing->invoice_number,
                'old_status' => $oldStatus,
                'new_status' => $billing->status,
            ],
            $request
        );

        return redirect()->route('superadmin.billing.index')->with('status', 'Billing status updated.');
    }

    public function downloadPdf(BillingRecord $billing): Response
    {
        $billing->load(['client.user:id,name,email', 'project:id,title']);

        $html = view('superadmin.invoice-pdf', [
            'billing' => $billing,
        ])->render();

        $pdf = new TCPDF();
        $pdf->SetCreator('Koneva');
        $pdf->SetAuthor('Koneva');
        $pdf->SetTitle('Invoice '.$billing->invoice_number);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        $output = $pdf->Output('invoice-'.$billing->invoice_number.'.pdf', 'S');

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-'.$billing->invoice_number.'.pdf"',
        ]);
    }
}
