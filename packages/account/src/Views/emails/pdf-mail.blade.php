
@isset($accountUserName)
	Hi,
@else 
	Dear {{ $accountUserName }},
@endisset
<br>
<div>Please find attached your Account Receivable balance as of {{ $statementDate }}.</div>
<div><b>NOTE:</b> This is not a spam or phishing email. If you have any concerns please reach out to Richard O'Ffill or Nataly Alfaro.</div>
<br>
<br>
<div>
Kind regards,
<br>
Nataly Alfaro
</div>
<div style="margin-top:5px;">
    <img src="{{ asset('logos/adra-mail-logo.png') }}" alt="">
</div>
