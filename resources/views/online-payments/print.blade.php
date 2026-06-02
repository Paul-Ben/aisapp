<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->reference }}</title>
    <style>
        /* dompdf: page margin is also set via setPaper(); inline rule omitted to avoid Blade parse issues with the @ character */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
            font-size: 12pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #003399;
            padding-bottom: 14px;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0 0 4px 0;
            color: #003399;
            font-size: 20pt;
        }
        .header p {
            margin: 2px 0;
            color: #555;
            font-size: 10pt;
        }
        .receipt-title {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #777;
            font-size: 11pt;
            margin-bottom: 18px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            color: #888;
            text-transform: uppercase;
            font-size: 9pt;
            letter-spacing: 1px;
            display: block;
        }
        .info-value {
            font-weight: bold;
            font-size: 12pt;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items th {
            background: #003399;
            color: #fff;
            text-align: left;
            padding: 8px 10px;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .items th.text-end, .items td.text-end {
            text-align: right;
        }
        .items td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .items tfoot th {
            background: #f4f7f6;
            color: #003399;
            font-size: 12pt;
        }
        .footer-note {
            margin-top: 24px;
            padding: 12px;
            background: #f4f7f6;
            border-radius: 4px;
            font-size: 10pt;
        }
        .verified {
            display: inline-block;
            padding: 3px 8px;
            background: #28a745;
            color: #fff;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }
        .sign-off {
            margin-top: 30px;
            text-align: center;
            color: #888;
            font-size: 10pt;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $school['name'] ?? 'Alven International Schools' }}</h1>
        @if (!empty($school['address']))
            <p>{{ $school['address'] }}</p>
        @endif
        @if (!empty($school['phone']) || !empty($school['email']))
            <p>
                @if (!empty($school['phone']))Tel: {{ $school['phone'] }}@endif
                @if (!empty($school['phone']) && !empty($school['email'])) &nbsp;|&nbsp; @endif
                @if (!empty($school['email'])){{ $school['email'] }}@endif
            </p>
        @endif
    </div>

    <div class="receipt-title">Official Payment Receipt</div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="info-label">Receipt No.</span>
                <span class="info-value">{{ $payment->reference }}</span>
            </td>
            <td style="width: 50%; text-align: right;">
                <span class="info-label">Date Paid</span>
                <span class="info-value">{{ $payment->paid_at?->format('F d, Y') ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="info-label">Student</span>
                <span class="info-value">{{ $payment->student->full_name ?? '—' }}</span>
                <div style="font-size: 10pt; color: #555;">Adm. No. {{ $payment->student->admission_number ?? '—' }}</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <span class="info-label">Class</span>
                <span class="info-value">{{ $payment->student->class->name ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="info-label">Academic Session</span>
                <span class="info-value">{{ $payment->academicYear->session ?? '—' }} &middot; {{ ucfirst($payment->term) }} Term</span>
            </td>
            <td style="width: 50%; text-align: right;">
                <span class="info-label">Payment Method</span>
                <span class="info-value">Paystack @if ($payment->gateway_channel) &middot; {{ ucfirst($payment->gateway_channel) }}@endif</span>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->feeItem->name ?? 'School Fee' }}</td>
                <td class="text-end">&#8358;{{ number_format((float) $payment->amount_paid, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Total Paid</th>
                <th class="text-end">&#8358;{{ number_format((float) $payment->amount_paid, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: middle;">
                    <span class="info-label">Paystack Reference</span>
                    <code>{{ $payment->gateway_reference }}</code>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <span class="verified">VERIFIED BY PAYSTACK</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="sign-off">
        Thank you for your payment. Please retain this receipt for your records.
    </div>

</body>
</html>
