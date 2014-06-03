<{include file="db:xnewsletter_header.tpl"}>
<div style='clear:both;'></div>
<div class="outer">
    <h2><{$letter.letter_title}></h2>
    <div style='padding:10px;border:1px solid black;'>
        <{$letter.letter_content_templated}>
    </div>
    <input action="action" type="button" value="<{$smarty.const._BACK}>" onclick="history.go(-1);" />
</div>
<div style='clear:both;'></div>
<{include file="db:xnewsletter_footer.tpl"}>