<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $categories_list}>
    <table class='table table-bordered' >
        <thead>
            <tr class='head'>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_ID}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_NAME}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_INFO}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_ADMIN}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_CREATE}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_LIST}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_READ}></th>
                <{if $use_mailinglist}>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_MAILINGLIST}></th>
                <{/if}>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}></th>
                <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
            </tr>
        </thead>
        <{if $categories_count}>
            <tbody>
                <{foreach item=category from=$categories_list}>
                    <tr class="<{cycle values='odd, even'}>">
                        <td class='center'><{$category.id}></td>
                        <td class='center'><{$category.name}></td>
                        <td class='center'><{$category.info}></td>
                        <td class='center'><{$category.gperms_admin}></td>
                        <td class='center'><{$category.gperms_create}></td>
                        <td class='center'><{$category.gperms_list}></td>
                        <td class='center'><{$category.gperms_read}></td>
                        <{if $use_mailinglist}>
                            <td class='center'><{$category.mailinglist_text}></td>
                        <{/if}>
                        <td class='center'><{$category.created}></td>
                        <td class='center'><{$category.submitter}></td>
                        <td class='center  width10'>
                            <a href='<{$xnewsletter_url}>/admin/cat.php?op=edit_cat&amp;cat_id=<{$category.id}>' title='<{$smarty.const._EDIT}>'>
                                <img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._EDIT}>'></a>
                            <a href='<{$xnewsletter_url}>/admin/cat.php?op=delete_cat&amp;cat_id=<{$category.id}>' title='<{$smarty.const._DELETE}>'>
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
