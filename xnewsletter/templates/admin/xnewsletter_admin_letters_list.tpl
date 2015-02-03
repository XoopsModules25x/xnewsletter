<script language='JavaScript'>
    function toggle(source){
        checkboxes = document.getElementsByName('letter_ids[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
        togglers = document.getElementsByName('togglers[]');
        for (var i = 0, n = togglers.length; i < n; i++) {
            togglers[i].checked = source.checked;
        }
    }
</script>
<script language='JavaScript'>
    function check(source){
        checkboxes = document.getElementsByName('letter_ids[]');
        for (var i = 0,  n= checkboxes.length; i < n; i++) {
            if (checkboxes[i].checked) return true;
        }
        return false;
    }
</script>

<form action="letter.php" method="post" id="letterform">
<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='11'><{$smarty.const._AM_XNEWSLETTER_THEREARE_LETTER|replace:'%s':$letterCount}></td>
    </tr>
    <tr>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_CATS}></th>
        <th style='white-space: nowrap'><{$smarty.const._AM_XNEWSLETTER_LETTER_SUBMITTER}><br /><{$smarty.const._AM_XNEWSLETTER_LETTER_CREATED}></th>
        <th style='white-space: nowrap'><{$smarty.const._AM_XNEWSLETTER_LETTER_SENDER}><br /><{$smarty.const._AM_XNEWSLETTER_LETTER_SENT}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_TEMPLATE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_ATTACHMENT}><br /><{$smarty.const._AM_XNEWSLETTER_LETTER_SIZE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_ACCOUNT}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_EMAIL_TEST}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach from=$letters item='letter'}>
    <tr class="<{cycle values='odd, even'}>">
        <td><{$letter.letter_id}></td>
        <td><{$letter.letter_title}></td>
        <td style='white-space: nowrap'>
        <{foreach from=$letter.letter_cats item='cat'}>
            <{$cat.cat_name}>
            <a href='cat.php?op=edit_cat&cat_id=<{$cat.cat_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>
                    title='<{$smarty.const._EDIT}>
                    style='padding:1px' />
            </a>
        <{/foreach}>
        </td>
        <td>
            <{$letter.letter_submitter_uname}>&nbsp;
            <br />
            <{$letter.letter_created_formatted}>&nbsp;
        </td>
        <td>
            <{$letter.letter_sender_uname}>&nbsp;
            <br />
            <{$letter.letter_sent_formatted}>&nbsp;
        </td>
        <td>
        <{if ($letter.letter_template.template_id)}>
            db:<{$letter.letter_template.template_title}>
            <a href='template.php?op=edit_template&template_id=<{$letter.letter_template.template_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>'
                    title='<{$smarty.const._EDIT}>'
                    style='padding:1px' /></a>
        <{else}>
            file:<{$letter.letter_template.template_title}>
        <{/if}>
        </td>
        <td style='white-space: nowrap'>
        <{if ($letter.letter_attachments)}>
            <ul>
            <{foreach from=$letter.letter_attachments item='attachment'}>
                <li><span title='<{$attachment.attachment_type}> <{$attachment.attachment_size1024}>'><{$attachment.attachment_name}></span></li>
            <{/foreach}>
            </ul>
            <{$smarty.const._AM_XNEWSLETTER_LETTER_ATTACHMENT_TOTALSIZE}>: <span title='<{$letter.letter_attachments_size}> Bytes'><{$letter.letter_attachments_size1024}></span>
            <br />
        <{/if}>
            <{$smarty.const._AM_XNEWSLETTER_LETTER_EMAIL_SIZE}>: <span title='<{$letter.letter_size}> Bytes (<{$smarty.const._AM_XNEWSLETTER_LETTER_EMAIL_SIZE_DESC}>)'><{$letter.letter_size1024}></span>
        </td>
        <td><{$letter.letter_account.accounts_name}>&nbsp;</td>
        <td><{$letter.letter_email_test}>&nbsp;</td>
        <td>
        <{foreach from=$letter.letter_protocols item='protocol'}>
            <a href='protocol.php?op=list_letter&letter_id=<{$letter.letter_id}>'><{$protocol.protocol_status}></a>
            <br />
        <{/foreach}>
        </td>
        <td class='center'>
            <a href='?op=edit_letter&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>'
                    title='<{$smarty.const._EDIT}>'
                    style='padding:1px' /></a>
            <a href='?op=clone_letter&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_clone.png'
                    alt='<{$smarty.const._CLONE}>'
                    title='<{$smarty.const._CLONE}>'
                    style='padding:1px' /></a>
            <a href='?op=delete_letter&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png'
                    alt='<{$smarty.const._DELETE}>'
                    title='<{$smarty.const._DELETE}>'
                    style='padding:1px' /></a>
            <br />
            <a href='sendletter.php?op=send_test&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_sendtest.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST}>'
                    style='padding:1px' /></a>
            <a href='sendletter.php?op=send_letter&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_send.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SEND}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SEND}>'
                    style='padding:1px' /></a>
            <a href='sendletter.php?op=resend_letter&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_resend.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_RESEND}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_RESEND}>'
                    style='padding:1px' /></a>
            <br />
            <a href='?op=show_preview&letter_id=<{$letter.letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_preview.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>'
                    style='padding:1px' /></a>
            <a href='<{$smarty.const.XNEWSLETTER_URL}>/print.php?letter_id=<{$letter.letter_id}>' target='_BLANK' >
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/printer.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PRINT}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PRINT}>'
                    style='padding:1px' /></a>
        </td>
    </tr>
<{/foreach}>
</table>
<{$letters_pagenav}>
