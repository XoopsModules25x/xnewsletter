<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( http://www.xoops.org )
 * ****************************************************************************
 *  XNEWSLETTER - MODULE FOR XOOPS
 *  Copyright (c) 2007 - 2012
 *  Goffy ( wedega.com )
 *
 *  You may not change or alter any portion of this comment or credits
 *  of supporting developers from this source code or any supporting
 *  source code which is considered copyrighted (c) material of the
 *  original comment or credit authors.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  ---------------------------------------------------------------------------
 *  @copyright  Goffy ( wedega.com )
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op             = XoopsRequest::getString('op', 'list');
$save_and_check = XoopsRequest::getString('save_and_check', 'none');
$accounts_id    = XoopsRequest::getInt('accounts_id', 0);
$post           = XoopsRequest::getString('post', '');

if ('' == $post && 'save_accounts' === $op && 'none' === $save_and_check) {
    $op = 'edit_account';
}

switch ($op) {
    case 'check_account' :
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        if (0 == $accounts_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
        } else {
            $accountObj = $xnewsletter->getHandler('accounts')->get($accounts_id);
        }

        $mailhost = $accountObj->getVar('accounts_server_in');
        $port = $accountObj->getVar('accounts_port_in');
        switch ($accountObj->getVar('accounts_type')) {
            case _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3:
                $service = 'pop3';
                break;
            case _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP:
            case _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL:
                $service = 'imap';
                break;
            case 'default':
            default:
                $service = '';
                break;
        }
        $service_option = $accountObj->getVar('accounts_securetype_in');
        $accounts_password = $accountObj->getVar('accounts_password');
        $accounts_username = $accountObj->getVar('accounts_username');
        $accounts_inbox = $accountObj->getVar('accounts_inbox'); $accounts_inbox_ok = 0;
        $accounts_hardbox = $accountObj->getVar('accounts_hardbox'); $accounts_hardbox_ok = 0;
        $accounts_softbox = $accountObj->getVar('accounts_softbox'); $accounts_softbox_ok = 0;

        $command = $mailhost . ':' . $port;
        if ($service !='') $command .= '/' . $service;
        if ($service_option !='') $command .= '/' . $service_option;

        echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width5'></th>
                    <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_INFO . '</th>
                </tr>';

        $mbox = @imap_open('{' . $command . '}', $accounts_username, $accounts_password); // or die ("can't connect: " . imap_last_error());
        if ($mbox === false) {
            echo '<tr>';
            echo "<td class='center width5'>" . XNEWSLETTER_IMG_FAILED . '</td>';
            echo "<td class='left'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX._AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . '</td>';
            echo "<td class='left'>" . imap_last_error() . '</td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo "<td class='center width5'>" . XNEWSLETTER_IMG_OK . '</td>';
            echo "<td class='left'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX._AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . '</td>';
            echo "<td class='left'></td>";
            echo '</tr>';

            $folders = imap_listmailbox($mbox, '{' . $command . '}', '*');
            if ($folders == false) {
                echo '<tr>';
                echo "<td class='center width5'>" . XNEWSLETTER_IMG_FAILED . '</td>';
                echo "<td class='left'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS._AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . '</td>';
                echo "<td class='left'>" . imap_last_error() . '</td>';
                echo '</tr>';
            } else {
                echo '<tr>';
                echo "<td class='center width5'>" . XNEWSLETTER_IMG_OK . '</td>';
                echo "<td class='left'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS._AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . '</td>';
                echo "<td class='left'>";
                if (is_array($folders)) {
                    reset ($folders);
                    sort ($folders);
                    $foldercreated = 0;
                    foreach ($folders as $key => $val) {
                        echo "($key) ";
                        echo imap_utf7_decode (str_replace('{' . $command . '}', '', $val)) . "<br>\n";
                        if ('{' . $command . '}' . $accounts_inbox == $val) $accounts_inbox_ok = 1;
                        if ('{' . $command . '}' . $accounts_hardbox == $val) {
                            $accounts_hardbox_ok = 1;
                        } else {
                            @imap_createmailbox($mbox, imap_utf7_encode('{'.$command.'}' . $accounts_hardbox));
                            $foldercreated = 1;
                        }
                        if ('{' . $command . '}' . $accounts_softbox == $val) {
                            $accounts_softbox_ok = 1;
                        } else {
                            @imap_createmailbox($mbox, imap_utf7_encode('{' . $command . '}' . $accounts_softbox));
                            $foldercreated = 1;
                        }
                    }
                    if ($foldercreated == 1) {
                        $folders_recheck = imap_listmailbox($mbox, '{' . $command . '}', '*');
                        foreach ($folders_recheck as $key => $val) {
                            if ('{' . $command . '}' . $accounts_hardbox == $val) $accounts_hardbox_ok = 1;
                            if ('{' . $command . '}' . $accounts_softbox == $val) $accounts_softbox_ok = 1;
                        }
                    }
                }

                echo '</td>';
                echo '</tr>';
                echo '<tr>';
                if ($accountObj->getVar('accounts_use_bmh') == '1') {
                    if (1 == $accounts_inbox_ok && 1 == $accounts_hardbox_ok && 1 == $accounts_softbox_ok) {
                        echo "<td class='center width5'>" . XNEWSLETTER_IMG_OK . '</td>';
                    } else {
                        echo "<td class='center width5'>" . XNEWSLETTER_IMG_FAILED . '</td>';
                    }
                    echo "<td class='left'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH . '</td>';
                    echo "<td class='left'>";
                    echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_INBOX . ' ';
                    if (1 == $accounts_inbox_ok) {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . XNEWSLETTER_IMG_OK;
                    } else {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . XNEWSLETTER_IMG_FAILED;
                    }
                    echo '<br />';
                    echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_HARDBOX . ' ';
                    if (1 == $accounts_hardbox_ok) {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . XNEWSLETTER_IMG_OK;
                    } else {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . XNEWSLETTER_IMG_FAILED;
                    }
                    echo '<br />';
                    echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_SOFTBOX . ' ';
                    if (1 == $accounts_softbox_ok) {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . XNEWSLETTER_IMG_OK;
                    } else {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . XNEWSLETTER_IMG_FAILED;
                    }
                    echo '<br />';
                    echo '</td>';
                } else {
                    echo "<td class='center width5'>" . XNEWSLETTER_IMG_OK . '</td>';
                    echo "<td class='left'>" . _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH._AM_XNEWSLETTER_ACCOUNTS_CHECK_SKIPPED . '</td>';
                    echo "<td class='center'></td>";
                }
                echo '</tr>';
            }
            imap_close($mbox);
        }
        echo '</table>';
        break;

    case 'list':
    default:
        echo $indexAdmin->addNavigation($currentFile) ;
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWACCOUNTS, '?op=new_account', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $accountsCriteria = new CriteriaCompo();
        $accountsCriteria->setSort('accounts_id ASC, accounts_type');
        $accountsCriteria->setOrder('ASC');
        $accountsCount = $xnewsletter->getHandler('accounts')->getCount();
        $start = XoopsRequest::getInt('start', 0);
        $accountsCriteria->setStart($start);
        $accountsCriteria->setLimit($limit);
        $accountsObjs = $xnewsletter->getHandler('accounts')->getAll($accountsCriteria);
        if ($accountsCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new XoopsPageNav($accountsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($accountsCount > 0) {
            echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_ACCOUNTS_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_TYPE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_NAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_YOURNAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_YOURMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_DEFAULT . "</th>
                    <th class='center width10'>"._AM_XNEWSLETTER_FORMACTION . '</th>
                </tr>';

            $class = 'odd';

            foreach ($accountsObjs as $accounts_id => $accountsObj) {
                echo "<tr class='{$class}'>";
                $class = ($class === 'even') ? 'odd' : 'even';
                echo "<td class='center'>{$accounts_id}</td>";
                $arr_accounts_type= [
                        _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL => _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPMAIL,
                    _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL => _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPSENDMAIL,
                            _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3 => _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3,
                            _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP => _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP,
                           _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL => _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL
                ];
                echo "<td class='center'>" . $arr_accounts_type[$accountsObj->getVar('accounts_type')] . '</td>';
                echo "<td class='center'>" . $accountsObj->getVar('accounts_name') . '</td>';
                echo "<td class='center'>" . $accountsObj->getVar('accounts_yourname') . '</td>';
                echo "<td class='center'>" . $accountsObj->getVar('accounts_yourmail') . '</td>';
                $verif_accounts_default = ($accountsObj->getVar('accounts_default') == 1) ? _YES : _NO;
                echo "<td class='center'>{$verif_accounts_default}</td>";

                echo "<td class='center width5'>";
                echo "    <a href='?op=edit_account&accounts_id={$accounts_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='"._EDIT . "' /></a>";
                echo "    <a href='?op=delete_account&accounts_id={$accounts_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                if ($accountsObj->getVar('accounts_type') != _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL && $accountsObj->getVar('accounts_type') != _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL) {
                    echo "    <a href='?op=check_account&accounts_id={$accounts_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_check.png alt='" . _AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK . "' title='" . _AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK . "' /></a>";
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</table><br /><br />';
            echo "<br /><div class='center'>" . $pagenav . '</div><br />';
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                      <th class='center width2'>" . _AM_XNEWSLETTER_ACCOUNTS_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_TYPE . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_NAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_YOURNAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_YOURMAIL . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_USERNAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_PASSWORD . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_SERVER_IN . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_PORT_IN . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_IN . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_SERVER_OUT . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_PORT_OUT . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_OUT . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_ACCOUNTS_DEFAULT . "</th>
                        <th class='center width10'>" . _AM_XNEWSLETTER_FORMACTION . '</th>
                    </tr>';
            echo '</table><br /><br />';
        }

        break;

    case 'new_account':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $accountObj = $xnewsletter->getHandler('accounts')->create();
        $accountObj = xnewsletter_setPost($accountObj, $_POST);
        $form = $accountObj->getForm();
        $form->display();
        break;

    case 'save_accounts':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $accountObj = $xnewsletter->getHandler('accounts')->get($accounts_id);
        $_POST['accounts_id'] = $accounts_id;
        $accountObj = xnewsletter_setPost($accountObj, $_POST);

        $accountsCriteria = new CriteriaCompo();
        $accountsCriteria->add(new Criteria('accounts_default', 1));
        $count_accounts_default = $xnewsletter->getHandler('accounts')->getCount($accountsCriteria);
        if ($count_accounts_default > 0) {
            if (1 == $accountObj->getVar('accounts_default')) {
                global $xoopsDB;
                $verif_accounts_default = 1;
                //reset old accounts_default
                $sql = "UPDATE `{$xoopsDB->prefix('xnewsletter_accounts')}` SET `accounts_default` = '0'";
                if(!$result = $xoopsDB->query($sql)) die ('MySQL-Error: ' . $xoopsDB->error());
            } else {
                $verif_accounts_default = 0;
            }
        } else {
            $verif_accounts_default = 1;
        }
        $accountObj->setVar('accounts_default', $verif_accounts_default);
        if (('' != $accountObj->getVar('accounts_yourmail')) && $accountObj->getVar('accounts_yourmail') != _AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL ) {
            if ($xnewsletter->getHandler('accounts')->insert($accountObj)) {
                if ('none' === $save_and_check) {
                    redirect_header('?op=list', 2, _AM_XNEWSLETTER_FORMOK);
                } else {
                    redirect_header("?op=check_account&accounts_id={$accountObj->getVar('accounts_id')}", 2, _AM_XNEWSLETTER_FORMOK);
                }
            }
        } else {
            $accountObj->setErrors(_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
        }

        echo $accountObj->getHtmlErrors();
        $form = $accountObj->getForm();
        $form->display();
        break;

    case 'edit_account':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWACCOUNTS, '?op=new_account', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $accountObj = $xnewsletter->getHandler('accounts')->get($accounts_id);
        if (!empty($_POST)) {
            xnewsletter_setPost($accountObj, $_POST);
        }
        $form = $accountObj->getForm();
        $form->display();
    break;

    case 'delete_account':
        $accountObj = $xnewsletter->getHandler('accounts')->get($accounts_id);
        if (isset($_POST['ok']) && $_POST['ok'] == '1') {
            if ( !$GLOBALS['xoopsSecurity']->check() ) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('accounts')->delete($accountObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $accountObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => 1, 'accounts_id' => $accounts_id, 'op' => 'delete_account'], $currentFile, sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $accountObj->getVar('accounts_name')));
        }
        break;
}
include_once __DIR__ . '/admin_footer.php';
