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
 *  @license    GNU General Public License 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once dirname(__FILE__) . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op = xnewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');
$bmh_id         = xnewsletter_CleanVars($_REQUEST, 'bmh_id', 0, 'int');
$bmh_measure    = xnewsletter_CleanVars($_REQUEST, 'bmh_measure', 0, 'int');
$filter         = xnewsletter_CleanVars($_REQUEST, 'bmh_measure_filter', _AM_XNEWSLETTER_BMH_MEASURE_VAL_ALL, 'int');

switch ($op) {
    case "bmh_delsubscr":
        if ((isset($_POST["ok"]) && $_POST["ok"] == 1)) {
            $count_err = 0;

            $bmhObj = $xnewsletter->getHandler('bmh')->get($bmh_id);
            $bmh_email = $bmhObj->getVar("bmh_email");

            $sql = "SELECT subscr_id FROM " . $xoopsDB->prefix("xnewsletter_subscr") . " WHERE (";
            $sql .= "subscr_email='" . $bmh_email . "'";
            $sql .= ") LIMIT 1;";
            if ( $user = $xoopsDB->query($sql) ) {
                $row_user = $xoopsDB->fetchRow($user);
                $subscr_id = intval($row_user[0]);
            }
            if ($subscr_id == 0) {
                //set bmh_measure for all entries in bmh with this email
                $sql_upd_measure = "UPDATE " . $xoopsDB->prefix("xnewsletter_bmh") . " SET `bmh_measure` = '" . _AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING . "'";
                $sql_upd_measure .=" WHERE ((`" . $xoopsDB->prefix("xnewsletter_bmh") . "`.`bmh_email` ='" . $bmh_email . "') AND (`" . $xoopsDB->prefix("xnewsletter_bmh") . "`.`bmh_measure` ='0'))";
                $xoopsDB->query($sql_upd_measure);
                redirect_header("?op=list", 5, _AM_XNEWSLETTER_BMH_ERROR_NO_SUBSCRID);
            }
            $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);

            // delete subscriber
            if (!$xnewsletter->getHandler('subscr')->delete($subscrObj,true)) {
                $actionprot_err = $subscrObj->getHtmlErrors()."<br/><br/><br/>";
                ++$count_err;
            }

            //delete subscription
            $catsubscrCriteria = new CriteriaCompo();
            $catsubscrCriteria->add(new Criteria('catsubscr_subscrid', $subscr_id));
            $catsubscrsCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
            if ($catsubscrsCount > 0) {
                $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
                foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                    $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($catsubscrObj->getVar("catsubscr_id"));
                    $catObj = $xnewsletter->getHandler('cat')->get($catsubscrObj->getVar("catsubscr_catid"));
                    $cat_mailinglist = $catObj->getVar("cat_mailinglist");

                    if ($xnewsletter->getHandler('catsubscr')->delete($catsubscrObj, true)) {
                        //handle mailinglists
                        if ($cat_mailinglist > 0) {
                            require_once( XOOPS_ROOT_PATH . "/modules/xnewsletter/include/mailinglist.php" );
                            subscribingMLHandler(0, $subscr_id, $cat_mailinglist);
                        }
                    } else {
                        $actionprot_err .= $catsubscrObj->getHtmlErrors();
                        ++$count_err;
                    }
                }
            }

            if ($count_err == 0) {
                redirect_header("?op=handle_bmh&bmh_id=".$bmh_id."&bmh_measure="._AM_XNEWSLETTER_BMH_MEASURE_VAL_DELETE."&filter=".$filter, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $actionprot_err;
            }
        } else {
            xoops_confirm(array("ok" => 1, "bmh_id" => $bmh_id, "op" => "bmh_delsubscr", "filter" => $filter), $currentFile, sprintf(_AM_XNEWSLETTER_BMH_MEASURE_DELETE_SURE));
        }
    break;

    case "handle_bmh":
        if ($bmh_id == 0) redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
        if ($bmh_measure == 0) redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);

        $bmhObj = $xnewsletter->getHandler('bmh')->get($bmh_id);

        if ($bmhObj->getVar("bmh_measure") == _AM_XNEWSLETTER_BMH_MEASURE_VAL_DELETE ) {
            redirect_header("?op=list&filter=".$filter."'", 3, _AM_XNEWSLETTER_BMH_MEASURE_ALREADY_DELETED);
        }

        $bmh_email = $bmhObj->getVar("bmh_email");

        if ($bmh_measure == _AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT) {
            $sql = "UPDATE `".$xoopsDB->prefix("xnewsletter_subscr")."` INNER JOIN `";
            $sql .= $xoopsDB->prefix("xnewsletter_catsubscr")."` ON `subscr_id` = `catsubscr_subscrid` ";
            $sql .= "SET `catsubscr_quited` = ".time()." WHERE (((`subscr_email`)='";
            $sql .= $bmh_email. "'))";
            if(!$result = $xoopsDB->queryF($sql)) die ("MySQL-Error: " . mysql_error());
        }
        //set bmh_measure for all entries in bmh with this email
        $sql_upd = "UPDATE ".$xoopsDB->prefix("xnewsletter_bmh")." SET ";
        $sql_upd .="`bmh_measure` = '".$bmh_measure."'";
        $sql_upd .=", `bmh_submitter` = '".$xoopsUser->uid()."'";
        $sql_upd .=", `bmh_created` = '".time()."'";
        $sql_upd .=" WHERE ((`".$xoopsDB->prefix("xnewsletter_bmh")."`.`bmh_email` ='".$bmh_email."') AND (`".$xoopsDB->prefix("xnewsletter_bmh")."`.`bmh_measure` ='0'))";
        if(!$result = $xoopsDB->queryF($sql_upd)) die ("MySQL-Error: " . mysql_error());

        redirect_header("?op=list&filter=".$filter, 3, _AM_XNEWSLETTER_FORMOK);

        echo $bmhObj->getHtmlErrors();
    break;

    case "run_bmh":
        require_once('bmh_callback_database.php');
        require_once(XOOPS_ROOT_PATH . '/modules/xnewsletter/include/phpmailer_bmh/class.phpmailer-bmh.php');

        $accountCriteria = new CriteriaCompo();
        $accountCriteria->add(new Criteria("accounts_use_bmh", "1"));
        $accountsCount = $xnewsletter->getHandler('accounts')->getCount($accountCriteria);

        if ($accountsCount > 0) {
            $accountObjs = $xnewsletter->getHandler('accounts')->getAll($accountCriteria);
            $result_bmh = _AM_XNEWSLETTER_BMH_SUCCESSFUL."<br/>";

            foreach ($accountObjs as $account_id => $accountObj) {
                $bmh = new BounceMailHandler();
                $bmh->verbose            = VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
                //$bmh->use_fetchstructure = true; // true is default, no need to speficy
                //$bmh->testmode           = true; // false is default, no need to specify
                //$bmh->debug_body_rule    = false; // false is default, no need to specify
                //$bmh->debug_dsn_rule     = false; // false is default, no need to specify
                //$bmh->purge_unprocessed  = false; // false is default, no need to specify
                $bmh->disable_delete     = true; // detected mails will be not deleted, default is false

                // for local mailbox (to process .EML files)
                //$bmh->openLocalDirectory('/home/email/temp/mailbox');
                //$bmh->processMailbox();

                // for remote mailbox
                $bmh->mailhost          = $accountObj->getVar("accounts_server_in"); // your mail server
                $bmh->mailbox_username  = $accountObj->getVar("accounts_username"); // your mailbox username
                $bmh->mailbox_password  = $accountObj->getVar("accounts_password"); // your mailbox password
                $bmh->port              = $accountObj->getVar("accounts_port_in"); // the port to access your mailbox, default is 143
                if ($accountObj->getVar("accounts_type") == _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3) {
                    $bmh->service           = 'pop3'; // the service to use (imap or pop3), default is 'imap'
                } else {
                    $bmh->service           = 'imap'; // the service to use (imap or pop3), default is 'imap'
                }
                $bmh->service_option    = $accountObj->getVar("accounts_securetype_in"); // the service options (none, tls, notls, ssl, etc.), default is 'notls'
                $bmh->boxname           = $accountObj->getVar("accounts_inbox"); // the mailbox to access, default is 'INBOX'
                $verif_movehard         = $accountObj->getVar("accounts_movehard") == '1' ? true : false;
                $bmh->moveHard          = $verif_movehard; // default is false
                $bmh->hardMailbox       = $accountObj->getVar("accounts_hardbox"); // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
                $verif_movesoft         = $accountObj->getVar("accounts_movesoft") == '1' ? true : false;
                $bmh->moveSoft          = $verif_movesoft; // default is false
                $bmh->softMailbox       = $accountObj->getVar("accounts_softbox"); // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
                //$bmh->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'

                // rest used regardless what type of connection it is
                $bmh->openMailbox();
                $bmh->processMailbox();

                $result_bmh .= str_replace("%b", $accountObj->getVar("accounts_yourmail"), _AM_XNEWSLETTER_BMH_RSLT);
                $result_bmh = str_replace("%r", $bmh->result_total, $result_bmh);
                $result_bmh = str_replace("%a", $bmh->result_processed, $result_bmh);
                $result_bmh = str_replace("%n", $bmh->result_unprocessed, $result_bmh);
                $result_bmh = str_replace("%m", $bmh->result_moved, $result_bmh);
                $result_bmh = str_replace("%d", $bmh->result_deleted, $result_bmh);
            }
            redirect_header($currentFile, 5, $result_bmh);
        } else {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_BMH_ERROR_NO_ACTIVE);
        }
    break;

    case "list":
    default:
        echo $indexAdmin->addNavigation('bmh.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_RUNBMH, '?op=run_bmh', 'add');
        echo $indexAdmin->renderButton();
        //
        $arr_measure_type = array(
            _AM_XNEWSLETTER_BMH_MEASURE_VAL_ALL=>_AM_XNEWSLETTER_BMH_MEASURE_ALL,
            _AM_XNEWSLETTER_BMH_MEASURE_VAL_PENDING=>_AM_XNEWSLETTER_BMH_MEASURE_PENDING,
            _AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING=>_AM_XNEWSLETTER_BMH_MEASURE_NOTHING,
            _AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT=>_AM_XNEWSLETTER_BMH_MEASURE_QUITED,
            _AM_XNEWSLETTER_BMH_MEASURE_VAL_DELETE=>_AM_XNEWSLETTER_BMH_MEASURE_DELETED);

        $limit = $xnewsletter->getConfig('adminperpage');
        $bhmCriteria = new CriteriaCompo();
        if ($filter > -1) $criteria->add(new Criteria("bmh_measure", $filter));
        $bhmCriteria->setSort("bmh_id");
        $bhmCriteria->setOrder("DESC");
        $bhmsCount = $xnewsletter->getHandler('bmh')->getCount($bhmCriteria);
        $start = xnewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $bhmCriteria->setStart($start);
        $bhmCriteria->setLimit($limit);
        $bhmObjs = $xnewsletter->getHandler('bmh')->getAll($bhmCriteria);
        if ($bhmsCount > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($bhmsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        //form to filter result
        echo "<table class='outer width100' cellspacing='1'><tr class='odd'><td>";
        echo "<form id='form_filter' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_filter'>";

        $checked = ($filter == -1)  ? "checked='checked'" : "";
        echo "<input id='bmh_measure_all' type='radio' $checked value='-1' title='"._AM_XNEWSLETTER_BMH_MEASURE."' name='bmh_measure_filter' onclick='submit()' />
            <label for='bmh_measure_all' name='bmh_measure_all'>"._AM_XNEWSLETTER_BMH_MEASURE_ALL."</label>";

        $checked = ($filter == _AM_XNEWSLETTER_BMH_MEASURE_VAL_PENDING)  ? "checked='checked'" : "";
        echo "<input id='bmh_measure0' type='radio' $checked value='"._AM_XNEWSLETTER_BMH_MEASURE_VAL_PENDING."' title='"._AM_XNEWSLETTER_BMH_MEASURE."' name='bmh_measure_filter'  onclick='submit()' />
            <label for='bmh_measure0' name='bmh_measure0'>"._AM_XNEWSLETTER_BMH_MEASURE_PENDING."</label>";

        $checked = ($filter == _AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING)  ? "checked='checked'" : "";
        echo "<input id='bmh_measure1' type='radio' $checked value='"._AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING."' title='"._AM_XNEWSLETTER_BMH_MEASURE."' name='bmh_measure_filter'  onclick='submit()' />
            <label for='bmh_measure1' name='bmh_measure1'>"._AM_XNEWSLETTER_BMH_MEASURE_NOTHING."</label>";

        $checked = ($filter == _AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT)  ? "checked='checked'" : "";
        echo "<input id='bmh_measure2' type='radio' $checked value='"._AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT."' title='"._AM_XNEWSLETTER_BMH_MEASURE."' name='bmh_measure_filter'  onclick='submit()'>
            <label for='bmh_measure2' name='bmh_measure2'>"._AM_XNEWSLETTER_BMH_MEASURE_QUITED."</label>";

        $checked = ($filter == _AM_XNEWSLETTER_BMH_MEASURE_VAL_DELETE)  ? "checked='checked'" : "";
        echo "<input id='bmh_measure3' type='radio' $checked value='"._AM_XNEWSLETTER_BMH_MEASURE_VAL_DELETE."' title='"._AM_XNEWSLETTER_BMH_MEASURE."' name='bmh_measure_filter'  onclick='submit()' />
            <label for='bmh_measure3' name='bmh_measure3'>"._AM_XNEWSLETTER_BMH_MEASURE_DELETED."</label>";
        echo "</form>";
        echo "</td></tr></table>";

        // View Table
        if ($bhmsCount > 0) {
            echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_BMH_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_RULE_NO . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_RULE_CAT . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_BOUNCETYPE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_REMOVE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_MEASURE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_CREATED . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>";

            $class = "odd";

            foreach ($bhmObjs as $bhm_id => $bhmObj) {
                echo "<tr class='".$class."'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $bhm_id . "</td>";
                echo "<td class='center'>" . $bhmObj->getVar("bmh_rule_no")."</td>";
                echo "<td class='center'>" . $bhmObj->getVar("bmh_rule_cat")."</td>";
                echo "<td class='center'>" . $bhmObj->getVar("bmh_bouncetype")."</td>";

                $verif_bmh_remove = ( $bhmObj->getVar("bmh_remove") == "0" ) ? ' ' : $bhmObj->getVar("bmh_remove");
                echo "<td class='center'>" . $verif_bmh_remove . "</td>";
                echo "<td class='center'>" . $bhmObj->getVar("bmh_email") . "</td>";

                echo "<td class='center'>" . $arr_measure_type[$bhmObj->getVar("bmh_measure")] . "</td>";
                echo "<td class='center'>" . formatTimeStamp($bhmObj->getVar("bmh_created"),"S") . "</td>";

                echo "<td class='center width20'>
                    <a href='?op=handle_bmh&bmh_id=" . $bhm_id . "&bmh_measure=" . _AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING . "&filter=" . $filter . "'>
                        <img src=" . XNEWSLETTER_ICONS_URL . "/xn_nothing.png alt='" . _AM_XNEWSLETTER_BMH_MEASURE_NOTHING . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE_NOTHING . "' />
                    </a>
                    <a href='?op=handle_bmh&bmh_id=". $bhm_id . "&bmh_measure=" . _AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT . "&filter=" . $filter . "'>
                        <img src=" . XNEWSLETTER_ICONS_URL . "/xn_catsubscr_temp.png alt='" . _AM_XNEWSLETTER_BMH_MEASURE_QUIT . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE_QUIT . "' />
                    </a>
                    <a href='?op=bmh_delsubscr&bmh_id=".$bhm_id."&filter=".$filter . "'>
                        <img src=" . XNEWSLETTER_ICONS_URL . "/xn_quit.png alt='" . _AM_XNEWSLETTER_BMH_MEASURE_DELETE . "' title='" . _AM_XNEWSLETTER_BMH_MEASURE_DELETE . "' />
                    </a>
                    <a href='?op=edit_bmh&bmh_id=" . $bhm_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _AM_XNEWSLETTER_BMH_EDIT . "' title='" . _AM_XNEWSLETTER_BMH_EDIT . "' width='16px' /></a>
                    <a href='?op=delete_bmh&bmh_id=" . $bhm_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _AM_XNEWSLETTER_BMH_DELETE . "' title='" . _AM_XNEWSLETTER_BMH_DELETE . "' width='16px' /></a>
                    </td>";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "
                <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_BMH_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_RULE_NO . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_RULE_CAT . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_BOUNCETYPE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_REMOVE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_SUBJECT . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_MEASURE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_BMH_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>
                <tr>
                    <td class='even' colspan='10'>" . sprintf(_AM_XNEWSLETTER_BMH_MEASURE_SHOW_NONE, $arr_measure_type[$filter]) . "</td>
                </tr>";
        echo "</table><br />";
        }
    break;

    case "save_bmh":
        if (!$GLOBALS["xoopsSecurity"]->check()) {
            redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }

        $bmhObj = $xnewsletter->getHandler('bmh')->get($bmh_id);
        $bmhObj->setVar("bmh_rule_no", xnewsletter_CleanVars($_REQUEST, "bmh_rule_no", "", "string"));
        $bmhObj->setVar("bmh_rule_cat", xnewsletter_CleanVars($_REQUEST, "bmh_rule_cat", "", "string"));
        $bmhObj->setVar("bmh_bouncetype", xnewsletter_CleanVars($_REQUEST, "bmh_bouncetype", "", "string"));
        $bmhObj->setVar("bmh_remove", xnewsletter_CleanVars($_REQUEST, "bmh_remove", "", "string"));
        $bmhObj->setVar("bmh_email", xnewsletter_CleanVars($_REQUEST, "bmh_email", "", "email"));
        $bmhObj->setVar("bmh_subject", xnewsletter_CleanVars($_REQUEST, "bmh_subject", "", "string"));
        $bmhObj->setVar("bmh_measure", xnewsletter_CleanVars($_REQUEST, "bmh_measure", 0, "int"));
        $bmhObj->setVar("bmh_submitter", xnewsletter_CleanVars($_REQUEST, "bmh_submitter", 0, "int"));
        $bmhObj->setVar("bmh_created", xnewsletter_CleanVars($_REQUEST, "bmh_created", 0, "int"));

        if ($xnewsletter->getHandler('bmh')->insert($bmhObj)) {
            redirect_header("?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }
        echo $bmhObj->getHtmlErrors();
        $form = $bmhObj->getForm();
        $form->display();
    break;

    case "edit_bmh":
        echo $indexAdmin->addNavigation('bmh.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_BMHLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $bmhObj = $xnewsletter->getHandler('bmh')->get($bmh_id);
        $form = $bmhObj->getForm();
        $form->display();
    break;

    case "delete_bmh":
        $bmhObj = $xnewsletter->getHandler('bmh')->get($bmh_id);
        if (isset($_POST["ok"]) && $_POST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('bmh')->delete($bmhObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $bmhObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "bmh_id" => $bmh_id, "op" => "delete_bmh"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $bmhObj->getVar("bmh_rule_no")));
        }
    break;
}
include_once dirname(__FILE__) . '/admin_footer.php';

/**
 * @return float
 */
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());

    return ((float) $usec + (float) $sec);
}
