<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $heading ?? $appName }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f5f7;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f5f7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
                    <tr>
                        <td style="padding:28px 32px 0;">
                            <p style="margin:0;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;">{{ $appName }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 32px 0;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;color:#111827;font-weight:600;">{{ $heading ?? '' }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px 8px;font-size:15px;line-height:1.6;color:#374151;">
                            @foreach (($lines ?? []) as $line)
                                <p style="margin:0 0 12px;">{{ $line }}</p>
                            @endforeach
                        </td>
                    </tr>
                    @if (! empty($actionUrl))
                        <tr>
                            <td style="padding:8px 32px 28px;">
                                <a href="{{ $actionUrl }}" style="display:inline-block;background-color:#111827;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 22px;border-radius:8px;">{{ $actionLabel ?? 'Open' }}</a>
                            </td>
                        </tr>
                    @else
                        <tr><td style="padding:0 32px 28px;"></td></tr>
                    @endif
                    <tr>
                        <td style="padding:18px 32px;border-top:1px solid #f3f4f6;font-size:12px;line-height:1.5;color:#9ca3af;">
                            {{ $footer ?? $appName }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
