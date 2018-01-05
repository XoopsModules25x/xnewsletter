<{if $advertise_code != ''}>
    <div class="center">
        <{$advertise_code}>
    </div>
<{/if}>
<{if $social_active != 0}>
    <div class="center">
        <{$social_code}>
    </div>
<{/if}>
<div class="center">
    <{$copyright_code}>
</div>

<!-- footer menu -->
<div class="xnewsletter_adminlinks">
    <{foreach item='footerMenuItem' from=$xnewsletterModuleInfoSub}>
        <a href='<{$smarty.const.XNEWSLETTER_URL}>/<{$footerMenuItem.url}>'><{$footerMenuItem.name}></a>
    <{/foreach}>
    <{if $isAdmin == true}>
        <br>
        <a href='<{$smarty.const.XNEWSLETTER_URL}>/admin/index.php'><{$smarty.const._MA_XNEWSLETTER_ADMIN}></a>
    <{/if}>
</div>
