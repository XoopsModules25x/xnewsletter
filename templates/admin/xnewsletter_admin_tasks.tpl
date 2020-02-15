<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $tasks_list}>
        <table class='table table-bordered' >
            <thead>
                <tr class='head'>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TASK_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TASK_LETTER_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TASK_SUBSCR_ID}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_TASK_STARTTIME}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
                    <th class='center'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
                </tr>
            </thead>
            <{if $taskCounts}>
                <tbody id="tasks-list">
                    <{foreach item=task from=$tasks_list}>
                        <tr class="<{cycle values='odd, even'}>" id="corder_<{$task.id}>">
                            <td class='center'><{$task.id}></td>
                            <td class='center'><{$task.letter_title}></td>
                            <td class='center'><{$task.subscr_email}></td>
                            <td class='center'><{$task.starttime}></td>
                            <td class='center'><{$task.submitter}></td>
                            <td class='center'><{$task.created}></td>
                            <td class='center  width10'>
                                <a href='<{$xnewsletter_url}>/admin/tasks.php?op=delete_task&amp;task_id=<{$task.id}>' title='<{$smarty.const._DELETE}>'>
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
