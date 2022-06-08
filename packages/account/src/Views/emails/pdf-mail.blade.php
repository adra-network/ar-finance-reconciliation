
<div>
@isset($accountUserName)
	Dear {{ $accountUserName }},
@else
	Hi,
@endisset
</div>
<br>
<div>Please find attached your Account Receivable balance as of {{ $statementDate }}. Your statement total is ${{ $statementTotal }}. If you have any questions regarding your attached statement, please send an email to Employee.AR@adra.org and someone will assist you as soon as possible.</div>
<br>
<div><b>NOTE:</b> If you have any concerns this may be a spam or phishing email, please reach out to our IT department.</div>
<br>
<div>
Kind regards,
<br>
Finance Department
</div>
<div style="margin-top:5px;">
    <img src="{{ asset('logos/adra-mail-logo.png') }}" alt="">
</div>
