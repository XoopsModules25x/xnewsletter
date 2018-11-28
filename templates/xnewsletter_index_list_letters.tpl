<{include file="db:xnewsletter_header.tpl"}>
<div class="outer">
    <table cellpadding="0" cellspacing="0" class="item" width="100%">
        <tr class="itemHead">
            <th class="center"></th>
            <th class="center"><{$smarty.const._AM_XNEWSLETTER_LETTER_TITLE}></th>
            <th class="center"><{$smarty.const._AM_XNEWSLETTER_LETTER_CONTENT}></th>
            <th class="center"><{$smarty.const._MA_XNEWSLETTER_LETTER_CATS}></th>
            <th class="center"><{$smarty.const._AM_XNEWSLETTER_LETTER_CREATED}></th>
            <th class="center">&nbsp;</th>
        </tr>
    <{foreach item='letter' from=$letters}>
        <tr class = "<{cycle values = 'even,odd'}>">
            <td class="center"><{$letter.letter_id}></td>
            <td class="center"><{$letter.letter_title}></td>
            <td class="center"><{$letter.letter_content}></td>
            <td class="center">
            <{foreach item='letter_cat' from=$letter.letter_cats}>
                <{$letter_cat.cat_name}>
                <br />
            <{/foreach}>
            </td>
            <td class="center"><{$letter.letter_created_timestamp}></td>
            <td class="center">
                <a href="index.php?op=show_preview&letter_id=<{$letter.letter_id}>">
                    <img
                        src="<{$smarty.const.XNEWSLETTER_ICONS_URL}>/xn_preview.png"
                        alt="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>"
                        title="<{$smarty.const._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW}>" />
                </a>
            </td>
        </tr>
    <{/foreach}>
    </table>
</div>
<{include file="db:xnewsletter_footer.tpl"}>
