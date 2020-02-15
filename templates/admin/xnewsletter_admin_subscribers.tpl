<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $subscribers_list}>
        <table class='table table-bordered' >
            <thead>
                <tr class='head'>
                    <th class='center'><input type='checkbox' title='" . _ALL . "'onClick='toggle(this);'></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_SEX}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_UID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
                    <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
                </tr>
            </thead>
            <{if $subscribers_count}>
                <tbody>
                    <{$form_filter}>
                    <form id='form_action' onsubmit='return check(this);' enctype='multipart/form-data' method='post' action='subscr.php' name='form_action'>
                        <{foreach item=subscriber from=$subscribers_list}>
                            <tr class="<{cycle values='odd, even'}>">
                                <td class="center"><input type='checkbox' name='subscr_ids[]' value='<{$subscriber.id}>'></td>
                                <td class='center'><{$subscriber.id}></td>
                                <td class='center'><{$subscriber.sex}></td>
                                <td class='center'><{$subscriber.firstname}></td>
                                <td class='center'><{$subscriber.lastname}></td>
                                <td class='center'><{$subscriber.email}></td>
                                <td class='center'><{$subscriber.username}></td>
                                <td class='center'><{$subscriber.activated_img}><{$subscriber.created_ip}></td>
                                <td class='center  width10'>
                                    <a href='<{$xnewsletter_url}>/admin/subscr.php?op=edit_subscr&amp;subscr_id=<{$subscriber.id}>&amp;start=<{$start}>&amp;limit=<{$limit}>' title='<{$smarty.const._EDIT}>'>
                                        <img src='<{xoModuleIcons16 edit.png}>' alt='subscribers'></a>
                                    <a href='<{$xnewsletter_url}>/admin/subscr.php?op=delete_subscr&amp;subscr_id=<{$subscriber.id}>&amp;start=<{$start}>&amp;limit=<{$limit}>' title='<{$smarty.const._DELETE}>'>
                                        <img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='subscribers'></a>
                                    <a href='?op=show_catsubscr&subscr_id=<{$subscriber.id}>&filter_subscr=<{$filter_subscr}>&filter_subscr_firstname=<{$filter_subscr_firstname}>&filter_subscr_lastname=<{$filter_subscr_lastname}>&filter_subscr_email=<{$filter_subscr_email}>&apply_filter=<{$op}>' >
                                        <img src='<{$xnewsletter_icons_url}>/xn_details.png' alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>' title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'>
                                    </a>
                                </td>
                            </tr>
                        <{/foreach}>
                        <tr>
                            <td colspan='9'>
                                <select id='actions_action' name='actions_action' size='1'>
                                    <option value='delete'><{$smarty.const._DELETE}></option>
                                    <option value='activate'><{$smarty.const._AM_XNEWSLETTER_ACTIONS_ACTIVATE}></option>
                                    <option value='unactivate'><{$smarty.const._AM_XNEWSLETTER_ACTIONS_UNACTIVATE}></option>
                                </select>
                                <input id='actions_submit' class='formButton' type='submit' title='<{$smarty.const._AM_XNEWSLETTER_ACTIONS_EXEC}>' value='<{$smarty.const._AM_XNEWSLETTER_ACTIONS_EXEC}>' name='actions_submit'>
                            </td>
                        </tr>
                        <input id='actions_op' type='hidden' value='apply_actions' name='op'>
                    </form>
                </tbody>
            <{/if}>
        </table>
	<div class='clear'>&nbsp;</div>
	<{if $pagenav}>
		<div class='xo-pagenav floatright'><{$pagenav}></div>
		<div class='clear spacer'></div>
	<{/if}>
<{/if}>

<{if $show_catsubscr}>
    <table class='outer' cellspacing='1'>
        <tr>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_ID}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_LETTERLIST}></th>
        </tr>
        <tr class="<{cycle values='odd, even'}>">
            <td><{$show_catsubscr.id}></td>
            <td><{$show_catsubscr.email}></td>
            <td><{$show_catsubscr.cats}></td>
        </tr>
    </table>
<{/if}>


<br>
<script language='JavaScript'>
    function toggle(source)
    {
        checkboxes = document.getElementsByName('subscr_ids[]');
        for (var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<script language='JavaScript'>
    function check(source)
    {
        checkboxes = document.getElementsByName('subscr_ids[]');
        for (var i=0, n=checkboxes.length;i<n;i++) {
            if (checkboxes[i].checked) return true;
        }

        return false;
    }
</script>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>
