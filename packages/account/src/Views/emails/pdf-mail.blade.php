
<div>
@isset($accountUserName)
	Dear {{ $accountUserName }},
@else
	Hi,
@endisset
</div>
<br>
<div>Please find attached your Account Receivable balance as of {{ $statementDate }}. Your statement total is ${{ $statementTotal }}.</div>
<br>
<div><b>NOTE:</b> This is not a spam or phishing email. If you have any concerns please reach out to Richard O'Ffill or Nataly Alfaro.</div>
<br>
<div>
Kind regards,
<br>
Nataly Alfaro
</div>
<div style="margin-top:5px;">
    <img src="{{ asset('logos/adra-mail-logo.png') }}" alt="">
</div>
