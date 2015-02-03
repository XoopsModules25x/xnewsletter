<script language='JavaScript'>
    function toggle(source){
        checkboxes = document.getElementsByName('protocol_ids[]');
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
        checkboxes = document.getElementsByName('protocol_ids[]');
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
            <{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}>
            <{$filter_protocol_letter_ids_select}>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_PROTOCOL_STATUS}>
            <{$filter_protocol_success_radio}>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_PROTOCOL_CREATED_FILTER_FROM}>
            <{$filter_protocol_created_from_datetime}>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_PROTOCOL_CREATED_FILTER_TO}>
            <{$filter_protocol_created_to_datetime}>
            &nbsp;
            <input id='filter_submit' class='formButton' type='submit' title='<{$smarty.const._SEARCH}>' value='<{$smarty.const._SEARCH}>' name='filter_submit'>
            <input type='hidden' id='op' name='op' value='list_protocols' >
            <input type='hidden' id='filter_op' name='apply_filter' value='1' >
        </form>
        </td>
    </tr>
</table>

<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='7'><{$smarty.const._AM_XNEWSLETTER_THEREARE_PROTOCOL|replace:'%s':$protocolCount}></td>
    </tr>

<form id='form_action' onsubmit='return check(this);' enctype='multipart/form-data' method='post' action='' name='form_action'>
    <tr>
        <th class='center'><input type='checkbox' name='togglers[]' title='<{$smarty.const._ALL}>' onClick='toggle(this);'></th>
        <th><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_STATUS}></th>
        <th>
            <{$smarty.const._AM_XNEWSLETTER_PROTOCOL_SUBMITTER}>
            <br />
            <{$smarty.const._AM_XNEWSLETTER_PROTOCOL_CREATED}></th>
        </th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach from=$protocols item='protocol'}>
    <tr class="<{cycle values='odd, even'}>">
        <td class='center'><input type='checkbox' name='protocol_ids[]' value='<{$protocol.protocol_id}>'></td>
        <td><{$protocol.protocol_id}></td>
        <td><{$protocol.protocol_letter_title}></td>
        <td>
        <{if ($protocol.protocol_subscriber_id != 0)}>
            <{$protocol.protocol_subscriber_email}>
            <a href='subscr.php?op=edit_subscr&subscr_id=<{$protocol.protocol_subscriber_id}>'>
                <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>' />
            </a>
        <{else}>
            &nbsp;
        <{/if}>
        <td>
        <{if ($protocol.protocol_success)}>
            <{$smarty.const.XNEWSLETTER_IMG_OK}>
        <{else}>
            <{$smarty.const.XNEWSLETTER_IMG_FAILED}>
        <{/if}>
            <{$protocol.protocol_status}>
        </td>
        <td>
            <{$protocol.protocol_submitter_uname}>
            <br />
            <{$protocol.protocol_created_formatted}>
        </td>
        <td class='center' nowrap='nowrap'>
            <a href='?op=delete_protocol&protocol_id=<{$protocol.protocol_id}>'><img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>' /></a>
        </td>
    </tr>
<{/foreach}>
    <tr>
        <td colspan='7'>
            <input type='hidden' id='actions_action' name='actions_action' value='delete' >
            <input id='actions_submit' class='formButton' type='submit' title='<{$smarty.const._DELETE}>' value='<{$smarty.const._DELETE}>' name='actions_submit'>
        </td>
    </tr>
    <input type='hidden' id='actions_op' name='op' value='apply_actions' >
</form>
</table>
<{$protocols_pagenav}>
