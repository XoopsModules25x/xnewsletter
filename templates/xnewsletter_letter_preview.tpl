<{include file="db:xnewsletter_header.tpl"}>

<div style='clear:both;'></div>

<h2><{$letter.letter_title}></h2>
<div style='padding:10px;margin:20px 0;border:1px solid black;'>
    <{$letter.letter_content_templated}>
</div>
<div style='clear:both;'></div>

<input class="btn btn-primary wg-color1" type="button" value="<{$smarty.const._BACK}>" onclick="history.go(-1);">
<div style='clear:both;'></div>

<{include file="db:xnewsletter_footer.tpl"}>
