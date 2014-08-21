<{include file="db:xnewsletter_header.tpl"}>

<table class='outer width100' cellspacing='1'>
    <tr>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_CATS}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_SENT}></th>
    <{if ($showAdminColumns)}>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_TEMPLATE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_ATTACHMENT}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_EMAIL_TEST}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS}></th>
    <{/if}>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach item='letter' from=$letters}>
    <tr class="<{cycle values = 'even,odd'}>">
        <td><{$letter.letter_id}></td>
        <td><{$letter.letter_title}></td>
        <td>
        <{foreach item='letter_cat' from=$letter.letter_cats}>
            <{$letter_cat.cat_name}>
            <br />
        <{/foreach}>
        </td>
        <td><{$letter.letter_sent_formatted}></td>
    <{if ($showAdminColumns)}>
        <td><{$letter.letter_template}></td>
        <td><{$letter.attachmentCount}></td>
        <td><{$letter.letter_email_test}></td>
        <td>
        <{foreach item='protocol' from=$letter.protocols}>
            <a href='protocol.php?op=list_protocols&letter_id=<{$protocol.protocol_letter_id}>'><{$protocol.protocol_status}></a>
            <br />
        <{/foreach}>
        </td>
    <{/if}>
        <td>
        <{if ($letter.userPermissions.edit)}>
            <a href="?op=edit_letter&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png"
                    alt="<{$smarty.const._EDIT}>"
                    title="<{$smarty.const._EDIT}>" />
            </a>
        <{/if}>
        <{if ($letter.userPermissions.delete)}>
            <a href="?op=delete_letter&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png"
                    alt="<{$smarty.const._DELETE}>"
                    title="<{$smarty.const._DELETE}>" />
            </a>
        <{/if}>
        <{if ($letter.userPermissions.create)}>
            <a href="?op=copy_letter&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_copy.png"
                    alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_COPYNEW}>"
                    title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_COPYNEW}>" />
            </a>
        <{/if}>
        <{if ($letter.userPermissions.edit || $letter.userPermissions.delete || $letter.userPermissions.create)}>
            <br />
        <{/if}>
        <{if ($letter.userPermissions.send)}>
            <a href="sendletter.php?op=send_test&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_sendtest.png"
                    alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST}>"
                    title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST}>" />
            </a>
            <a href="sendletter.php?op=send_letter&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_send.png"
                    alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SEND}>"
                    title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SEND}>" />
            </a>
            <a href="sendletter.php?op=resend_letter&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_resend.png"
                    alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_RESEND}>"
                    title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_RESEND}>" />
            </a>
            <br />
        <{/if}>
        <{if ($letter.userPermissions.read)}>
            <a href="?op=show_preview&letter_id=<{$letter.letter_id}>">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_preview.png"
                    alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>"
                    title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>" />
            </a>
            <a href="<{$smarty.const.XNEWSLETTER_URL}>/print.php?letter_id=<{$letter.letter_id}>" target="_BLANK">
                <img
                    src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/printer.png"
                    alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PRINT}>"
                    title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PRINT}>" />
            </a>
        <{/if}>
        </td>
    </tr>
<{/foreach}>
</table>
<br />
<div><{$pagenav}></div>


<{include file="db:xnewsletter_footer.tpl"}>
