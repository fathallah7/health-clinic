<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f4f6fb;padding:32px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(44,62,80,0.08);padding:0 32px;">
                <tr>
                    <td style="padding:24px 0 24px 0; vertical-align:middle; width:72px;">
                        <img src="https://png.pngtree.com/png-vector/20190115/ourmid/pngtree-vector-doctor-icon-png-image_321081.jpg"
                            alt="App Logo"
                            style="height:56px; border-radius:50%; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    </td>
                    <td style="text-align:left; font-family:Arial, Helvetica, sans-serif; padding-left:24px;">
                        <div style="font-size:20px; font-weight:700; color:#2d3748; letter-spacing:0.5px;">
                            {{ $title ?? config('app.name') }}
                        </div>
                        @if(!empty($subtitle))
                            <div style="font-size:14px; color:#718096; margin-top:4px;">
                                {{ $subtitle }}
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>