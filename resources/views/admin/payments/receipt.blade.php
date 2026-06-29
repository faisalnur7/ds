<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Payment Receipt') }} - {{ $record->receipt_no }}</title>
        <style>
            :root {
                color-scheme: light;
            }

            @page {
                margin: 14mm;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                background: #e9eef6;
                color: #0f172a;
                padding: 24px 0 40px;
            }

            .page {
                max-width: 920px;
                margin: 0 auto;
                padding: 0 24px;
            }

            .toolbar {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-bottom: 16px;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 999px;
                padding: 12px 18px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                text-decoration: none;
            }

            .button-print {
                background: #0f172a;
                color: #fff;
            }

            .button-back {
                background: #cbd5e1;
                color: #0f172a;
            }

            .sheet {
                background: #fff;
                border-radius: 24px;
                padding: 40px 44px;
                box-shadow: 0 24px 70px rgba(15, 23, 42, 0.16);
            }

            .header {
                display: flex;
                justify-content: space-between;
                gap: 24px;
                border-bottom: 2px solid #e2e8f0;
                padding-bottom: 20px;
                margin-bottom: 24px;
            }

            .brand {
                font-size: 26px;
                font-weight: 800;
                margin: 0;
            }

            .subtle {
                color: #64748b;
                margin: 6px 0 0;
            }

            .receipt-no {
                text-align: right;
            }

            .receipt-no p {
                margin: 0;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
            }

            .card {
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                padding: 16px 18px;
                background: #f8fafc;
            }

            .label {
                display: block;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: #64748b;
                margin-bottom: 8px;
            }

            .value {
                font-size: 18px;
                font-weight: 700;
                color: #0f172a;
                margin: 0;
            }

            .footer {
                margin-top: 28px;
                display: flex;
                justify-content: space-between;
                gap: 16px;
                align-items: end;
                border-top: 2px solid #e2e8f0;
                padding-top: 18px;
            }

            .signature {
                min-width: 220px;
                text-align: center;
                padding-top: 26px;
                border-top: 1px solid #94a3b8;
                color: #475569;
            }

            .meta {
                color: #64748b;
                font-size: 14px;
            }

            @media print {
                @page {
                    margin: 16mm;
                }

                body {
                    background: #fff;
                    padding: 0;
                }

                .page {
                    margin: 0;
                    padding: 0;
                    max-width: none;
                }

                .toolbar {
                    display: none;
                }

                .sheet {
                    border-radius: 18px;
                    border: 1px solid #dbe4f0;
                    box-shadow: none;
                    padding: 18mm;
                }
            }
        </style>
        <script>
            window.addEventListener('load', () => {
                window.print();
            });
        </script>
    </head>
    <body>
        <div class="page">
            <div class="toolbar">
                <a class="button button-back" href="{{ route('admin.payments.index') }}">{{ __('Back') }}</a>
                <a class="button button-back" href="{{ route('admin.payments.receipt.download', $record) }}">{{ __('Download CSV') }}</a>
                <button class="button button-print" type="button" onclick="window.print()">{{ __('Print receipt') }}</button>
            </div>

            <section class="sheet">
                <div class="header">
                    <div>
                        <p class="brand">{{ config('app.name', 'Darus Salam CCIMS') }}</p>
                        <p class="subtle">{{ __('Payment Receipt') }}</p>
                    </div>
                    <div class="receipt-no">
                        <p class="meta">{{ __('Receipt No') }}</p>
                        <p class="value">{{ $record->receipt_no ?? '—' }}</p>
                    </div>
                </div>

                <div class="grid">
                    <div class="card">
                        <span class="label">{{ __('Member') }}</span>
                        <p class="value">{{ $record->member?->full_name ?? '—' }}</p>
                        <p class="subtle">{{ $record->member?->member_code ?? '—' }}</p>
                    </div>
                    <div class="card">
                        <span class="label">{{ __('Payment Month') }}</span>
                        <p class="value">{{ optional($record->payment_month)->format('F Y') ?? '—' }}</p>
                    </div>
                    <div class="card">
                        <span class="label">{{ __('Amount Paid') }}</span>
                        <p class="value">{{ number_format((float) $record->amount_paid, 2) }}</p>
                    </div>
                    <div class="card">
                        <span class="label">{{ __('Payment Method') }}</span>
                        <p class="value">{{ __(ucfirst($record->payment_method)) }}</p>
                    </div>
                    <div class="card">
                        <span class="label">{{ __('Transaction No') }}</span>
                        <p class="value">{{ $record->transaction_no ?? '—' }}</p>
                    </div>
                    <div class="card">
                        <span class="label">{{ __('Status') }}</span>
                        <p class="value">{{ __(ucfirst($record->status)) }}</p>
                    </div>
                    <div class="card">
                        <span class="label">{{ __('Created At') }}</span>
                        <p class="value">{{ optional($record->created_at)->format('F j, Y g:i A') ?? '—' }}</p>
                    </div>
                </div>

                <div class="footer">
                    <div>
                        <p class="meta">{{ __('Share Value') }}: {{ number_format((float) $record->share_value, 2) }}</p>
                        <p class="meta">{{ __('Share Cost') }}: {{ number_format((float) $record->share_cost, 2) }}</p>
                        <p class="meta">{{ __('Total Amount') }}: {{ number_format((float) $record->total_amount, 2) }}</p>
                    </div>
                    <div class="signature">
                        {{ __('Authorized Signature') }}
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
