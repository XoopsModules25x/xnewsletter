<form name="<{$block.formname}>" id="<{$block.formname}>" action="<{$block.formaction}>" method="post" enctype="multipart/form-data">
    <table class="outer">
        <tr class="<{cycle values = "even,odd"}>">
            <td style="text-align:center;">
                <{$block.infotext}>
            </td>
        </tr>
        <tr class="<{cycle values = "even,odd"}>">
            <td style="text-align:center;">
                <input type="submit" class="formButton" name="submit" id="xn_block_submit" value="<{$block.buttontext}>"
                       title="<{$block.buttontext}>">
            </td>
        </tr>
    </table>
</form>
