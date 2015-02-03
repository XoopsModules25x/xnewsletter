<table class='outer' cellspacing='1'>
    <tr>
        <td align='left' colspan='6'><{$smarty.const._AM_XNEWSLETTER_THEREARE_TEMPLATE|replace:'%s':$templateCount}></td>
    </tr>
    <tr>
        <th><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_ID}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_TITLE}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_DESCRIPTION}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_SUBMITTER}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_TEMPLATE_CREATED}></th>
        <th><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
    </tr>
<{foreach from=$templates item='template'}>
    <tr class="<{cycle values='odd, even'}>">
        <td><{$template.template_id}></td>
        <td><{$template.template_title}>&nbsp;</td>
        <td><{$template.template_description}>&nbsp;</td>
        <td><{$template.template_submitter_uname}>&nbsp;</td>
        <td><{$template.template_created_formatted}></td>
        <td class='center' nowrap='nowrap'>
            <a href='?op=edit_template&template_id=<{$template.template_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_edit.png'
                    alt='<{$smarty.const._EDIT}>'
                    title='<{$smarty.const._EDIT}>'
                    style='padding:1px' /></a>
            <a href='?op=delete_template&template_id=<{$template.template_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_delete.png'
                    alt='<{$smarty.const._DELETE}>'
                    title='<{$smarty.const._DELETE}>'
                    style='padding:1px' /></a>
            <a href='?op=show_preview&template_id=<{$template.template_id}>'>
                <img
                    src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_preview.png'
                    alt='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>'
                    title='<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>'
                    style='padding:1px' /></a>
        </td>
    </tr>
<{/foreach}>
</table>
<{$templates_pagenav}>
