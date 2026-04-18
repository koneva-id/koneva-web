<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\BillingRecord;
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\Deliverable;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $monthlyRevenue = BillingRecord::query()
            ->orderBy('issued_at')
            ->get(['issued_at', 'amount', 'status'])
            ->groupBy(function (BillingRecord $record) {
                $issuedAt = (string) $record->getRawOriginal('issued_at');

                return $issuedAt !== '' ? substr($issuedAt, 0, 7) : 'unknown';
            })
            ->map(function (Collection $rows, string $month) {
                return (object) [
                    'month' => $month,
                    'invoiced_total' => (float) $rows->sum('amount'),
                    'paid_total' => (float) $rows->where('status', 'paid')->sum('amount'),
                ];
            })
            ->values();

        return view('superadmin.reports', [
            'metrics' => [
                'totalUsers' => User::count(),
                'activeUsers' => User::where('is_active', true)->count(),
                'totalClients' => Client::count(),
                'totalProjects' => Project::count(),
                'totalRequests' => ClientRequest::count(),
                'totalDeliverables' => Deliverable::count(),
                'totalInvoices' => BillingRecord::count(),
                'paidRevenue' => BillingRecord::where('status', 'paid')->sum('amount'),
                'outstandingRevenue' => BillingRecord::whereIn('status', ['issued', 'overdue'])->sum('amount'),
            ],
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = BillingRecord::query()
            ->with(['client.user:id,email', 'project:id,title'])
            ->orderBy('issued_at')
            ->get();

        $filename = 'billing-report-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($rows): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'Invoice Number',
                'Title',
                'Client Company',
                'Client Email',
                'Project',
                'Status',
                'Currency',
                'Amount',
                'Issued At',
                'Due Date',
                'Paid At',
            ]);

            foreach ($rows as $item) {
                fputcsv($output, [
                    $item->invoice_number,
                    $item->title,
                    $item->client?->company_name,
                    $item->client?->user?->email,
                    $item->project?->title,
                    $item->status,
                    $item->currency,
                    (string) $item->amount,
                    optional($item->issued_at)->format('Y-m-d'),
                    optional($item->due_date)->format('Y-m-d'),
                    optional($item->paid_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }
}
