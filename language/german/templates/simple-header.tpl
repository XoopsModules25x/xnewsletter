<div class="letter-body" style="width:100%;background-color:#fff;">
    <div class="letter_header" style="width:100%;background-color:#5ca82e;">
        <img alt="<{$xoops_sitename}> - <{$xoops_slogan}>" src="<{$xoops_upload_url}>/xnewsletter/newsletter_header.png" style="align: center; width: 150px;" title="<{$xoops_sitename}> - <{$xoops_slogan}>" />
        <{$xoops_sitename}> - <{$xoops_slogan}>
    </div>
    <div style="width:100%;font-family: Arial,Helvetica,sans-serif; font-size: 14px; margin-top: 5px;">
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
  
    <div class="letter_footer" style="width:100%;font-family:Arial,Helvetica,sans-serif;font-size:11px;padding-top:50px;">
        <div style="width:100%;border-top:1px solid #5ca82e">&nbsp;</div>
        <p style="margin-top:10px;margin-bottom:20px;">Wenn Sie eine Liste Ihrer Newsletteranmeldungen sehen wollen, dann klicken Sie bitte <a href="<{$listsubscription_link}>" target="_blank">hier</a></p>
        <p style="margin-top:10px;margin-bottom:20px;">Wenn Sie sich von allen Newsletter abmelden wollen, dann klicken Sie bitte <a href="<{$unsubscribe_link}>" target="_blank">hier</a></p>
    </div>
</div>