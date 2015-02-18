<script language='JavaScript'>
    function toggle(source){
        checkboxes = document.getElementsByName('task_ids[]');
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
        checkboxes = document.getElementsByName('task_ids[]');
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
            <{$filter_task_letter_ids_select}>
            <br />
            <{$smarty.const._AM_XNEWSLETTER_TASK_CREATED_FILTER_FROM}>
            <{$filter_task_created_from_datetime}>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_TASK_CREATED_FILTER_TO}>
            <{$filter_task_created_to_datetime}>
            <br />
            <{$smarty.const._AM_XNEWSLETTER_TASK_STARTTIME_FILTER_FROM}>
            <{$filter_task_starttime_from_datetime}>
            &nbsp;
            <{$smarty.const._AM_XNEWSLETTER_TASK_STARTTIME_FILTER_TO}>
            <{$filter_task_starttime_to_datetime}>
            <br />
            <input type='submit' class='formButton' id='filter_submit' title='<{$smarty.const._SEARCH}>' name='filter_submit' value='<{$smarty.const._SEARCH}>'>
            <input type='hidden' id='op' name='op' value='list_tasks' >
            <input type='hidden' id='filter_op' name='apply_filter' value='1' >
        </form>
        </td>
    </tr>
</table>

<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='7'><{$smarty.const._AM_XNEWSLETTER_THEREARE_TASK|replace:'%s':$taskCount}></td>
    </tr>
<form id='form_action' onsubmit='return check(this);' enctype='multipart/form-data' method='post' action='' name='form_action'>
    <tr>
        <th class='center'><input type='checkbox' name='togglers[]' title='<{$smarty.const._ALL}>' onClick='toggle(this);'></th>
        <th><{$smarty.const._AM_XNEWSLETTER_TASK_LETTER_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_TASK_STARTTIME}></th>
        <th>
            <{$smarty.const._AM_XNEWSLETTER_TASK_SUBMITTER}>
            <br />
            <{$smarty.const._AM_XNEWSLETTER_TASK_CREATED}>
        </th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>

<{assign var = "class" value = 'odd'}>
<{assign var = "prev_starttime" value = 0}>

<{foreach from=$tasks item='task'}>

    <{if $task.task_starttime <> $prev_starttime}>
        <{if $class == 'odd'}>
            <{assign var = "class" value = 'even'}>
        <{else}>
            <{assign var = "class" value = 'odd'}>
        <{/if}>
    <{/if}>
    <{assign var="prev_starttime" value = $task.task_starttime}>
    <tr class="<{$class}>">
        <td class='center'><input type='checkbox' name='task_ids[]' value='<{$task.task_id}>'></td>
        <td><{$task.task_letter_title}></td>
        <td>
            <{$task.task_subscr_email}>
            <a href='subscr.php?op=edit_subscr&subscr_id=<{$task.task_subscr_id}>'>
                <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>' />
            </a>
        </td>
        <td>
        <{if ($task.task_starttime_expired)}>
        <span style="color:red;"><{$task.task_starttime_formatted}></span>
        <{else}>
        <{$task.task_starttime_formatted}>
        <{/if}>
        </td>
        <td>
            <{$task.task_submitter_uname}>
            <br />
            <{$task.task_created_formatted}>
        </td>
        <td class='center' nowrap='nowrap'>
            <a href='?op=delete_task&task_id=<{$task.task_id}>'><img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>' /></a>
        </td>
    </tr>
<{/foreach}>
    <tr>
        <td colspan='7'>
            <select id='actions_action' name='actions_action' value='delete' size='1'>
                <option value='delete'><{$smarty.const._DELETE}></option>
                <option value='execute'><{$smarty.const._AM_XNEWSLETTER_TASK_ACTIONS_EXECUTE}></option>
            </select>
            <input type='submit' class='formButton' id='actions_submit' title='<{$smarty.const._AM_XNEWSLETTER_ACTIONS_EXEC}>' name='actions_submit' value='<{$smarty.const._AM_XNEWSLETTER_ACTIONS_EXEC}>'>
        </td>
    </tr>
    <input type='hidden' id='actions_op' name='op' value='apply_actions' >
</form>
</table>
<{$tasks_pagenav}>
