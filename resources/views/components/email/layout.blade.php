<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f7f7f7;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <x-email.header :logo="$logo ?? null" :title="$title ?? null" :subtitle="$subtitle ?? null" />

    <!-- Main content -->
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                    style="background:#ffffff;border-radius:8px;padding:24px; margin-top:12px;">
                    <tr>
                        <td
                            style="font-family:Arial, Helvetica, sans-serif; color:#333; font-size:14px; line-height:1.6;">
                            {{ $slot }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <x-email.footer :contact="$contact ?? null" :address="$address ?? null" />
</body>

</html>