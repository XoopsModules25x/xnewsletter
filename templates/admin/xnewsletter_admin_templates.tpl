<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $templates_list}>

        <table class='table table-bordered' >
            <thead>
                <tr class='head'>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_TITLE}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_DESCRIPTION}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_TYPE}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_ONLINE}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
                </tr>
            </thead>
            <{if $templatesCount}>
                <tbody>
                    <{foreach item=template from=$templates_list}>
                        <tr class="<{cycle values='odd, even'}>">
                            <td class='center'><{$template.id}></td>
                            <td class='center'>
                                <{if $template.template_err}>
                                    <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/alert.png' alt='<{$letter.template_err_text}>' title='<{$letter.template_err_text}>'>
                                <{/if}>
                                <{$template.title}>
                            </td>
                            <td class='center'><{$template.description}></td>
                            <td class='center'><{$template.type_text}></td>
                            <td class='center'><{$template.submitter}></td>
                            <td class='center'><{$template.created}></td>
                            <td class='center'>
                                <{if $template.online == 1}>
                                    <a href='<{$xnewsletter_url}>/admin/template.php?op=state_template&amp;template_id=<{$template.id}>&amp;template_online=0' title='<{$smarty.const._EDIT}>'>
                                        <img src='<{$xnewsletter_icons_url}>/xn_ok.png' alt='<{$smarty.const._EDIT}>'></a>
                                <{else}>
                                    <a href='<{$xnewsletter_url}>/admin/template.php?op=state_template&amp;template_id=<{$template.id}>&amp;template_online=1' title='<{$smarty.const._EDIT}>'>
                                        <img src='<{$xnewsletter_icons_url}>/xn_failed.png' alt='<{$smarty.const._EDIT}>'></a>
                                <{/if}>
                            </td>
                            <td class='center  width10'>
                                <a href='<{$xnewsletter_url}>/admin/template.php?op=edit_template&amp;template_id=<{$template.id}>' title='<{$smarty.const._EDIT}>'>
                                    <img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._EDIT}>'></a>
                                <a href='<{$xnewsletter_url}>/admin/template.php?op=delete_template&amp;template_id=<{$template.id}>' title='<{$smarty.const._DELETE}>'>
                                    <img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._DELETE}>'></a>
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
