<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<{if $form}>
	<{$form}>
<{/if}>
<{if $error}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<{if $maintenance}>
    <{$maintenance}>
<{/if}>

<br>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>
