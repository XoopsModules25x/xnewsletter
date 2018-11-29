<{include file="db:xnewsletter_header.tpl"}>
<div class="outer">
    <table cellpadding="0" cellspacing="0" class="item" width="100%">
        <tr class="itemHead">
            <th class="center"><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_ID}></th>
            <th class="center"><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
            <th class="center" colspan="2"><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_STATUS}></th>
            <th class="center"><{$smarty.const._AM_XNEWSLETTER_PROTOCOL_CREATED}></th>
        </tr>
    <{foreach item='protocol' from=$protocols}>
        <tr class = "<{cycle values = 'even,odd'}>">
            <td class="center"><{$protocol.protocol_id}></td>
            <td class="center">
            <{if ($protocol.subscr)}>
                <{$protocol.subscr_email}>
            <{else}>
                <{$smarty.const._AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL}>
            <{/if}>

            </td>
            <td class="right">
            <{if ($protocol.protocol_success)}>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_ok.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_OK}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_OK}>' />&nbsp;&nbsp;
            <{else}>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_failed.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_FAILED}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_FAILED}>' />&nbsp;&nbsp;
            <{/if}>
            </td>
            <td class="left"><{$protocol.protocol_status}></td>
            <td class="center"><{$protocol.protocol_created_timestamp}></td>
        </tr>
    <{/foreach}>
    </table>
</div>
<{include file="db:xnewsletter_footer.tpl"}>
