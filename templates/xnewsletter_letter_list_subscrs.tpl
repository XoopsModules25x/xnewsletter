<{include file="db:xnewsletter_header.tpl"}>

<{$searchSubscriberForm}>
<br>
<!-- cats table -->
<table class='xnewsletter-table'>
    <tr>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_NAME}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_INFO}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
    <{$cats_content}>
    <{foreach item='cat' from=$cats}>
        <tr class="<{cycle values = 'even,odd'}>">
            <td><{$cat.cat_id}></td>
            <td><{$cat.cat_name}></td>
            <td><{$cat.cat_info}></td>
            <td><{$cat.catsubscrCount}></td>
            <td>
                <a href='?op=list_subscrs&cat_id=<{$cat.cat_id}>'>
                    <img
                            src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_details.png'
                            alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'
                            title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'>
                </a>
            </td>
        </tr>
    <{/foreach}>
</table>
<br>
<{if ($cat_id > 0)}>
    <h2><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID}>: <{$cat_name}></h2>
    <table class='outer width100' cellspacing='1'>
        <tr>
            <th>&nbsp;</th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_SEX}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}></th>
            <th><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
            <{if ($permissionChangeOthersSubscriptions)}>
                <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
            <{/if}>
        </tr>
        <{foreach item='subscr' from=$subscrs}>
            <tr>
                <td><{$subscr.counter}></td>
                <td><{$subscr.subscr_sex}></td>
                <td><{$subscr.subscr_lastname}></td>
                <td><{$subscr.subscr_firstname}></td>
                <td><{$subscr.subscr_email}></td>
                <{if ($permissionChangeOthersSubscriptions)}>
                    <td>
                        <a href='subscription.php?op=edit_subscription&subscr_id=<{$subscr.subscr_id}>'>
                            <img
                                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                                    alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>'
                                    title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>'>
                        </a>
                    </td>
                <{/if}>
            </tr>
        <{/foreach}>
    </table>
    <br>
<{/if}>

<{include file="db:xnewsletter_footer.tpl"}>
