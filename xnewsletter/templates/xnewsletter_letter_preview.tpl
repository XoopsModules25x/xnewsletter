<{include file="db:xnewsletter_header.tpl"}>

<div style='clear:both;'></div>

<h2><{$letter.letter_title}></h2>
<div style='padding:0px; margin:0px; border:none;'>
    <{$letter.letter_content_templated}>
</div>
<div style='clear:both;'></div>

<input action="action" type="button" value="<{$smarty.const._BACK}>" onclick="history.go(-1);" />
<div style='clear:both;'></div>

<{include file="db:xnewsletter_footer.tpl"}>
