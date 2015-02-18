<{include file="db:xnewsletter_header.tpl"}>

<{if (($actionProts_ok|@count gt 0) or ($actionProts_warning|@count gt 0) or ($actionProts_error|@count gt 0))}>
<{foreach item='actionProt_ok' from=$actionProts_ok}>
    <div>
        <span class="left" style="display: inline-block; height: 100%; vertical-align: middle;">
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/on.png' alt='<{$smarty.const._OK}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_OK}>'>
        </span>
        <span style="display: inline-block; vertical-align: middle; line-height: normal;">
            <{$actionProt_ok}>
        </span>
        <div style="clear:both;"></div>
    </div>
<{/foreach}>
<{foreach item='actionProt_warning' from=$actionProts_warning}>
    <div>
        <span class="left" style="display: inline-block; height: 100%; vertical-align: middle;">
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/alert.png' alt='<{$smarty.const._WARNING}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_OK}>'>
        </span>
        <span style="display: inline-block; vertical-align: middle; line-height: normal;">
            <{$actionProt_warning}>
        </span>
        <div style="clear:both;"></div>
    </div>
<{/foreach}>
<{foreach item='actionProt_error' from=$actionProts_error}>
    <div>
        <span class="left" style="display: inline-block; height: 100%; vertical-align: middle;">
            <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/off.png' alt='<{$smarty.const._ERROR}>' title='<{$smarty.const._ERROR}>"'>
        </span>
        <span style="display: inline-block; vertical-align: middle; line-height: normal;">
            <{$actionProt_error}>
        </span>
        <div style="clear:both;"></div>
    </div>
<{/foreach}>
<br />
<{/if}>

<{include file="db:xnewsletter_footer.tpl"}>
