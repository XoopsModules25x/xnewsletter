<{include file="db:xNewsletter_header.tpl"}>
<div class="outer">
    <p align="center" style="margin-top: 20px; margin-bottom: 20px; font-weight:bold">
        <{$subscription_result}>
    </p>
<{foreach item='actionProt_ok' from=$actionProts_ok}>
    <p>
        <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/on.png' alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_OK}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_OK}>'>
        &nbsp;&nbsp;<{$actionProt_ok}>
    </p>
<{/foreach}>
<{foreach item='actionProt_error' from=$actionProts_error}>
    <p>
        <img src='<{$smarty.const.XNEWSLETTER_ICONS_URL}>/off.png' alt='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_ERROR}>' title='<{$smarty.const._MA_XNEWSLETTER_SUBSCRIPTION_ERROR}>"'>
        &nbsp;&nbsp;
        <{$actionProt_error}>
    </p>
<{/foreach}>
</div>
<{include file="db:xNewsletter_footer.tpl"}>