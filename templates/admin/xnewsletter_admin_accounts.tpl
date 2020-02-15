<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>

<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>

<{if $account_check}>
    <table class='table table-bordered' >
        <thead>
        <tr class='head'>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_CHECK}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_CHECK_RESULT}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_CHECK_INFO}></th>
        </tr>
        </thead>
        <tbody>
            <{foreach item=check from=$checks}>
                <tr class="<{cycle values='odd, even'}>">
                    <td class='center'><{$check.check}></td>
                    <td class='center'><{$check.result_img}><{$check.result}></td>
                    <td class='center'>
                        <{$check.info}>
                        <{if $check.created}>
                            <{$check.created}>
                        <{/if}>
                    </td>
                </tr>
            <{/foreach}>
        </tbody>
    </table>
<{/if}>
<{if $accounts_list}>
        <table class='table table-bordered' >
            <thead>
                <tr class='head'>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_TYPE}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_NAME}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_YOURNAME}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_YOURMAIL}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_DEFAULT}></th>
                    <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
                </tr>
            </thead>
            <{if $accountsCount}>
                <tbody>
                    <{foreach item=account from=$accounts_list}>
                        <tr class="<{cycle values='odd, even'}>">
                            <td class='center'><{$account.id}></td>
                            <td class='center'><{$account.type_text}></td>
                            <td class='center'><{$account.name}></td>
                            <td class='center'><{$account.yourname}></td>
                            <td class='center'><{$account.yourmail}></td>
                            <td class='center'><{$account.default_text}></td>
                            <td class='center  width10'>
                                <a href='<{$xnewsletter_url}>/admin/accounts.php?op=edit_account&amp;accounts_id=<{$account.id}>' title='<{$smarty.const._EDIT}>'>
                                    <img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='accounts'></a>
                                <a href='<{$xnewsletter_url}>/admin/accounts.php?op=delete_account&amp;accounts_id=<{$account.id}>' title='<{$smarty.const._DELETE}>'>
                                    <img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='accounts'></a>
                                <{if $account.show_check}>
                                    <a href='<{$xnewsletter_url}>/admin/accounts.php?op=check_account&amp;accounts_id=<{$account.id}>' title='<{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK}>'>
                                        <img src='<{$xnewsletter_icons_url}>/xn_check.png' alt='<{$smarty.const._AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK}>'></a>
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
