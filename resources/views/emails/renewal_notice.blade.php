@php
    $who = '';
    if (!empty($deceasedNames)) {
        $who = implode(', ', $deceasedNames);
    } elseif (!empty($slotReference)) {
        $who = $slotReference;
    } else {
        $who = 'your reserved slot';
    }
@endphp



<p>Dear {{ $clientName }},</p>

<p>We hope this message finds you well.</p>

<p>
This is a gentle reminder that the burial slot for <strong>{{ $who }}</strong>
is approaching its renewal date <strong>{{ $expirationDateMmDdYyyy }}</strong>.
To ensure continued reservation and maintenance of the plot, we kindly ask that you
settle the renewal payment at the San Juan City Cemetery Office at your earliest convenience.
</p>

<p>
Please visit our office during business hours to complete the necessary arrangements.
Should you have any questions or require assistance, feel free to contact us at
<strong>cgsjcemetery@gmail.com</strong>.
</p>

<p>Thank you for your attention to this matter.</p>

<p>
Sincerely,<br>
San Juan City Cemetery Office<br>
City Government of San Juan
</p>
