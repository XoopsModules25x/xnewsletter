<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='7'><{$smarty.const._AM_XNEWSLETTER_THEREARE_ATTACHMENT|replace:'%s':$attachmentCount}></td>
    </tr>
    <tr>
        <th><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_LETTER_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_NAME}></th>
        <th>
            <{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_SIZE}>
            <br />
            <{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_TYPE}>
        </th>
        <th><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_SUBMITTER}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_CREATED}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach from=$attachments item='attachment'}>
    <tr class="<{cycle values='odd, even'}>">
        <td><{$attachment.attachment_id}></td>
        <td>
            <{$attachment.attachment_letter_title}>
            <a href='letter.php?op=edit_letter&letter_id=<{$attachment.attachment_letter_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>
                    title='<{$smarty.const._EDIT}>
                    style='padding:1px' />
            </a>


        </td>
        <td><{$attachment.attachment_name}>&nbsp;</td>
        <td>
            <span title='<{$attachment.attachment_size}> B'><{$attachment.attachment_size1024}></span>
            <br />
            <{$attachment.attachment_type}>
        </td>
        <td><{$attachment.attachment_submitter_uname}>&nbsp;</td>
        <td><{$attachment.attachment_created_formatted}></td>
        <td class='center' nowrap='nowrap'>
            <a href='?op=edit_attachment&attachment_id=<{$attachment.attachment_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>'
                    title='<{$smarty.const._EDIT}>'
                    style='padding:1px' /></a>
            <a href='?op=delete_attachment&attachment_id=<{$attachment.attachment_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png'
                    alt='<{$smarty.const._DELETE}>'
                    title='<{$smarty.const._DELETE}>'
                    style='padding:1px' /></a>
        </td>
    </tr>
<{/foreach}>
</table>
<{$attachments_pagenav}>
