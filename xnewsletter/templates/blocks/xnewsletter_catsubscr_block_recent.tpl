<table class="outer">
    <{foreachq item=catsubscr from=$block}>
    <tr class="<{cycle values = "even,odd"}>">
        <td>
            <{$catsubscr.catsubscr_email}>;
            <{$catsubscr.catsubscr_newsletter}>;
            <{$catsubscr.catsubscr_created}>;
        </td>
    </tr>
    <{/foreach}>
</table>
