<script language='JavaScript'>
    function toggle(source){
        checkboxes = document.getElementsByName('subscr_ids[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
        togglers = document.getElementsByName('togglers[]');
        for (var i = 0, n = togglers.length; i < n; i++) {
            togglers[i].checked = source.checked;
        }
    }
</script>
<script language='JavaScript'>
    function check(source){
        checkboxes = document.getElementsByName('subscr_ids[]');
        for (var i = 0,  n= checkboxes.length; i < n; i++) {
            if (checkboxes[i].checked) return true;
        }
        return false;
    }
</script>

<table class='outer width100' cellspacing='1'>
    <tr class='odd'>
        <td>
        <form id='form_filter' enctype='multipart/form-data' method='post' action='' name='form_filter'>
            <{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}>
            <input type='text' id='filter_subscr_firstname' maxlength='50' size='15' title='' name='filter_subscr_firstname' value='<{$filter_subscr_firstname}>'>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}>
            <input type='text' id='filter_subscr_lastname' maxlength='50' size='15' title='' name='filter_subscr_lastname' value='<{$filter_subscr_lastname}>'>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}>
            <input type='text' id='filter_subscr_email' maxlength='255' size='40' title='' name='filter_subscr_email' value='<{$filter_subscr_email}>'>
            <{$smarty.const._SEARCH}>&nbsp;
            <{$filter_subscr_criteria_select}>
<!--
            <select id='filter_subscr' title='<{$smarty.const._SEARCH}>' name='filter_subscr' size='1'>
                <option value='=' <{if ($filter_subscr == "=")}> selected='selected'<{/if}>><{$smarty.const._AM_XNEWSLETTER_SEARCH_EQUAL}></option>
                <option value='LIKE' <{if ($filter_subscr == "LIKE")}> selected='selected'<{/if}>><{$smarty.const._AM_XNEWSLETTER_SEARCH_CONTAINS}></option>
            </select>
-->
            <input type='submit' id='filter_submit' class='formButton' title='<{$smarty.const._SEARCH}>' value='<{$smarty.const._SEARCH}>' name='filter_submit'>
            <input type='hidden' id='op' name='op' value='list_subscrs' >
            <input type='hidden' id='filter_op' name='apply_filter' value='1' >
        </form>
        </td>
    </tr>
</table>

<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='9'><{$smarty.const._AM_XNEWSLETTER_THEREARE_SUBSCR|replace:'%s':$subsrCount}></td>
    </tr>
<form id='form_action' onsubmit='return check(this);' enctype='multipart/form-data' method='post' action='' name='form_action'>
    <tr>
        <th class='center'><input type='checkbox' name='togglers[]' title='<{$smarty.const._ALL}>' onClick='toggle(this);'></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_SEX}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_UID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_CREATED}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach from=$subscrs item='subscr'}>
    <tr class="<{cycle values='odd, even'}>">
        <td class='center'><input type='checkbox' name='subscr_ids[]' value='<{$subscr.subscr_id}>'></td>
        <td><{$subscr.subscr_id}></td>
        <td><{$subscr.subscr_sex}>&nbsp;</td>
        <td><{$subscr.subscr_firstname}>&nbsp;</td>
        <td><{$subscr.subscr_lastname}>&nbsp;</td>
        <td><{$subscr.subscr_email}>&nbsp;</td>
        <td>
        <{if ($subscr.subscr_activated)}>
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_ok.png' alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED}>' />
        <{else}>
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_failed.png' alt='<{$smarty.const._AM_XNEWSLETTER_SUBSCRWAIT}>' title='<{$smarty.const._AM_XNEWSLETTER_SUBSCRWAIT}>' />
        <{/if}>
            &nbsp;<{$subscr.subscr_uname}>&nbsp;[<{$subscr.subscr_ip}>]
        </td>
        <td>
            <{$subscr.subscr_created_formatted}>
        </td>
        <td class='center' nowrap='nowrap'>
            <a href='?op=edit_subscr&subscr_id=<{$subscr.subscr_id}>'><img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>' /></a>
            &nbsp;
            <a href='?op=delete_subscr&subscr_id=<{$subscr.subscr_id}>'><img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>' /></a>
            &nbsp;
            <a href='?op=show_catsubscr&subscr_id=<{$subscr.subscr_id}>&filter_subscr=<{$filter_subscr}>&filter_subscr_firstname=<{$filter_subscr_firstname}>&filter_subscr_lastname=<{$filter_subscr_lastname}>&filter_subscr_email=<{$filter_subscr_email}>&prev_op=<{$op}>'>
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_details.png' alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>' title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>' />
            </a>
        </td>
    </tr>
<{/foreach}>
    <tr>
        <td colspan='5'>
            <select id='actions_action' name='actions_action' value='delete' size='1'>
                <option value='delete'><{$smarty.const._DELETE}></option>
                <option value='activate'><{$smarty.const._AM_XNEWSLETTER_ACTIONS_ACTIVATE}></option>
                <option value='unactivate'><{$smarty.const._AM_XNEWSLETTER_ACTIONS_UNACTIVATE}></option>
            </select>
            <input type='submit' class='formButton' id='actions_submit' title='<{$smarty.const._AM_XNEWSLETTER_ACTIONS_EXEC}>' name='actions_submit' value='<{$smarty.const._AM_XNEWSLETTER_ACTIONS_EXEC}>'>
        </td>
    </tr>
    <input type='hidden' id='actions_op' name='op' value='apply_actions'>
</form>
</table>
<{$subscrs_pagenav}>
