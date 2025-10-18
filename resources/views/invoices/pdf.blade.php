<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .company-info {
            color: #666;
            font-size: 11px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-meta {
            color: #666;
            font-size: 11px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .info-box h3 {
            font-size: 12px;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead {
            background: #667eea;
            color: white;
        }
        thead th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        tbody td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-left: auto;
            width: 300px;
            margin-bottom: 30px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            padding: 8px 12px;
            border: none;
        }
        .totals .total-row {
            background: #667eea;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .notes {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="logo">📚 Bookshare</div>
                <div class="company-info">
                    <p><strong>{{ config('invoice.company.name', 'Bookshare Platform') }}</strong></p>
                    <p>{{ config('invoice.company.address', 'La petite ariana') }}</p>
                    <p>{{ config('invoice.company.city', 'Ariana, La petite ariana') }}</p>
                    <p>Email: {{ config('invoice.company.email', 'contact@bookshare.com') }}</p>
                    <p>Phone: {{ config('invoice.company.phone', '+216 29135995') }}</p>
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                    <p><strong>Date:</strong> {{ $invoice->invoice_date->format('F d, Y') }}</p>
                    <p><strong>Due Date:</strong> {{ $invoice->due_date->format('F d, Y') }}</p>
                    <p><span class="status-badge status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></p>
                </div>
            </div>
        </div>

        <!-- Bill To / Payment Info -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Bill To</h3>
                    <p><strong>{{ $invoice->user->name }}</strong></p>
                    <p>{{ $invoice->user->email }}</p>
                    @if($invoice->user->phone)
                    <p>{{ $invoice->user->phone }}</p>
                    @endif
                </div>
            </div>
            <div class="info-right">
                <div class="info-box">
                    <h3>Payment Information</h3>
                    <p><strong>Payment ID:</strong> {{ $invoice->subscriptionPayment->payment_id }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst($invoice->subscriptionPayment->payment_method) }}</p>
                    <p><strong>Payment Status:</strong> {{ ucfirst($invoice->subscriptionPayment->payment_status) }}</p>
                    <p><strong>Transaction Date:</strong> {{ $invoice->subscriptionPayment->created_at->format('F d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Duration</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $invoice->subscription->name }} Subscription</strong><br>
                        <small>{{ $invoice->subscription->description }}</small>
                    </td>
                    <td>{{ $invoice->subscription->duration_days }} days</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-{{ $invoice->currency }} {{ number_format($invoice->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <strong>Notes:</strong><br>
            {{ $invoice->notes }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>For any questions regarding this invoice, please contact us at billing@bookshare.com</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} Bookshare. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
