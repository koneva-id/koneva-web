<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $billing->invoice_number }}</title>
    <style>
    body {
        font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
        color: #374151;
        font-size: 12px;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }

    .page {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
    }

    /* Header section */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .header-table td {
        vertical-align: top;
        border: none;
        padding: 0;
    }

    .brand-name {
        font-size: 28px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }

    .brand-subtitle {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .invoice-title {
        text-align: right;
        font-size: 32px;
        font-weight: 800;
        color: #2563eb;
        letter-spacing: 1px;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .invoice-meta {
        text-align: right;
        color: #4b5563;
        font-size: 12px;
    }

    .invoice-meta strong {
        color: #111827;
    }

    /* Information Grid */
    .info-grid {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .info-grid td {
        vertical-align: top;
        padding: 16px;
        background-color: #f8fafc;
        border-radius: 8px;
    }

    .info-grid .spacer {
        width: 4%;
        background: transparent;
        padding: 0;
    }

    .section-title {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 4px;
    }

    .info-text {
        color: #111827;
        font-size: 13px;
    }

    /* Status Badges */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-paid {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-issued {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-overdue {
        background-color: #fef08a;
        color: #92400e;
    }

    .badge-draft {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .badge-cancelled {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    /* Items Table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .items-table th {
        background-color: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #cbd5e1;
    }

    .items-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #e2e8f0;
        color: #111827;
        vertical-align: top;
    }

    .items-table .text-right {
        text-align: right;
    }

    .item-title {
        font-weight: 700;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .item-notes {
        font-size: 11px;
        color: #6b7280;
    }

    /* Totals */
    .totals-table {
        width: 100%;
        border-collapse: collapse;
    }

    .totals-table td {
        padding: 12px;
        text-align: right;
    }

    .total-row td {
        font-size: 18px;
        font-weight: 800;
        color: #111827;
        background-color: #f8fafc;
        border-top: 2px solid #2563eb;
    }

    /* Footer */
    .footer {
        margin-top: 50px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
        font-size: 11px;
        color: #94a3b8;
        text-align: center;
    }
    </style>
</head>

<body>
    @php
    $status = strtolower((string) $billing->status);
    $badgeClass = match ($status) {
    'paid' => 'badge-paid',
    'issued' => 'badge-issued',
    'overdue' => 'badge-overdue',
    'draft' => 'badge-draft',
    'cancelled' => 'badge-cancelled',
    default => 'badge-draft',
    };
    @endphp

    <div class="page">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <div class="brand-name">KONEVA</div>
                    <div class="brand-subtitle">Digital Solutions</div>
                </td>
                <td style="width: 50%;" align="right">
                    <div class="invoice-title">Invoice</div>
                    <div class="invoice-meta">
                        Invoice No: <strong>#{{ $billing->invoice_number }}</strong><br>
                        Issue Date: <strong>{{ optional($billing->issued_at)->format('M d, Y') ?? '-' }}</strong><br>
                        Due Date: <strong>{{ optional($billing->due_date)->format('M d, Y') ?? '-' }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-grid">
            <tr>
                <td style="width: 48%;">
                    <div class="section-title">Billed To</div>
                    <div class="info-text">
                        <strong>{{ $billing->client?->company_name ?? 'Client Name Unavailable' }}</strong><br>
                        {{ $billing->client?->user?->email }}
                    </div>
                </td>
                <td class="spacer"></td>
                <td style="width: 48%;">
                    <div class="section-title">Project Details</div>
                    <div class="info-text" style="margin-bottom: 8px;">
                        <strong>Project:</strong> {{ $billing->project?->title ?? 'N/A' }}
                    </div>
                    <div class="section-title" style="margin-top: 12px;">Payment Status</div>
                    <div>
                        <span class="badge {{ $badgeClass }}">
                            {{ strtoupper($billing->status) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Description</th>
                    <th style="width: 30%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-title">{{ $billing->title }}</div>
                        <div class="item-notes">{{ $billing->notes ?? 'No additional notes provided.' }}</div>
                    </td>
                    <td class="text-right" style="vertical-align: middle;">
                        {{ $billing->currency }} {{ number_format((float) $billing->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 20%; color: #6b7280; font-weight: 600;">Total Due</td>
                <td style="width: 20%;" class="text-right">
                    <strong>{{ $billing->currency }} {{ number_format((float) $billing->amount, 2) }}</strong>
                </td>
            </tr>
            <tr class="total-row">
                <td></td>
                <td>Amount</td>
                <td class="text-right">{{ $billing->currency }} {{ number_format((float) $billing->amount, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            Thank you for your business. <br>
            This invoice was securely generated by the Koneva Billing System.
        </div>
    </div>
</body>

</html>
