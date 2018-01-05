<table class="outer">
    <{foreachq item=letter from=$block}>
    <tr class="<{cycle values = "even,odd"}>">
        <td>
            <a href="<{$letter.href}>"><{$letter.letter_title}><br>(<{$letter.letter_created}>)</a>
        </td>
    </tr>
    <{/foreach}>
</table>
