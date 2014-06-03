<{include file="db:xnewsletter_header.tpl"}>
<div class="outer">
    <table class='outer width100' cellspacing='1'>
        <tr>
            <th class='center width2'><{$smarty.const._AM_XNEWSLETTER_LETTER_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_TEMPLATE}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_CATS}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_ATTACHMENT}></th>
            <th class='center width10'><{$smarty.const._AM_XNEWSLETTER_LETTER_EMAIL_TEST}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS}></th>
            <th class='center' width='120px'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
    <{foreach item='letter' from=$letters}>
        <tr class="<{cycle values = 'even,odd'}>">
            <td class="center"><{$letter.letter_id}></td>
            <td class="center"><{$letter.letter_title}></td>
            <td class="center"><{$letter.letter_template}></td>
            <td class="center">
            <{foreach item='letter_cat' from=$letter.letter_cats}>
                <{$letter_cat.cat_name}>
                <br />
            <{/foreach}>
            </td>
            <td class="center"><{$letter.attachmentCount}></td>
            <td class="center"><{$letter.letter_email_test}></td>
            <td class="center">
            <{if ($letter.userPermissions.edit)}>
            <{foreach item='protocol' from=$letter.protocols}>
                <a href='protocol.php?op=list_letter&letter_id=<{$protocol.protocol_letter_id}>'><{$protocol.protocol_status}></a>
                <br />
            <{/foreach}>
            <{else}>
                -
            <{/if}>
            </td>
            <td class="center">
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
            <{if ($letter.userPermissions.read)}>
                <a href="?op=show_preview&letter_id=<{$letter.letter_id}>">
                    <img
                        src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_preview.png"
                        alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>"
                        title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>" />
                </a>
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
            <{/if}>
            </td>
        </tr>
    <{/foreach}>
    </table>
    <br />
    <div class='center'><{$pagenav}></div>
    <br />
</div>
<{include file="db:xnewsletter_footer.tpl"}>