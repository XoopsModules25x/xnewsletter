<div class="letter-body" style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
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
<div class="letter_footer" style="width: 100%; font-family: Arial, Helvetica, sans-serif; font-size: 11px;padding-top:50px">
Wenn Sie eine Liste Ihrer Newsletteranmeldungen sehen wollen, dann klicken Sie bitte <a href="<{$listsubscription_link}>" target="_blank">hier</a><br>
Wenn Sie sich von allen Newslettern abmelden wollen, dann klicken Sie bitte <a href="<{$unsubscribe_link}>" target="_blank">hier</a>
</div>
