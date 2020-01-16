<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>

<{if $error}>
    <div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $resulttext}>
    <div style='margin-top:20px;margin-bottom:20px;color:#ff0000;font-weight:bold;font-size:14px'>
        <{$resulttext}>
    </div>
<{/if}>
<{if $form}>
    <{$form}>
<{/if}>
<br>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>

