<div class="xn_letter" style="width:100%">
	<div class="letter_header" style="background-image: url(<{$xoops_url}>/modules/xnewsletter/assets/images/letter_template/xoops-bg.png); background-repeat: repeat-x; height: 80px; width:100%">
		<div style="background-image: url(<{$xoops_url}>/modules/xnewsletter/assets/images/letter_template/xoops-logo.png); background-repeat: no-repeat; height: 80px;" >&nbsp;</div>
	</div>
	<div class="letter-body" style="width: 100%;font-family: Arial,Helvetica,sans-serif; font-size: 11px; margin-top: 30px;">
		Sehr geehrte(r) <{$sex}> <{$firstname}> <{$lastname}><br>
		<br>
		<{$content}>
		<br>
	</div>
	<div>
		<ul>
			<{foreach item='attachment' from=$attachments}>
				<li><a href="<{$attachment.attachment_link}>"><{$attachment.attachment_name}></a></li>
			<{/foreach}>
		</ul>
	</div>

	<div class="letter_footer" style="width: 100%; font-family: Arial,Helvetica,sans-serif; font-size: 11px; padding-top: 50px;">
		<div style="border-top:1px solid #37449a">&nbsp;</div>
		<p style="margin-top:10px;margin-bottom:20px;">Wenn Sie eine Liste Ihrer Newsletteranmeldungen sehen wollen, dann klicken Sie bitte <a href="<{$listsubscription_link}>" target="_blank">hier</a></p>
		<p style="margin-top:10px;margin-bottom:20px;">Wenn Sie sich von allen Newsletter abmelden wollen, dann klicken Sie bitte <a href="<{$unsubscribe_link}>" target="_blank">hier</a></p>
	</div>
</div>