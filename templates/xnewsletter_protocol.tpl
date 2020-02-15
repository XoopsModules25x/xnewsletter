<{include file="db:xnewsletter_header.tpl"}>

<table class='xnewsletter-table' cellspacing='1'>
    <tr class="itemHead">
        <th><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_STATUS}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
    </tr>
    <{foreach item='protocol' from=$protocols}>
        <tr class="<{cycle values = 'even,odd'}>">
            <td><{$protocol.protocol_id}></td>
            <td>
                <{if ($protocol.subscr.subscr_id > 0)}>
                    <{$protocol.subscr.subscr_email}>
                <{/if}>

            </td>
            <td>
                <{if ($protocol.protocol_success)}>
                    <img
                            src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_ok.png'
                            alt='<{$smarty.const._AM_XNEWSLETTER_OK}>'
                            title='<{$smarty.const._AM_XNEWSLETTER_OK}>'>
                    &nbsp;&nbsp;
                <{else}>
                    <img
                            src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_failed.png'
                            alt='<{$smarty.const._AM_XNEWSLETTER_FAILED}>'
                            title='<{$smarty.const._AM_XNEWSLETTER_FAILED}>'>
                    &nbsp;&nbsp;
                <{/if}>
                <{$protocol.protocol_status}>
            </td>
            <td><{$protocol.protocol_created_formatted}></td>
        </tr>
    <{/foreach}>
</table>
<div><{$pagenav}></div>
<{include file="db:xnewsletter_footer.tpl"}>
