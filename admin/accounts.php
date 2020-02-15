<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
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
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_accounts.tpl';

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

// We recovered the value of the argument op in the URL$
$op             = Request::getString('op', 'list');
$save_and_check = Request::getString('save_and_check', 'none');
$accounts_id    = Request::getInt('accounts_id', 0);
// $post           = Request::getString('post', '');

// if ('' == $post && 'save_accounts' === $op && 'none' === $save_and_check) {
    // $op = 'edit_account';
// }

switch ($op) {
    case 'check_account':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $GLOBALS['xoopsTpl']->assign('account_check', true);

        if (0 == $accounts_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
        } else {
            $accountObj = $helper->getHandler('Accounts')->get($accounts_id);
        }

        $mailhost = $accountObj->getVar('accounts_server_in');
        $port     = $accountObj->getVar('accounts_port_in');
        switch ($accountObj->getVar('accounts_type')) {
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3:
                $service = 'pop3';
                break;
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP:
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL:
                $service = 'imap';
                break;
            case 'default':
            default:
                $service = '';
                break;
        }
        $service_option      = $accountObj->getVar('accounts_securetype_in');
        $accounts_password   = $accountObj->getVar('accounts_password');
        $accounts_username   = $accountObj->getVar('accounts_username');
        $accounts_inbox      = $accountObj->getVar('accounts_inbox');
        $accounts_inbox_ok   = false;
        $accounts_hardbox    = $accountObj->getVar('accounts_hardbox');
        $accounts_hardbox_ok = false;
        $accounts_softbox    = $accountObj->getVar('accounts_softbox');
        $accounts_softbox_ok = false;

        $command = $mailhost . ':' . $port;
        if ('' != $service) {
            $command .= '/' . $service;
        }
        if ('' != $service_option) {
            $command .= '/' . $service_option;
        }

        $checks = [];

        $mbox = @imap_open('{' . $command . '}', $accounts_username, $accounts_password);
        if (false === $mbox) {
            $checks['openmailbox']['check'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX;
            $checks['openmailbox']['result'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED;
            $checks['openmailbox']['result_img'] = XNEWSLETTER_IMG_FAILED;
            $checks['openmailbox']['info'] = imap_last_error();

        } else {
            $checks['openmailbox']['check'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX;
            $checks['openmailbox']['result'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK;
            $checks['openmailbox']['result_img'] = XNEWSLETTER_IMG_OK;

            $folders = imap_list($mbox, '{' . $command . '}', '*');
            if (false === $folders) {
                $checks['listfolder']['check'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_LIST_FOLDERS;
                $checks['listfolder']['result'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED;
                $checks['listfolder']['result_img'] = XNEWSLETTER_IMG_FAILED;
                $checks['listfolder']['info'] = imap_last_error();
            } else {
                $checks['listfolder']['check'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_LIST_FOLDERS;
                $checks['listfolder']['result'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK;
                $checks['listfolder']['result_img'] = XNEWSLETTER_IMG_OK;

                //check whether mailboxes from yor settings are really in folders array
                $arrCheckMB[]= $accounts_inbox;
                $arrCheckMB[]= $accounts_hardbox;
                $arrCheckMB[]= $accounts_softbox;
                foreach ($arrCheckMB as $key => $mailbox) {
                    $found_result = 0;
                    $foldercreated = 1;
                    if (is_array($folders)) {
                        foreach ($folders as $key => $val) {
                            if ('{' . $command . '}' . $mailbox == $val) {
                                $found_result = true;
                            }
                        }
                    }
                    if ($found_result == 0) {
                        if (false === @imap_createmailbox($mbox, imap_utf7_encode('{' . $command . '}' . $mailbox))) {
//                                throw new \RuntimeException('The hard-mailbox '.$mbox.' could not be created.');
                            $checks[$mailbox]['created'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_MAILBOX_CREATE_FAILED . $mailbox;
                        } else {
                            $foldercreated = 1;
                            $checks[$mailbox]['created'] = true;
                            $found_result = true;
                            $checks[$mailbox]['created'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_MAILBOX_CREATE_SUCCESS . $mailbox;
                        }
                    }
                    if ($found_result) {
                        $checks[$mailbox]['check'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS . $mailbox;
                        $checks[$mailbox]['result'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK;
                        $checks[$mailbox]['result_img'] = XNEWSLETTER_IMG_OK;
                    } else {
                        $checks[$mailbox]['check'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS . $mailbox;
                        $checks[$mailbox]['result'] = _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED;
                        $checks[$mailbox]['result_img'] = XNEWSLETTER_IMG_FAILED;

                    }
                }
            }
            imap_close($mbox);
        }
        $GLOBALS['xoopsTpl']->assign('checks', $checks);
        break;
    case 'list':
    case 'list_accounts':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWACCOUNTS, '?op=new_account', 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $limit            = $helper->getConfig('adminperpage');
        $accountsCriteria = new \CriteriaCompo();
        $accountsCriteria->setSort('accounts_id ASC, accounts_type');
        $accountsCriteria->setOrder('ASC');
        $accountsCount = $helper->getHandler('Accounts')->getCount();
        $start         = Request::getInt('start', 0);
        $accountsCriteria->setStart($start);
        $accountsCriteria->setLimit($limit);
        $accountsAll = $helper->getHandler('Accounts')->getAll($accountsCriteria);
        if ($accountsCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($accountsCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }

        if ($accountsCount > 0) {
            $GLOBALS['xoopsTpl']->assign('accountsCount', $accountsCount);

            foreach ($accountsAll as $acc_id => $accountsObj) {
                $account = $accountsObj->getValuesAccount();
                if (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL != $account['accounts_type']
                    && _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL != $account['accounts_type']) {
                    $account['show_check'] = true;
                }
                $GLOBALS['xoopsTpl']->append('accounts_list', $account);
                unset($account);
            }
        }
        break;
    case 'new_account':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $accountObj = $helper->getHandler('Accounts')->create();
        $accountObj = xnewsletter_setPost($accountObj, $_POST);
        $form       = $accountObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_accounts':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $accountObj           = $helper->getHandler('Accounts')->get($accounts_id);
        $_POST['accounts_id'] = $accounts_id;
        $accountObj           = xnewsletter_setPost($accountObj, $_POST);

        $accountsCriteria = new \CriteriaCompo();
        $accountsCriteria->add(new \Criteria('accounts_default', 1));
        $count_accounts_default = $helper->getHandler('Accounts')->getCount($accountsCriteria);
        if ($count_accounts_default > 0) {
            if (1 == $accountObj->getVar('accounts_default')) {
                $verif_accounts_default = 1;
                //reset old accounts_default
                if (!$helper->getHandler('Accounts')->updateAll('accounts_default', 0, null, false)) {
                    exit('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
                }
            } else {
                $verif_accounts_default = 0;
            }
        } else {
            $verif_accounts_default = 1;
        }
        $accountObj->setVar('accounts_default', $verif_accounts_default);
        if ('' != $accountObj->getVar('accounts_yourmail')
            && _AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL != $accountObj->getVar('accounts_yourmail')) {
            if ($helper->getHandler('Accounts')->insert($accountObj)) {
                if ('none' === $save_and_check) {
                    redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
                } else {
                    redirect_header("?op=check_account&accounts_id={$accountObj->getVar('accounts_id')}", 3, _AM_XNEWSLETTER_FORMOK);
                }
            }
        } else {
            $accountObj->setErrors(_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
        }

        $GLOBALS['xoopsTpl']->assign('error', $accountObj->getHtmlErrors());
        $form = $accountObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_account':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWACCOUNTS, '?op=new_account', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $accountObj = $helper->getHandler('Accounts')->get($accounts_id);
        if (!empty($_POST)) {
            xnewsletter_setPost($accountObj, $_POST);
        }
        $form = $accountObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_account':
        $accountObj = $helper->getHandler('Accounts')->get($accounts_id);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Accounts')->delete($accountObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $accountObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'accounts_id' => $accounts_id, 'op' => 'delete_account'], $currentFile, sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $accountObj->getVar('accounts_name')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
