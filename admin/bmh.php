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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op          = \Xmf\Request::getString('op', 'list');
$bmh_id      = \Xmf\Request::getInt('bmh_id', 0);
$bmh_measure = \Xmf\Request::getInt('bmh_measure', 0);
$filter      = \Xmf\Request::getInt('bmh_measure_filter', _XNEWSLETTER_BMH_MEASURE_VAL_ALL);

switch ($op) {
    case 'bmh_delsubscr':
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            $count_err = 0;

            $bmhObj    = $helper->getHandler('Bmh')->get($bmh_id);
            $bmh_email = $bmhObj->getVar('bmh_email');

            $sql = "SELECT subscr_id FROM {$xoopsDB->prefix('xnewsletter_subscr')}";
            $sql .= " WHERE (subscr_email='{$bmh_email}')";
            $sql .= ' LIMIT 1;';
            if ($user = $xoopsDB->query($sql)) {
                $row_user  = $xoopsDB->fetchRow($user);
                $subscr_id = (int)$row_user[0];
            }
            if (0 == $subscr_id) {
                //set bmh_measure for all entries in bmh with this email
                $sql_upd_measure = "UPDATE {$xoopsDB->prefix('xnewsletter_bmh')} SET `bmh_measure` = '" . _XNEWSLETTER_BMH_MEASURE_VAL_NOTHING . "'";
                $sql_upd_measure .= " WHERE ((`{$xoopsDB->prefix('xnewsletter_bmh')}`.`bmh_email` ='{$bmh_email}') AND (`{$xoopsDB->prefix('xnewsletter_bmh')}`.`bmh_measure` ='0'))";
                $xoopsDB->query($sql_upd_measure);
                redirect_header('?op=list', 3, _AM_XNEWSLETTER_BMH_ERROR_NO_SUBSCRID);
            }
            $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);

            // delete subscriber
            if (!$helper->getHandler('Subscr')->delete($subscrObj, true)) {
                $actionprot_err = $subscrObj->getHtmlErrors() . '<br><br><br>';
                ++$count_err;
            }

            //delete subscription
            $catsubscrCriteria = new \CriteriaCompo();
            $catsubscrCriteria->add(new \Criteria('catsubscr_subscrid', $subscr_id));
            $catsubscrsCount = $helper->getHandler('Catsubscr')->getCount($catsubscrCriteria);
            if ($catsubscrsCount > 0) {
                $catsubscrObjs = $helper->getHandler('Catsubscr')->getAll($catsubscrCriteria);
                foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                    $catsubscrObj    = $helper->getHandler('Catsubscr')->get($catsubscrObj->getVar('catsubscr_id'));
                    $catObj          = $helper->getHandler('Cat')->get($catsubscrObj->getVar('catsubscr_catid'));
                    $cat_mailinglist = $catObj->getVar('cat_mailinglist');

                    if ($helper->getHandler('Catsubscr')->delete($catsubscrObj, true)) {
                        //handle mailinglists
                        if ($cat_mailinglist > 0) {
                            require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                            subscribingMLHandler(0, $subscr_id, $cat_mailinglist);
                        }
                    } else {
                        $actionprot_err .= $catsubscrObj->getHtmlErrors();
                        ++$count_err;
                    }
                }
            }

            if (0 == $count_err) {
                redirect_header("?op=handle_bmh&bmh_id={$bmh_id}&bmh_measure=" . _XNEWSLETTER_BMH_MEASURE_VAL_DELETE . "&filter={$filter}", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $actionprot_err;
            }
        } else {
            xoops_confirm(['ok' => true, 'bmh_id' => $bmh_id, 'op' => 'bmh_delsubscr', 'filter' => $filter], $currentFile, sprintf(_AM_XNEWSLETTER_BMH_MEASURE_DELETE_SURE));
        }
        break;
    case 'handle_bmh':
        if (0 == $bmh_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
        }
        if (0 == $bmh_measure) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
        }

        $bmhObj = $helper->getHandler('Bmh')->get($bmh_id);

        if (_XNEWSLETTER_BMH_MEASURE_VAL_DELETE == $bmhObj->getVar('bmh_measure')) {
            redirect_header("?op=list&filter={$filter}'", 3, _AM_XNEWSLETTER_BMH_MEASURE_ALREADY_DELETED);
        }

        $bmh_email = $bmhObj->getVar('bmh_email');

        if (_XNEWSLETTER_BMH_MEASURE_VAL_QUIT == $bmh_measure) {
            $sql = "UPDATE `{$xoopsDB->prefix('xnewsletter_subscr')}` INNER JOIN `{$xoopsDB->prefix('xnewsletter_catsubscr')}` ON `subscr_id` = `catsubscr_subscrid`";
            $sql .= ' SET `catsubscr_quited` = ' . time();
            $sql .= " WHERE (((`subscr_email`)='{$bmh_email}'))";
            if (!$result = $xoopsDB->queryF($sql)) {
                die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
            }
        }
        //set bmh_measure for all entries in bmh with this email
        $sql_upd = "UPDATE {$xoopsDB->prefix('xnewsletter_bmh')}";
        $sql_upd .= " SET `bmh_measure` = '{$bmh_measure}', `bmh_submitter` = '{$xoopsUser->uid()}', `bmh_created` = '" . time() . "'";
        $sql_upd .= " WHERE ((`{$xoopsDB->prefix('xnewsletter_bmh')}`.`bmh_email` ='{$bmh_email}') AND (`{$xoopsDB->prefix('xnewsletter_bmh')}`.`bmh_measure` ='0'))";
        if (!$result = $xoopsDB->queryF($sql_upd)) {
            die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
        }

        redirect_header("?op=list&filter={$filter}", 3, _AM_XNEWSLETTER_FORMOK);

        echo $bmhObj->getHtmlErrors();
        break;
    case 'run_bmh':
        require_once __DIR__ . '/bmh_callback_database.php';
