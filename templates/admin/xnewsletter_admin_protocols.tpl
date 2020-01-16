<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>

<{if $list_letter}>
    <h3><{$letter_title}></h3>
    <table class='table table-bordered' >
        <thead>
            <tr class='head'>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_ID}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_STATUS}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_SUCCESS}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBMITTER}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
                <th class='center'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
            </tr>
        </thead>
        <tbody>
            <{if $protocols_count}>
                <{foreach item=protocol from=$protocols_list2}>
                    <tr class="<{cycle values='odd, even'}>">
                        <td class='center'><{$protocol.id}></td>
                        <td class='left'><{$protocol.subscriber}></td>
                        <td class='center'><{$protocol.status}></td>
                        <td class='center'><{$protocol.success_text}></td>
                        <td class='center'><{$protocol.submitter}></td>
                        <td class='center'><{$protocol.created}></td>
                        <td class='center  width10'>
                            <a href='?op=delete_protocol&protocol_id=<{$protocol.id}>' title='<{$smarty.const._DELETE}>'>
                                <img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._DELETE}>'></a>
                        </td>
                    </tr>
                <{/foreach}>
            <{/if}>
        </tbody>
    </table>
	<div class='clear'>&nbsp;</div>
	<{if $pagenav}>
		<div class='xo-pagenav floatright'><{$pagenav}></div>
		<div class='clear spacer'></div>
	<{/if}>
<{/if}>

<{if $list_protocols}>
    <table class='table table-bordered' >
        <thead>
        <tr class='head'>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
        </thead>
        <tbody>
        <tr class="<{cycle values='odd, even'}>">
            <td class='center'>-</td>
            <td class='left'><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_MISC}></td>
            <td class='center'><a href='?op=list_letter&letter_id=0'><{$protocol_status}></a></td>
            <td class='center'><{$protocol_created_formatted}></td>
            <td class='center  width10'>
                <a href='?op=list_letter&letter_id=0' title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'>
                    <img src='<{$xnewsletter_icons_url}>/xn_details.png' alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'></a>
            </td>
        </tr>
        <{if $letters_count}>
            <{foreach item=protocol from=$protocols_list}>
            <tr class="<{cycle values='odd, even'}>">
                <td class='center'><{$protocol.letter_id}></td>
                <td class='left'><{$protocol.letter_title}></td>
                <td class='center'><a href='?op=list_letter&letter_id=<{$protocol.letter_id}>'><{$protocol.status}></a></td>
                <td class='center'><{$protocol.created}></td>
                <td class='center  width10'>
                    <a href='?op=list_letter&letter_id=<{$protocol.letter_id}>' title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'>
                        <img src='<{$xnewsletter_icons_url}>/xn_details.png' alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'></a>
                </td>
            </tr>
            <{/foreach}>
            <{/if}>
        </tbody>
    </table>
    <div class='clear'>&nbsp;</div>
    <{if $pagenav}>
    <div class='xo-pagenav floatright'><{$pagenav}></div>
    <div class='clear spacer'></div>
    <{/if}>
    <{/if}>
<br>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>
