<x-email.layout :title="'Appointment Canceled'">
    <h2 style="margin-top:0; color:#2c3e50; font-size:1.5em;">Hello {{ $appointment->patient->name }},</h2>

    <p style="font-size:1.1em; color:#34495e;">Sorry, Your appointment has been <strong style="color:#ff0000;">Canceled</strong>.</p>

    <table cellpadding="0" cellspacing="0" role="presentation"
        style="width:100%; margin:20px 0; border-collapse:separate;">
        <tr>
            <td style="padding:12px 16px; background:#f9fafb; border-radius:8px 8px 0 0; border:1px solid #e1e4e8;">
                <strong style="color:#2c3e50;">Date:</strong>
                <span style="margin-left:8px; color:#555;">{{ $appointment->slot->date }}</span>
            </td>
        </tr>
        <tr>
            <td
                style="padding:12px 16px; background:#f9fafb; border-radius:0; border-left:1px solid #e1e4e8; border-right:1px solid #e1e4e8; border-bottom:1px solid #e1e4e8;">
                <strong style="color:#2c3e50;">Time:</strong>
                <span style="margin-left:8px; color:#555;">{{ $appointment->slot->start_time }} -
                    {{ $appointment->slot->end_time }}</span>
            </td>
        </tr>
        <tr>
            <td
                style="padding:12px 16px; background:#f9fafb; border-radius:0 0 8px 8px; border:1px solid #e1e4e8; border-top:none;">
                <strong style="color:#2c3e50;">Notes:</strong>
                <span style="margin-left:8px; color:#555;">{{ $appointment->notes ?? 'No notes' }}</span>
            </td>
        </tr>
    </table>

    <div style="text-align:center; margin-top:28px;">
        <a href="{{ url('/appointments/cancel/' . $appointment->id) }}"
            style="display:inline-block; padding:14px 28px; background:#585858; color:#fff; text-decoration:none; border-radius:8px; font-weight:bold; font-size:1.1em; box-shadow:0 2px 8px rgba(231,76,60,0.08); transition:background 0.2s;">
            Apply for a New appointment
        </a>
    </div>

    <p style="margin-top:28px; color:#7f8c8d; font-size:1em;">Thanks,<br><span style="color:#2c3e50;">Dr. Abdullah
            Fathallah</span></p>
</x-email.layout>