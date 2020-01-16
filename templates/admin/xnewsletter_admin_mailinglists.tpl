<!-- Header -->
<{include file='db:xnewsletter_admin_header.tpl'}>
<style>
    .errorInfo {
        background-color: #b94a48;
        color:#fff;
    }
    .successInfo {
        background-color: #aaff88;
        color:#000;
    }
    .errorInfo, .successInfo {
        padding:20px 50px;
        margin: 5px;
        text-align: center;
        border-radius:5px;
    }
</style>
<{if $error}>
    <div class='errorInfo'><strong><{$error}></strong></div>
<{/if}>
<{if $success}>
    <div class='successInfo'><strong><{$success}></strong></div>
<{/if}>
<{if $form}>
    <{$form}>
<{/if}>
<{if $mailinglists_list}>
    <table class='table table-bordered' >
        <thead>
        <tr class='head'>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_ID}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_SYSTEM}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_NAME}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_LISTNAME}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_PARAMS}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_CREATED}></th>
            <th class='center'><{$smarty.const._AM_XNEWSLETTER_FORMACTION}></th>
        </tr>
        </thead>
        <tbody>
            <{if $mailinglistCount}>
                <{foreach item=mailinglist from=$mailinglists_list}>
                <tr class="<{cycle values='odd, even'}>">
                    <td class='center'><{$mailinglist.id}></td>
                    <td class='center'><{$mailinglist.system_text}></td>
                    <td class='center'><{$mailinglist.name}></td>
                    <td class='center'><{$mailinglist.listname}></td>
                    <td class='left'>
                        <{if $mailinglist.system == $smarty.const._XNEWSLETTER_MAILINGLIST_TYPE_MAJORDOMO_VAL}>
                            <{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_EMAIL}>:  <{$mailinglist.email}><br>
                            <{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE}>:  <{$mailinglist.subscribe}><br>
                            <{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE}>:  <{$mailinglist.unsubscribe}><br>
                        <{/if}>
                        <{if $mailinglist.system == $smarty.const._XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL}>
                        <{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_TARGET}>:  <{$mailinglist.target}><br>
                        <{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_PWD}>:  <{$mailinglist.pwd}><br>
                        <{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_NOTIFYOWNER}>:  <{$mailinglist.notifyowner_text}><br>
                        <{/if}>
                    </td>
                    <td class='center'><{$mailinglist.created}></td>
                    <td class='center  width10'>
                        <a href='?op=edit_mailinglist&mailinglist_id=<{$mailinglist.id}>'><img src='<{$xnewsletter_icons_url}>/xn_edit.png' alt='<{$smarty.const._EDIT}>' title='<{$smarty.const._EDIT}>'></a>
                        <a href='?op=delete_mailinglist&mailinglist_id=<{$mailinglist.id}>'><img src='<{$xnewsletter_icons_url}>/xn_delete.png' alt='<{$smarty.const._DELETE}>' title='<{$smarty.const._DELETE}>'></a>
                        <{if $mailinglist.check_list}>
                            <a href='?op=check_list&mailinglist_id=<{$mailinglist.id}>'><img src='<{$xnewsletter_icons_url}>/xn_check.png' alt='<{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_MEMBERS}>' title='<{$smarty.const._AM_XNEWSLETTER_MAILINGLIST_MEMBERS}>'></a>
                        <{/if}>
                    </td>
                </tr>
                <{/foreach}>
            <{/if}>
        </tbody>
    </table>
    <div class='clear'>&nbsp;</div>
    <{if $pagenav}>
    <div class='xo-pagenav floatright'><{$pagenav}></div>
    <div class='clear spacer'></div>
    <{/if}>
<{/if}>
<br>
<!-- Footer --><{include file='db:xnewsletter_admin_footer.tpl'}>

<script>
    function toggleFromFields (type) {
        if (type == <{$val_mailman}>) {
            /* phyton mailman */
            el = document.getElementById('mailinglist_email');
            el.setAttribute("disabled", "disabled");
            el.style.backgroundColor = "#ddd";
            el = document.getElementById('mailinglist_subscribe');
            el.setAttribute("disabled", "disabled");
            el.style.backgroundColor = "#ddd";
            el = document.getElementById('mailinglist_unsubscribe');
            el.setAttribute("disabled", "disabled");
            el.style.backgroundColor = "#ddd";
            el = document.getElementById('mailinglist_pwd');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_notifyowner1');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_notifyowner2');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
        } else if (type == <{$val_majordomo}>) {
            /* majordomo */
            el = document.getElementById('mailinglist_email');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_subscribe');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_unsubscribe');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_pwd');
            el.setAttribute("disabled", "disabled");
            el.style.backgroundColor = "#ddd";
            el = document.getElementById('mailinglist_notifyowner1');
            el.setAttribute("disabled", "disabled");
            el.style.backgroundColor = "#ddd";
            el = document.getElementById('mailinglist_notifyowner2');
            el.setAttribute("disabled", "disabled");
            el.style.backgroundColor = "#ddd";
        } else {
            /* misc */
            el = document.getElementById('mailinglist_name');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_email');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_listname');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_subscribe');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_unsubscribe');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_pwd');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_notifyowner');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_notifyowner1');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
            el = document.getElementById('mailinglist_notifyowner2');
            el.removeAttribute("disabled");
            el.style.backgroundColor = "#fff";
        }
    };
</script>