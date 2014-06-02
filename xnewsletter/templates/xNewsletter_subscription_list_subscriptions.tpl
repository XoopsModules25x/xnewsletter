<{include file="db:xNewsletter_header.tpl"}>
<div class="outer">
    <div>
    <{if ($subscrCount > 0)}>
        <{$smarty.const._MA_XNEWSLETTER_REGISTRATION_EXIST}>
    <{else}>
        <{$smarty.const._MA_XNEWSLETTER_REGISTRATION_NONE}>
    <{/if}>
    </div>

<{if ($showSubscrSearchForm)}>
    <{$subscrSearchForm}>
<{/if}>
<{if ($showSubscrForm)}>
    <{$subscrForm}>
<{/if}>
</div>

<{if ($subscrCount > 0)}>
    <table class='outer width100' cellspacing='1'>
        <tr>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_SEX}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}></th>
            <th class='center'><{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EXIST}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_CREATED}></th>
            <th class='center width10'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
    <{foreach item='subscr' from=$subscrs}>
        <tr>
            <td class='center'><{$subscr.subscr_email}></td>
            <td class='center'><{$subscr.subscr_sex}></td>
            <td class='center'><{$subscr.subscr_firstname}></td>
            <td class='center'><{$subscr.subscr_lastname}></td>
            <td class='center'>
            <{if ($subscr.catsubscrs|count > 0)}>
                <ul>
            <{foreach item='catsubscr' from=$subscr.catsubscrs}>
                    <li>
                        <{$catsubscr.cat.cat_name}>
                    <{if ($catsubscr.catsubscr_quited)}>
                        <{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_QUITED}>
                    <{/if}>
                    </li>
            <{/foreach}>
                </ul>
            <{else}>
                <{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EXIST_NONE}>
            <{/if}>
            </td>
            <td class='center'><{$subscr.subscr_created_timestamp}></td>
            <td class='center'>
                <a href='?op=edit_subscription&subscr_id=<{$subscr.subscr_id}>'>
                    <img
                        src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                        alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>'
                        title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>' />
                </a>
                <a href='?op=delete_subscription&subscr_id=<{$subscr.subscr_id}>&subscr_email=<{$subscr.subscr_email}>'>
                    <img
                        src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png'
                        alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_DELETE}>'
                        title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_DELETE}>' />
                </a>
            </td>
        </tr>
    <{if ($subscr.subscr_activated == 0)}>
        <tr>
            <td colspan='7'>
                <{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_UNFINISHED|replace:'%link':'?op=resend_subscription&subscr_id=%subscr_id'|replace:'%subscr_id':$subscr.subscr_id}>
            </td>
        </tr>
    <{/if}>
    <{/foreach}>
    </table>
<{/if}>
<{include file="db:xNewsletter_footer.tpl"}>