<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='7'><{$smarty.const._AM_XNEWSLETTER_THEREARE_CAT|replace:'%s':$catCount}></td>
    </tr>
    <tr>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_NAME}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_INFO}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_ADMIN}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_CREATE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_LIST}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_GPERMS_READ}></th>
    <{if ($xn_use_mailinglist)}>
        <th><{$smarty.const._AM_XNEWSLETTER_CAT_MAILINGLIST}></th>
    <{/if}>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach from=$cats item='cat'}>
    <tr class="<{cycle values='odd, even'}>">
        <td><{$cat.cat_id}></td>
        <td><{$cat.cat_name}>&nbsp;</td>
        <td><{$cat.cat_info}>&nbsp;</td>
        <td>
            <ul>
            <{foreach from=$cat.cat_gperms_admin_groups item='cat_gperms_admin_group'}>
                <li>
                    <a class="tooltip" title="<{$smarty.const._AM_XNEWSLETTER_GROUPS_EDIT}>"
                        href="<{$smarty.const.XOOPS_URL}>/modules/system/admin.php?fct=groups&op=groups_edit&groups_id=<{$cat_gperms_admin_group.group_id}>">
                        <{$cat_gperms_admin_group.group_name}>
                    </a>
                </li>
            <{/foreach}>
            </ul>
        </td>
        <td>
            <ul>
            <{foreach from=$cat.cat_gperms_create_groups item='cat_gperms_create_group'}>
                <li>
                    <a class="tooltip" title="<{$smarty.const._AM_XNEWSLETTER_GROUPS_EDIT}>"
                        href="<{$smarty.const.XOOPS_URL}>/modules/system/admin.php?fct=groups&op=groups_edit&groups_id=<{$cat_gperms_create_group.group_id}>">
                        <{$cat_gperms_create_group.group_name}>
                    </a>
                </li>
            <{/foreach}>
            </lu>
        </td>
        <td>
            <ul>
            <{foreach from=$cat.cat_gperms_list_groups item='cat_gperms_list_group'}>
                <li>
                    <a class="tooltip" title="<{$smarty.const._AM_XNEWSLETTER_GROUPS_EDIT}>"
                        href="<{$smarty.const.XOOPS_URL}>/modules/system/admin.php?fct=groups&op=groups_edit&groups_id=<{$cat_gperms_list_group.group_id}>">
                        <{$cat_gperms_list_group.group_name}>
                    </a>
                </li>
            <{/foreach}>
            </ul>
        </td>
        <td>
            <ul>
            <{foreach from=$cat.cat_gperms_read_groups item='cat_gperms_read_group'}>
                <li>
                    <a class="tooltip" title="<{$smarty.const._AM_XNEWSLETTER_GROUPS_EDIT}>"
                        href="<{$smarty.const.XOOPS_URL}>/modules/system/admin.php?fct=groups&op=groups_edit&groups_id=<{$cat_gperms_read_group.group_id}>">
                        <{$cat_gperms_read_group.group_name}>
                    </a>
                </li>
            <{/foreach}>
            </ul>
        </td>
    <{if ($xn_use_mailinglist)}>
        <td><{$cat.cat_mailinglist}>&nbsp;</td>
    <{/if}>
        <td class='center' nowrap='nowrap'>
            <a href='?op=edit_cat&cat_id=<{$cat.cat_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>'
                    title='<{$smarty.const._EDIT}>'
                    style='padding:1px' /></a>
            <a href='?op=delete_cat&cat_id=<{$cat.cat_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png'
                    alt='<{$smarty.const._DELETE}>'
                    title='<{$smarty.const._DELETE}>'
                    style='padding:1px' /></a>
        </td>
    </tr>
<{/foreach}>
</table>
<{$cats_pagenav}>
