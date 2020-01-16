<{include file="db:xnewsletter_header.tpl"}>

<{if (($actionProts_ok|@count gt 0) || ($actionProts_warning|@count gt 0) || ($actionProts_error|@count gt 0))}>
    <{foreach item='actionProt_ok' from=$actionProts_ok}>
        <div style="vertical-align: middle; line-height: normal;">
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/on.png' alt='<{$smarty.const._OK}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_OK}>'>
            <{$actionProt_ok}>
        </div>
    <{/foreach}>
    <{foreach item='actionProt_warning' from=$actionProts_warning}>
        <div style="vertical-align: middle; line-height: normal;">
             <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/alert.png' alt='<{$smarty.const._WARNING}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_OK}>'>
            <{$actionProt_warning}>
        </div>
    <{/foreach}>
    <{foreach item='actionProt_error' from=$actionProts_error}>
        <div style="vertical-align: middle; line-height: normal;">
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/off.png' alt='<{$smarty.const._ERROR}>' title='<{$smarty.const._ERROR}>"'>
            <{$actionProt_error}>
        </div>
    <{/foreach}>
    <br>
<{/if}>

<div>
    <{if ($showSubscrSearchForm)}>
        <{$subscrSearchForm}>
    <{/if}>
    <{if ($showSubscrForm)}>
        <{$subscrForm}>
    <{/if}>
</div>

<br>

<{if ($subscrCount > 0)}>
    <table class="xnewsletter-table">
        <tr>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_SEX}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}></th>
            <th><{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EXIST}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
        <{foreach item='subscr' from=$subscrs}>
            <tr>
                <td><{$subscr.subscr_email}></td>
                <td><{$subscr.subscr_sex}></td>
                <td><{$subscr.subscr_firstname}></td>
                <td><{$subscr.subscr_lastname}></td>
                <td>
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
                <td><{$subscr.subscr_created_formatted}></td>
                <td>
                    <a href='?op=edit_subscription&subscr_id=<{$subscr.subscr_id}>'>
                        <img
                                src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                                alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>'
                                title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>'>
                    </a>
                    <a href='?op=delete_subscription&subscr_id=<{$subscr.subscr_id}>&subscr_email=<{$subscr.subscr_email}>'>
                        <img
                                src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png'
                                alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_DELETE}>'
                                title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_DELETE}>'>
                    </a>
                </td>
            </tr>
        <{/foreach}>
    </table>
<{/if}>

<{include file="db:xnewsletter_footer.tpl"}>
