<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}><{$form}><{/if}>
<{if $error}><div class='errorMsg'><strong><{$error}></strong></div><{/if}>
<{if $preview}><{$preview}><{/if}>
<{if $attachments_list}>
    <table class='table table-bordered' >
        <thead>
        <tr class='head'>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_LETTER_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_NAME}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_SIZE}><br><{$smarty.const._AM_XNEWSLETTER_ATTACHMENT_TYPE}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
            <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
        </thead>
        <{if $attachmentCount}>
        <tbody>
        <{foreach item=attachment from=$attachments_list}>
            <tr class="<{cycle values='odd, even'}>">
                <td class='center'><{$attachment.id}></td>
                <td class='center'><{$attachment.letter_title}></td>
                <td class='center'><{$attachment.name}></td>
                <td class='center'><{$attachment.attsize}><br><{$attachment.type}></td>
                <td class='center'><{$attachment.submitter}></td>
                <td class='center'><{$attachment.created}></td>
                <td class='center  width10'>
                    <a href='?op=edit_attachment&attachment_id=<{$attachment.id}>'><img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>' style='padding:1px'></a>
                    <a href='?op=delete_attachment&attachment_id=<{$attachment.id}>'><img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>'  style='padding:1px'></a>
                </td>
            </tr>
            <{/foreach}>
        </tbody>
        <{/if}>
    </table>
    <div class='clear'>&nbsp;</div>
    <{if $pagenav}>
    <div class='xo-pagenav floatright'><{$pagenav}></div>
    <div class='clear spacer'></div>
    <{/if}>
    <{/if}>
<br>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>

