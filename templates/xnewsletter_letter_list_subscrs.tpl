<{include file="db:xnewsletter_header.tpl"}>
<div class="outer">
    <{$searchSubscriberForm}>
    <br />
    <!-- cats table -->
    <table class='outer width100' cellspacing='1'>
        <tr>
            <th class='center width2'><{$smarty.const._AM_XNEWSLETTER_CAT_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_NAME}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CAT_INFO}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID}></th>
            <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
    <{$cats_content}>
    <{foreach item='cat' from=$cats}>
        <tr class="<{cycle values = 'even,odd'}>">
            <td class='center'><{$cat.cat_id}></td>
            <td class='center'><{$cat.cat_name}></td>
            <td><{$cat.cat_info}></td>
            <td class='center'><{$cat.catsubscrCount}></td>
            <td class='center'>
                <a href='?op=list_subscrs&cat_id=<{$cat.cat_id}>'>
                    <img
                        src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_details.png'
                        alt='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>'
                        title='<{$smarty.const._AM_XNEWSLETTER_DETAILS}>' />
                </a>
            </td>
        </tr>
    <{/foreach}>
    </table>
    <br />
    <br />
<{if ($cat_id > 0)}>
    <h2><{$smarty.const._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID}>: <{$cats[$cat_id].cat_name}></h2>
    <table class='outer width100' cellspacing='1'>
        <tr>
            <th class='center width5'>&nbsp;</th>
            <th class='center width10'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_SEX}></th>
            <th class='center width25'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_LASTNAME}></th>
            <th class='center width25'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_FIRSTNAME}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_SUBSCR_EMAIL}></th>
        <{if ($permissionChangeOthersSubscriptions)}>
            <th class='center width5'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        <{/if}>
        </tr>
        <{foreach item='subscr' from=$subscrs}>
        <tr>
            <td class='center'><{$subscr.counter}></td>
            <td class='center'><{$subscr.subscr_sex}></td>
            <td class='center'><{$subscr.subscr_lastname}></td>
            <td class='center'><{$subscr.subscr_firstname}></td>
            <td class='center'><{$subscr.subscr_email}></td>
        <{if ($permissionChangeOthersSubscriptions)}>
            <td class='center'>
                <a href='subscription.php?op=edit_subscription&subscr_id=<{$subscr.subscr_id}>'>
                    <img
                        src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                        alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>'
                        title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_EDIT}>' />
                </a>
            </td>
        <{/if}>
        </tr>
        <{/foreach}>
    </table>
    <br />
    <br />
<{/if}>
</div>
<{include file="db:xnewsletter_footer.tpl"}>