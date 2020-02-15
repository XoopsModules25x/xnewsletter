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
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID}></th>
                    <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
                </tr>
            </thead>
            <{if $categories_count}>
                <tbody>
                    <{foreach item=category from=$categories_list}>
                        <tr class="<{cycle values='odd, even'}>">
                            <td class='center'><{$category.id}></td>
                            <td class='center'><a href='?op=list_cat&cat_id=<{$category.id}>'><{$category.name}></a></td>
                            <td class='center'><{$category.info}></td>
                            <td class='center'><{$category.subscrCount}></td>
                            <td class='center  width10'>
                                <a href='?op=list_cat&cat_id=<{$category.id}>'>
                                    <img src='<{$xnewsletter_icons_url}>/xn_details.png' alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>' title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'></a>
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
<{if $catsubscribers_list}>
    <table class='table table-bordered' >
        <thead>
        <tr class='head'>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_CATID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_QUITED}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
            <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
        </thead>
        <{if $catsubscr_count}>
            <tbody>
            <{foreach item=catsubscr from=$catsubscribers_list}>
                <tr class="<{cycle values='odd, even'}>">
                    <td class='center'><{$catsubscr.id}></td>
                    <td class='center'><{$catsubscr.cat_name}></td>
                    <td class='center'><{$catsubscr.subscr_email}></td>
                    <td class='center'><{$catsubscr.quited_text}></td>
                    <td class='center'><{$catsubscr.submitter}></td>
                    <td class='center'><{$catsubscr.created}></td>
                    <td class='center  width10'>
                        <a href='?op=edit_catsubscr&catsubscr_id=<{$catsubscr.id}>&cat_id=<{$catsubscr.catid}>'>
                            <img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>'></a>&nbsp;
                        <a href='?op=delete_catsubscr&catsubscr_id=<{$catsubscr.id}>&cat_id=<{$catsubscr.catid}>&cat_name=<{$catsubscr.cat_name}>&subscr_email=<{$catsubscr.subscr_email}>&subscr_id=<{$catsubscr.subscrid}>'>
                            <img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>'></a>
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
