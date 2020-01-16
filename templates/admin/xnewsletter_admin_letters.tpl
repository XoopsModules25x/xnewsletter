<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}><{$form}><{/if}>
<{if $error}><div class='errorMsg'><strong><{$error}></strong></div><{/if}>
<{if $preview}><{$preview}><{/if}>
<{if $letters_list}>
        <table class='table table-bordered' >
            <thead>
                <tr class='head'>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_CATS}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}><br><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_SENDER}><br><{$smarty.const._AM_XNEWSLETTER_LETTER_SENT}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_TEMPLATE}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_SIZE}><br><{$smarty.const._AM_XNEWSLETTER_LETTER_ATTACHMENT}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_ACCOUNT}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_EMAIL_TEST}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS}></th>
                    <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
                </tr>
            </thead>
            <{if $letterCount}>
                <tbody>
                    <{foreach item=letter from=$letters_list}>
                        <tr class="<{cycle values='odd, even'}>">
                            <td class='center'><{$letter.id}></td>
                            <td class='center'><{$letter.title}></td>
                            <td class='center'><{$letter.cats_text}></td>
                            <td class='center'><{$letter.submitter}><br><{$letter.created}></td>
                            <td class='center'><{$letter.sender}><br><{$letter.sent}></td>
                            <td class='center'>
                                <{if $letter.template_err}>
                                    <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/alert.png' alt='<{$letter.template_err_text}>' title='<{$letter.template_err_text}>'>
                                <{/if}>
                                <{$letter.template_title}>
                            </td>
                            <td class='center'><{$letter.size_attachments}></td>
                            <td class='center'><{$letter.letter_account}></td>
                            <td class='center'><{$letter.email_test}></td>
                            <td class='center'><a href=' protocol.php?op=list_letter&letter_id=<{$letter.protocol_letter_id}>'><{$letter.protocol_status}></a></td>
                            <td class='center  width10'>
                                <a href='?op=edit_letter&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>' style='padding:1px'></a>
                                <a href='?op=clone_letter&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_clone.png' alt='<{$smarty.const._CLONE}>' title='<{$smarty.const._CLONE}>' style='padding:1px'></a>
                                <a href='?op=delete_letter&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>'  style='padding:1px'></a>
                                <{if !$letter.template_err}>
                                    <a href='sendletter.php?op=send_test&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_sendtest.png' alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST}>' title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST}>' style='padding:1px'></a>
                                    <a href='sendletter.php?op=send_letter&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_send.png' alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SEND}>' title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_SEND}>' style='padding:1px'></a>
                                    <a href='sendletter.php?op=resend_letter&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_resend.png' alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_RESEND}>' title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_RESEND}>' style='padding:1px'></a>
                                    <a href='?op=show_preview&letter_id=<{$letter.id}>'><img src='<{$xnewsletter_icons_url}>/xn_preview.png' alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>' title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>' style='padding:1px'></a>
                                    <a href='<{$xnewsletter_url}>/print.php?letter_id=<{$letter.id}>' target='_BLANK' ><img src='<{$xnewsletter_icons_url}>/printer.png' alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PRINT}>' title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PRINT}>' style='padding:1px'></a>
                                <{/if}>
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
