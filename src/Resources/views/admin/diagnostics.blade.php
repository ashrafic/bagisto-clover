<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clover Diagnostics</title>
    <style>
        body { font-family: -apple-system, sans-serif; margin: 24px; color: #111827; }
        h1 { font-size: 20px; } h2 { font-size: 16px; margin-top: 32px; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 4px 8px; text-align: left; }
        th { background: #f9fafb; }
        .warning { color: #b91c1c; font-weight: 600; }
        pre { background: #f9fafb; border: 1px solid #e5e7eb; padding: 12px; font-size: 11px; overflow: auto; max-height: 480px; }
    </style>
</head>
<body>
    <h1>Clover Diagnostics</h1>

    <h2>Channel configuration</h2>
    <table>
        <tr><th>Setting</th><th>Value</th></tr>
        @foreach ($configuration as $setting => $value)
            <tr>
                <td>{{ $setting }}</td>
                <td class="{{ str_contains($value, 'NO') ? 'warning' : '' }}">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <h2>Checkout sessions (latest 50)</h2>
    <table>
        <tr>
            <th>ID</th><th>Session</th><th>Cart</th><th>Status</th><th>Payment</th><th>Verified via</th><th>Currency</th><th>Total</th><th>Created</th><th>Updated</th>
        </tr>
        @forelse ($checkoutSessions as $session)
            <tr>
                <td>{{ $session->id }}</td>
                <td>{{ substr($session->checkout_session_id, 0, 18) }}…</td>
                <td>{{ $session->cart_id }}</td>
                <td>{{ $session->status }}</td>
                <td>{{ $session->payment_id ? substr($session->payment_id, 0, 13).'…' : '—' }}</td>
                <td>{{ $session->verified_via ?: '—' }}</td>
                <td>{{ $session->currency_code }}</td>
                <td>{{ $session->base_grand_total }}</td>
                <td>{{ $session->created_at }}</td>
                <td>{{ $session->updated_at }}</td>
            </tr>
        @empty
            <tr><td colspan="10">No checkout sessions recorded yet.</td></tr>
        @endforelse
    </table>

    <h2>Recent Clover log entries (last 100)</h2>
    @if (count($logLines))
        <pre>{{ implode(PHP_EOL, $logLines) }}</pre>
    @else
        <p>No Clover log entries found.</p>
    @endif
</body>
</html>
