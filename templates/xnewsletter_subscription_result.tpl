<{include file="db:xnewsletter_header.tpl"}>

<{if (($actionProts_ok|@count gt 0) or ($actionProts_warning|@count gt 0) or ($actionProts_error|@count gt 0))}>
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

<{include file="db:xnewsletter_footer.tpl"}>
