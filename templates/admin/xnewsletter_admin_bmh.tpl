<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $form_filter}>
	<{$form_filter}>
<{/if}>
<table class='table table-bordered'>
	<thead>
		<tr class="head">
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_ID}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_RULE_NO}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_RULE_CAT}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_BOUNCETYPE}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_REMOVE}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_EMAIL}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
			<th class="center"><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
		</tr>
	</thead>
	<tbody>
		<{if $bhmCount}>
			<{foreach item=bmh_item from=$bmh_list}>
				<tr class="<{cycle values='odd, even'}>">
					<td class='center'><{$bmh_item.id}></td>
					<td class="center"><{$bmh_item.rule_no}></td>
					<td class="center"><{$bmh_item.rule_cat}></td>
					<td class="center"><{$bmh_item.bouncetype}></td>
					<td class="center"><{$bmh_item.remove}></td>
					<td class="center"><{$bmh_item.email}></td>
					<td class="center"><{$bmh_item.measure_text}></td>
					<td class="center"><{$bmh_item.created}></td>
					<td class="center  width5">
						<a href='?op=handle_bmh&bmh_id=<{$bmh_item.id}>&bmh_measure=<{$smarty.const._XNEWSLETTER_BMH_MEASURE_VAL_NOTHING}>&filter=<{$filter}>'>
							<img src='<{$xnewsletter_icons_url}>/xn_nothing.png' alt='<{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE_NOTHING}>' title='<{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE_NOTHING}>'></a>
						<a href='?op=handle_bmh&bmh_id=<{$bmh_item.id}>&bmh_measure=<{$smarty.const._XNEWSLETTER_BMH_MEASURE_VAL_QUIT}>&filter=<{$filter}>'>
							<img src='<{$xnewsletter_icons_url}>/xn_catsubscr_temp.png' alt='<{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE_QUIT}>' title='<{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE_QUIT}>'></a>
						<a href='?op=bmh_delsubscr&bmh_id=<{$bmh_item.id}>&filter=<{$filter}>'>
							<img src='<{$xnewsletter_icons_url}>/xn_quit.png' alt='<{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE_DELETE}>' title='<{$smarty.const._AM_XNEWSLETTER_BMH_MEASURE_DELETE}>'></a>
						<a href='?op=edit_bmh&bmh_id=<{$bmh_item.id}>'>
							<img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._AM_XNEWSLETTER_BMH_EDIT}>' title='<{$smarty.const._AM_XNEWSLETTER_BMH_EDIT}>' width='16px'></a>
						<a href='?op=delete_bmh&bmh_id=<{$bmh_item.id}>'>
							<img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._AM_XNEWSLETTER_BMH_DELETE}>' title='<{$smarty.const._AM_XNEWSLETTER_BMH_DELETE}>' width='16px'></a>
					</td>
				</tr>
			<{/foreach}>
		<{else}>
			<tr>
				<td class='even' colspan='10'><{$show_none}></td>
			</tr>
		<{/if}>
	</tbody>

</table>
<div class="clear">&nbsp;</div>
<{if $pagenav}>
	<div class="xo-pagenav floatright"><{$pagenav}></div>
	<div class="clear spacer"></div>
<{/if}>
<br>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>