//        require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/phpmailer_bmh/class.phpmailer-bmh.php';

        $accountCriteria = new \CriteriaCompo();
        $accountCriteria->add(new \Criteria('accounts_use_bmh', '1'));
        $accountsCount = $helper->getHandler('Accounts')->getCount($accountCriteria);

        if ($accountsCount > 0) {
            $accountObjs = $helper->getHandler('Accounts')->getAll($accountCriteria);
            $result_bmh  = _AM_XNEWSLETTER_BMH_SUCCESSFUL . '<br>';

            foreach ($accountObjs as $account_id => $accountObj) {
                $bmh          = new BounceMailHandler();
                $bmh->verbose = VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
                //$bmh->use_fetchstructure = true; // true is default, no need to speficy
                //$bmh->testmode           = true; // false is default, no need to specify
                //$bmh->debug_body_rule    = false; // false is default, no need to specify
                //$bmh->debug_dsn_rule     = false; // false is default, no need to specify
                //$bmh->purge_unprocessed  = false; // false is default, no need to specify
                $bmh->disable_delete = true; // detected mails will be not deleted, default is false

                // for local mailbox (to process .EML files)
                //$bmh->openLocalDirectory('/home/email/temp/mailbox');
                //$bmh->processMailbox();

                // for remote mailbox
                $bmh->mailhost         = $accountObj->getVar('accounts_server_in'); // your mail server
                $bmh->mailbox_username = $accountObj->getVar('accounts_username'); // your mailbox username
                $bmh->mailbox_password = $accountObj->getVar('accounts_password'); // your mailbox password
                $bmh->port             = $accountObj->getVar('accounts_port_in'); // the port to access your mailbox, default is 143
                if (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3 == $accountObj->getVar('accounts_type')) {
                    $bmh->service = 'pop3'; // the service to use (imap or pop3), default is 'imap'
                } else {
                    $bmh->service = 'imap'; // the service to use (imap or pop3), default is 'imap'
                }
                $bmh->service_option = $accountObj->getVar('accounts_securetype_in'); // the service options (none, tls, notls, ssl, etc.), default is 'notls'
                $bmh->boxname        = $accountObj->getVar('accounts_inbox'); // the mailbox to access, default is 'INBOX'
                $verif_movehard      = '1' == $accountObj->getVar('accounts_movehard') ? true : false;
                $bmh->moveHard       = $verif_movehard; // default is false
                $bmh->hardMailbox    = $accountObj->getVar('accounts_hardbox'); // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
                $verif_movesoft      = '1' == $accountObj->getVar('accounts_movesoft') ? true : false;
                $bmh->moveSoft       = $verif_movesoft; // default is false
                $bmh->softMailbox    = $accountObj->getVar('accounts_softbox'); // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
                //$bmh->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'

                // rest used regardless what type of connection it is
                $bmh->openMailbox();
                $bmh->processMailbox();

                $result_bmh .= str_replace('%b', $accountObj->getVar('accounts_yourmail'), _AM_XNEWSLETTER_BMH_RSLT);
                $result_bmh = str_replace('%r', $bmh->result_total, $result_bmh);
                $result_bmh = str_replace('%a', $bmh->result_processed, $result_bmh);
                $result_bmh = str_replace('%n', $bmh->result_unprocessed, $result_bmh);
                $result_bmh = str_replace('%m', $bmh->result_moved, $result_bmh);
                $result_bmh = str_replace('%d', $bmh->result_deleted, $result_bmh);
            }
            redirect_header($currentFile, 3, $result_bmh);
        } else {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_BMH_ERROR_NO_ACTIVE);
        }
        break;
    case 'list':
    default:
        $adminObject->displayNavigation(basename(__FILE__));
        $adminObject->addItemButton(_AM_XNEWSLETTER_RUNBMH, '?op=run_bmh', 'add');
        $adminObject->displayButton('left');

        $arr_measure_type = [
            _XNEWSLETTER_BMH_MEASURE_VAL_ALL     => _AM_XNEWSLETTER_BMH_MEASURE_ALL,
            _XNEWSLETTER_BMH_MEASURE_VAL_PENDING => _AM_XNEWSLETTER_BMH_MEASURE_PENDING,
            _XNEWSLETTER_BMH_MEASURE_VAL_NOTHING => _AM_XNEWSLETTER_BMH_MEASURE_NOTHING,
            _XNEWSLETTER_BMH_MEASURE_VAL_QUIT    => _AM_XNEWSLETTER_BMH_MEASURE_QUITED,
            _XNEWSLETTER_BMH_MEASURE_VAL_DELETE  => _AM_XNEWSLETTER_BMH_MEASURE_DELETED,
        ];

        $limit       = $helper->getConfig('adminperpage');
        $bhmCriteria = new \CriteriaCompo();
        if ($filter > -1) {
            $bhmCriteria->add(new \Criteria('bmh_measure', $filter));
        }
        $bhmCriteria->setSort('bmh_id');
        $bhmCriteria->setOrder('DESC');
        $bhmCount = $helper->getHandler('Bmh')->getCount($bhmCriteria);
        $start    = \Xmf\Request::getInt('start', 0);
        $bhmCriteria->setStart($start);
        $bhmCriteria->setLimit($limit);
        $bhmObjs = $helper->getHandler('Bmh')->getAll($bhmCriteria);
        if ($bhmCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($bhmCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        //form to filter result
        echo "<table class='outer width100' cellspacing='1'><tr class='odd'><td>";
        echo "<form id='form_filter' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_filter'>";

        $checked = (-1 == $filter) ? 'checked' : '';
        echo "<input id='bmh_measure_all' type='radio' {$checked} value='-1' title='" . _AM_XNEWSLETTER_BMH_MEASURE . "' name='bmh_measure_filter' onclick='submit()'>
            <label for='bmh_measure_all' name='bmh_measure_all'>" . _AM_XNEWSLETTER_BMH_MEASURE_ALL . '</label>';

        $checked = (_XNEWSLETTER_BMH_MEASURE_VAL_PENDING == $filter) ? 'checked' : '';
        echo "<input id='bmh_measure0' type='radio' {$checked} value='" . _XNEWSLETTER_BMH_MEASURE_VAL_PENDING . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE . "' name='bmh_measure_filter' onclick='submit()'>
            <label for='bmh_measure0' name='bmh_measure0'>" . _AM_XNEWSLETTER_BMH_MEASURE_PENDING . '</label>';

        $checked = (_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING == $filter) ? 'checked' : '';
        echo "<input id='bmh_measure1' type='radio' {$checked} value='" . _XNEWSLETTER_BMH_MEASURE_VAL_NOTHING . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE . "' name='bmh_measure_filter' onclick='submit()'>
            <label for='bmh_measure1' name='bmh_measure1'>" . _AM_XNEWSLETTER_BMH_MEASURE_NOTHING . '</label>';

        $checked = (_XNEWSLETTER_BMH_MEASURE_VAL_QUIT == $filter) ? 'checked' : '';
        echo "<input id='bmh_measure2' type='radio' {$checked} value='" . _XNEWSLETTER_BMH_MEASURE_VAL_QUIT . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE . "' name='bmh_measure_filter' onclick='submit()'>
            <label for='bmh_measure2' name='bmh_measure2'>" . _AM_XNEWSLETTER_BMH_MEASURE_QUITED . '</label>';

        $checked = (_XNEWSLETTER_BMH_MEASURE_VAL_DELETE == $filter) ? 'checked' : '';
        echo "<input id='bmh_measure3' type='radio' {$checked} value='" . _XNEWSLETTER_BMH_MEASURE_VAL_DELETE . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE . "' name='bmh_measure_filter' onclick='submit()'>
            <label for='bmh_measure3' name='bmh_measure3'>" . _AM_XNEWSLETTER_BMH_MEASURE_DELETED . '</label>';
        echo '</form>';
        echo '</td></tr></table>';

        // View Table
        echo "<table class='outer width100' cellspacing='1'>
            <tr>
                <th>" . _AM_XNEWSLETTER_BMH_ID . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_RULE_NO . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_RULE_CAT . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_BOUNCETYPE . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_REMOVE . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_EMAIL . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_MEASURE . '</th>
                <th>' . _AM_XNEWSLETTER_BMH_CREATED . '</th>
                <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>
            </tr>';

        if ($bhmCount > 0) {
            $class = 'odd';
            foreach ($bhmObjs as $bhm_id => $bhmObj) {
                echo "<tr class='{$class}'>";
                $class = ('even' === $class) ? 'odd' : 'even';
                echo '<td>' . $bhm_id . '</td>';
                echo '<td>' . $bhmObj->getVar('bmh_rule_no') . '</td>';
                echo '<td>' . $bhmObj->getVar('bmh_rule_cat') . '</td>';
                echo '<td>' . $bhmObj->getVar('bmh_bouncetype') . '</td>';

                $verif_bmh_remove = ('0' == $bhmObj->getVar('bmh_remove')) ? ' ' : $bhmObj->getVar('bmh_remove');
                echo '<td>' . $verif_bmh_remove . '</td>';
                echo '<td>' . $bhmObj->getVar('bmh_email') . '</td>';

                echo "<td class='center'>" . $arr_measure_type[$bhmObj->getVar('bmh_measure')] . '</td>';
                echo "<td class='center'>" . formatTimestamp($bhmObj->getVar('bmh_created'), 'S') . '</td>';

                echo "<td class='center width20'>";
                echo "    <a href='?op=handle_bmh&bmh_id="
                     . $bhm_id
                     . '&bmh_measure='
                     . _XNEWSLETTER_BMH_MEASURE_VAL_NOTHING
                     . '&filter='
                     . $filter
                     . "'><img src="
                     . XNEWSLETTER_ICONS_URL
                     . "/xn_nothing.png alt='"
                     . _AM_XNEWSLETTER_BMH_MEASURE_NOTHING
                     . "' title='"
                     . _AM_XNEWSLETTER_BMH_MEASURE_NOTHING
                     . "'></a>";
                echo "    <a href='?op=handle_bmh&bmh_id="
                     . $bhm_id
                     . '&bmh_measure='
                     . _XNEWSLETTER_BMH_MEASURE_VAL_QUIT
                     . '&filter='
                     . $filter
                     . "'><img src="
                     . XNEWSLETTER_ICONS_URL
                     . "/xn_catsubscr_temp.png alt='"
                     . _AM_XNEWSLETTER_BMH_MEASURE_QUIT
                     . "' title='"
                     . _AM_XNEWSLETTER_BMH_MEASURE_QUIT
                     . "'></a>";
                echo "    <a href='?op=bmh_delsubscr&bmh_id=" . $bhm_id . '&filter=' . $filter . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_quit.png alt='" . _AM_XNEWSLETTER_BMH_MEASURE_DELETE . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE_DELETE . "'></a>";
                echo "    <a href='?op=edit_bmh&bmh_id=" . $bhm_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _AM_XNEWSLETTER_BMH_EDIT . "' title='" . _AM_XNEWSLETTER_BMH_EDIT . "' width='16px'></a>";
                echo "    <a href='?op=delete_bmh&bmh_id=" . $bhm_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _AM_XNEWSLETTER_BMH_DELETE . "' title='" . _AM_XNEWSLETTER_BMH_DELETE . "' width='16px'></a>";
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr>';
            echo "    <td class='even' colspan='10'>" . sprintf(_AM_XNEWSLETTER_BMH_MEASURE_SHOW_NONE, $arr_measure_type[$filter]) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<br>';
        echo "<div class='center'>" . $pagenav . '</div>';
        echo '<br>';
        break;
    case 'save_bmh':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $bmhObj = $helper->getHandler('Bmh')->get($bmh_id);
        $bmhObj->setVar('bmh_rule_no', \Xmf\Request::getString('bmh_rule_no', ''));
        $bmhObj->setVar('bmh_rule_cat', \Xmf\Request::getString('bmh_rule_cat', ''));
        $bmhObj->setVar('bmh_bouncetype', \Xmf\Request::getString('bmh_bouncetype', ''));
        $bmhObj->setVar('bmh_remove', \Xmf\Request::getString('bmh_remove', ''));
        $bmh_email = \Xmf\Request::getString('bmh_email', '');
        $bmh_email = filter_var($bmh_email, FILTER_SANITIZE_EMAIL);
        $bmh_email = xnewsletter_checkEmail($bmh_email);
        $bmhObj->setVar('bmh_email', $bmh_email);
        $bmhObj->setVar('bmh_subject', \Xmf\Request::getString('bmh_subject', ''));
        $bmhObj->setVar('bmh_measure', \Xmf\Request::getInt('bmh_measure', 0));
        $bmhObj->setVar('bmh_submitter', \Xmf\Request::getInt('bmh_submitter', 0));
        $bmhObj->setVar('bmh_created', \Xmf\Request::getInt('bmh_created', 0));

        if ($helper->getHandler('Bmh')->insert($bmhObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }
        echo $bmhObj->getHtmlErrors();
        $form = $bmhObj->getForm();
        $form->display();
        break;
    case 'edit_bmh':
        $adminObject->displayNavigation(basename(__FILE__));
        $adminObject->addItemButton(_AM_XNEWSLETTER_BMHLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $bmhObj = $helper->getHandler('Bmh')->get($bmh_id);
        $form   = $bmhObj->getForm();
        $form->display();
        break;
    case 'delete_bmh':
        $bmhObj = $helper->getHandler('Bmh')->get($bmh_id);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Bmh')->delete($bmhObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $bmhObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'bmh_id' => $bmh_id, 'op' => 'delete_bmh'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $bmhObj->getVar('bmh_rule_no')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';

/**
 * @return float
 */
function microtime_float()
{
    list($usec, $sec) = explode(' ', microtime());

    return ((float)$usec + (float)$sec);
}
