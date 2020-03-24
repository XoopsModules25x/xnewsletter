<div class="letter-body" style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
    Dear <{$sex}> <{$firstname}> <{$lastname}><br>
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
<div class="letter_footer" style="width: 100%; font-family: Arial, Helvetica, sans-serif; font-size: 11px;padding-top:50px;">
    If you want to list and check your newsletter subscriptions then please click <a href="<{$listsubscription_link}>" target="_blank">here</a><br>
	If you want to unsubscribe from all subscriptions please click <a href="<{$unsubscribe_link}>" target="_blank">here</a>
</div>
