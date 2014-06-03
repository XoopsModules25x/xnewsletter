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
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

include "admin_header.php";
xoops_cp_header();

//global $pathIcon, $indexAdmin;
// We recovered the value of the argument op in the URL$
$op = xNewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');

switch ($op) {
    case "list":
    default:
        echo $indexAdmin->addNavigation('maintenance.php');
        include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
        echo "<table class='outer width75'>";
        echo "<tr>
                      <th class='center'>"._AM_XNEWSLETTER_MAINTENANCE_CAT."</th>
                        <th class='center'>"._AM_XNEWSLETTER_MAINTENANCE_DESCR."</th>
                        <th class='center'>"._AM_XNEWSLETTER_MAINTENANCE_PARAM."</th>
                        <th class='center'>"._AM_XNEWSLETTER_FORMACTION."</th>
                    </tr>";
        $class = "odd";

        // delete protocols
        echo "   <tr class='{$class}'>";
        $class = ($class == "even") ? "odd" : "even";
        echo "        <form action='maintenance.php' method='post'>";
        echo "            <td align='center' valign='middle'>prot</td>";
        echo "            <td align='left' valign='middle'>" . _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOCOL . "</td>";
        echo "            <td valign='middle' align='center'>&nbsp;";
        echo "            </td><td valign='middle' align='left'>";
        $cal_tray = new XoopsFormElementTray(" ",'&nbsp;&nbsp;');
        $cal_tray->addElement(new XoopsFormHidden("op", "del_oldprotocol"));
        $cal_tray->addElement(new XoopsFormButton("", "post", _SUBMIT, "submit"));
        echo $cal_tray->render();
        echo "            </td>";
        echo "        </form>";
        echo "    </tr>";

        // delete unconfirmed registrations
        echo "   <tr class='{$class}'>";
        $class = ($class == "even") ? "odd" : "even";
        echo "        <form action='maintenance.php' method='post'>";
        echo "            <td align='center' valign='middle'>reguser</td>";
        echo "            <td align='left' valign='middle'>" . _AM_XNEWSLETTER_MAINTENANCE_DELETEDATE . "</td>";
        echo "            <td valign='middle' align='center'>";
        $cal = new XoopsFormTextDateSelect('', 'del_date', 15, time() - (84600 * 10));
        echo $cal->render();
        echo "            </td><td valign='middle' align='left'>";
        $cal_tray = new XoopsFormElementTray(" ",'&nbsp;&nbsp;');
        $cal_tray->addElement(new XoopsFormHidden("op", "del_olduser"));
        $cal_tray->addElement(new XoopsFormButton("", "post", _SUBMIT, "submit"));
        echo $cal_tray->render();
        echo "            </td>";
        echo "        </form>";
        echo "    </tr>";

        // delete invalid catsubscr
        echo "   <tr class='{$class}'>";
        $class = ($class == "even") ? "odd" : "even";
        echo "        <form action='maintenance.php' method='post'>";
        echo "            <td align='center' valign='middle'>catsubscr</td>";
        echo "            <td align='left' valign='middle'>" . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR . "</td>";
        echo "            <td valign='middle' align='center'>&nbsp;";
        echo "            </td><td valign='middle' align='left'>";
        $cal_tray = new XoopsFormElementTray(" ",'&nbsp;&nbsp;');
        $cal_tray->addElement(new XoopsFormHidden("op", "del_invalid_catsubscr"));
        $cal_tray->addElement(new XoopsFormButton("", "post", _SUBMIT, "submit"));
        echo $cal_tray->render();
        echo "            </td>";
        echo "        </form>";
        echo "    </tr>";

        // check module preference xn_use_mailinglist with values in cat_mailinglist and check cat_mailinglist versus table mailinglist

        if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
            echo "   <tr class='{$class}'>";
            $class = ($class == "even") ? "odd" : "even";
            echo "        <form action='maintenance.php' method='post'>";
            echo "            <td align='center' valign='middle'>ml</td>";
            echo "            <td align='left' valign='middle'>" . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML . "</td>";
            echo "            <td valign='middle' align='center'>&nbsp;";
            echo "            </td><td valign='middle' align='left'>";
            $cal_tray = new XoopsFormElementTray(" ",'&nbsp;&nbsp;');
            $cal_tray->addElement(new XoopsFormHidden("op", "del_invalid_ml"));
            $cal_tray->addElement(new XoopsFormButton("", "post", _SUBMIT, "submit"));
            echo $cal_tray->render();
            echo "            </td>";
            echo "        </form>";
            echo "    </tr>";
        }

        // delete invalid cat
        echo "   <tr class='{$class}'>";
        $class = ($class == "even") ? "odd" : "even";
        echo "        <form action='maintenance.php' method='post'>";
        echo "            <td align='center' valign='middle'>cat</td>";
        echo "            <td align='left' valign='middle'>" . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL . "</td>";
        echo "            <td valign='middle' align='center'>&nbsp;";
        echo "            </td><td valign='middle' align='left'>";
        $cal_tray = new XoopsFormElementTray(" ",'&nbsp;&nbsp;');
        $cal_tray->addElement(new XoopsFormHidden("op", "del_invalid_cat"));
        $cal_tray->addElement(new XoopsFormButton("", "post", _SUBMIT, "submit"));
        echo $cal_tray->render();
        echo "            </td>";
        echo "        </form>";
        echo "    </tr>";

        // delete import
        echo "   <tr class='{$class}'>";
        $class = ($class == "even") ? "odd" : "even";
        echo "        <form action='maintenance.php' method='post'>";
        echo "            <td align='center' valign='middle'>import</td>";
        echo "            <td align='left' valign='middle'>" . _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT . "</td>";
        echo "            <td valign='middle' align='center'>&nbsp;";
        echo "            </td><td valign='middle' align='left'>";
        $cal_tray = new XoopsFormElementTray(" ",'&nbsp;&nbsp;');
        $cal_tray->addElement(new XoopsFormHidden("op", "del_import"));
        $cal_tray->addElement(new XoopsFormButton("", "post", _SUBMIT, "submit"));
        echo $cal_tray->render();
        echo "            </td>";
        echo "        </form>";
        echo "    </tr>";

        echo "</table>";
        break;

    case 'del_import':
        if (isset($_POST["ok"]) && $_POST["ok"] == "1") {
            $sql = "TRUNCATE TABLE `{$xoopsDB->prefix('xnewsletter_import')}`";
            $result = $xoopsDB->queryF($sql);
            $sql = "REPAIR TABLE `{$xoopsDB->prefix('xnewsletter_import')}`";
            $result = $xoopsDB->queryF($sql);
            $sql = "OPTIMIZE TABLE `{$xoopsDB->prefix('xnewsletter_import')}`";
            $result = $xoopsDB->queryF($sql);
            $sql = "ALTER TABLE `{$xoopsDB->prefix('xnewsletter_import')}` AUTO_INCREMENT =1";
            $result = $xoopsDB->queryF($sql);

            $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
            //Form protocol_letter_id
            $obj->setVar("protocol_letter_id", 0);
            //Form protocol_subscriber_id
            $obj->setVar("protocol_subscriber_id", 0);
            //Form protocol_status
            $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " import] " . _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT_OK);
            //Form protocol_success
            $obj->setVar("protocol_success", 1);
            //Form protocol_submitter
            $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
            //Form protocol_created
            $obj->setVar("protocol_created", time());

            if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
            }
            redirect_header("maintenance.php", 2,_AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT_OK);
        } else {
            xoops_confirm(array("ok" => 1, "", "op" => "del_import"), "maintenance.php", _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT);
        }
        break;

    case 'del_olduser':
        $time = strtotime($_POST['del_date']);
        if ( $time >= time() || $time == 0) {
            $numrows = -1; //for error
        } else {
            $criteria = new CriteriaCompo();
            $criteria->add( new Criteria('subscr_activated', 0) );
            $criteria->add( new Criteria('subscr_created', $time, '<') );
            $numrows = $xnewsletter->getHandler('xNewsletter_subscr')->getCount($criteria);
        }

        if (isset($_POST["ok"]) && $_POST["ok"] == "1") {
            $delete = 0;
            $error = array();
            $delusers = $xnewsletter->getHandler('xNewsletter_subscr')->getall($criteria, array('subscr_id'), false, false);
            foreach ($delusers as $id => $user) {
                $obj =& $xnewsletter->getHandler('xNewsletter_subscr')->get(intval($user['subscr_id']));
                $sql = "DELETE FROM `{$xoopsDB->prefix('xnewsletter_subscr')}` WHERE subscr_id={$user['subscr_id']}";
                $result = $xoopsDB->queryF($sql);
                if ($result) {
                    // Newsletterlist delete
                    $sql = "DELETE FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}` WHERE catsubscr_subscrid={$user['subscr_id']}";
                    $result = $xoopsDB->queryF($sql);
                    if (!$result) {
                        $error[] = "Error CAT-Subscr-ID: " . $user['subscr_id'] . " / " . $result->getHtmlErrors();
                    }
                    ++$delete;
                } else {
                    $error[] = "Error Subscr-ID: " . $user['subscr_id'] . " / " . $result->getHtmlErrors();
                }
            }

            if (count($error) > 0) {
                foreach ($error as $err =>$text) {
                    $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                    $obj->setVar("protocol_letter_id", 0);
                    $obj->setVar("protocol_subscriber_id", 0);
                    $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " reguser] " . $text);
                    $obj->setVar("protocol_success", 0);
                    $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                    $obj->setVar("protocol_created", time());
                    $xnewsletter->getHandler('xNewsletter_protocol')->insert($obj);
                }
            }

            if ($delete > 0) {
                $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                $obj->setVar("protocol_letter_id", 0);
                $obj->setVar("protocol_subscriber_id", 0);
                $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " reguser] " . sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETEUSEROK,$delete));
                $obj->setVar("protocol_success", 1);
                $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                $obj->setVar("protocol_created", time());
                $xnewsletter->getHandler('xNewsletter_protocol')->insert($obj);
            }

            redirect_header("maintenance.php", 2,sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETEUSEROK,$delete));

        } else {
            if ($numrows > 0) {
                xoops_confirm(array("ok" => 1, "del_date" => $_POST['del_date'], "op" => "del_olduser"), "maintenance.php", sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETEUSER, $numrows, $_POST['del_date']));
            } else {
                redirect_header("maintenance.php", 2,_AM_XNEWSLETTER_MAINTENANCE_DELETENOTHING);
            }
        }
        break;

    case 'del_oldprotocol':
        if (isset($_POST["ok"]) && $_POST["ok"] == "1") {
            $sql = "TRUNCATE TABLE `".$xoopsDB->prefix('xnewsletter_protocol')."`";
            $result = $xoopsDB->queryF($sql);
            $sql = "REPAIR TABLE `".$xoopsDB->prefix('xnewsletter_protocol')."`";
            $result = $xoopsDB->queryF($sql);
            $sql = "OPTIMIZE TABLE `".$xoopsDB->prefix('xnewsletter_protocol')."`";
            $result = $xoopsDB->queryF($sql);
            $sql = "ALTER TABLE `".$xoopsDB->prefix('xnewsletter_protocol')."` AUTO_INCREMENT =1";
            $result = $xoopsDB->queryF($sql);

            $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
            //Form protocol_letter_id
            $obj->setVar("protocol_letter_id", 0);
            //Form protocol_subscriber_id
            $obj->setVar("protocol_subscriber_id", 0);
            //Form protocol_status
            $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " prot] " . _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOK);
            //Form protocol_success
            $obj->setVar("protocol_success", 1);
            //Form protocol_submitter
            $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
            //Form protocol_created
            $obj->setVar("protocol_created", time());

            if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
            }
            redirect_header("maintenance.php", 2,_AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOK);
        } else {
            xoops_confirm(array("ok" => 1, "", "op" => "del_oldprotocol"), "maintenance.php", _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOCOL);
        }
        break;

    case 'del_invalid_catsubscr':
        //delete data in table catsubscr, if catsubscr_subscrid is no more existing in table subscr
        if (isset($_POST["ok"]) && $_POST["ok"] == "1") {
            $number_ids = 0;
            $delete = 0;
            $error = array();
            $sql = "SELECT Count(`catsubscr_id`) AS `nb_ids` FROM `".$xoopsDB->prefix("xnewsletter_catsubscr")."` LEFT JOIN `".$xoopsDB->prefix("xnewsletter_subscr")."` ON `catsubscr_subscrid` = `subscr_id` WHERE (`subscr_id` Is Null)";
            if ( $result = $xoopsDB->query($sql) ) {
                $row_result = $xoopsDB->fetchRow($result);
                $number_ids = $row_result[0];
            }
            if ($number_ids > 0) {
                $sql = "DELETE `".$xoopsDB->prefix("xnewsletter_catsubscr")."` FROM `".$xoopsDB->prefix("xnewsletter_catsubscr")."` LEFT JOIN `".$xoopsDB->prefix("xnewsletter_subscr")."` ON `catsubscr_subscrid` = `subscr_id` WHERE (`subscr_id` Is Null)";
                $result = $xoopsDB->query($sql);
                if ($result = $xoopsDB->query($sql)) {
                    ++$delete;
                } else {
                    $error[] = "Error delete catsubscr: " . $result->getHtmlErrors();
                }
            }

            if (count($error) > 0) {
                foreach ($error as $err =>$text) {
                    $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                    $obj->setVar("protocol_letter_id", 0);
                    $obj->setVar("protocol_subscriber_id", 0);
                    $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " catsubscr] " . $text);
                    $obj->setVar("protocol_success", 0);
                    $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                    $obj->setVar("protocol_created", time());
                    if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
                        echo _AM_XNEWSLETTER_MAINTENANCE_ERROR;
                    }
                }
            } else {
                $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                $obj->setVar("protocol_letter_id", 0);
                $obj->setVar("protocol_subscriber_id", 0);
                $status = $number_ids == 0 ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_NODATA :  sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK, $number_ids);
                $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " catsubscr] " . $status);
                $obj->setVar("protocol_success", 1);
                $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                $obj->setVar("protocol_created", time());

                if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
                    echo _AM_XNEWSLETTER_MAINTENANCE_ERROR;
                }
                redirect_header("maintenance.php", 3,sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK, $number_ids));
            }
        } else {
            xoops_confirm(array("ok" => 1, "", "op" => "del_invalid_catsubscr"), "maintenance.php", _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR);
        }
        break;

    case 'del_invalid_ml':
        if (isset($_POST["ok"]) && $_POST["ok"] == "1") {
            $use_mailinglist = $GLOBALS['xoopsModuleConfig']['xn_use_mailinglist'];
            $number_ids = 0;
            $update = 0;
            $error = array();
            if ($use_mailinglist == 0 || $use_mailinglist == '0') {
                //set cat_mailinglist = 0, if use mailinglist = false (if someone changed module preferences later)
                $sql = "SELECT Count(`cat_id`) AS `nb_ids` FROM `".$xoopsDB->prefix("xnewsletter_cat")."` WHERE (`cat_mailinglist` > 0)";
                if ( $result = $xoopsDB->query($sql) ) {
                    $row_result = $xoopsDB->fetchRow($result);
                    $number_ids = $row_result[0];
                }
                if ($number_ids > 0) {
                    $sql = "UPDATE `".$xoopsDB->prefix("xnewsletter_cat")."` SET `cat_mailinglist` = 0";
                    if ($result = $xoopsDB->query($sql)) {
                        ++$update;
                    } else {
                        $error[] = "Error update cat_mailinglist: " . $result->getHtmlErrors();
                    }
                }
            } else {
                //set cat_mailinglist = 0, if mailinglist_id is no more existing in table mailinglist
                $sql = "SELECT Count(`cat_mailinglist`) AS `nb_ids` FROM `".$xoopsDB->prefix("xnewsletter_cat")."` LEFT JOIN `".$xoopsDB->prefix("xnewsletter_mailinglist")."` ON `cat_mailinglist` = `mailinglist_id` WHERE (((`mailinglist_id`) Is Null) AND ((`cat_mailinglist`)>0)) HAVING (((Count(`cat_mailinglist`))>0));";
                if ( $result = $xoopsDB->query($sql) ) {
                    $row_result = $xoopsDB->fetchRow($result);
                    $number_ids = $row_result[0];
                }
                if ($number_ids > 0) {
                    $sql = "UPDATE `".$xoopsDB->prefix("xnewsletter_cat")."` LEFT JOIN `".$xoopsDB->prefix("xnewsletter_mailinglist")."` ON `cat_mailinglist` = `mailinglist_id` SET `cat_mailinglist` = 0 WHERE (((`cat_mailinglist`)>0) AND ((`mailinglist_id`) Is Null));";
                    if ($result = $xoopsDB->query($sql)) {
                        ++$update;
                    } else {
                        $error[] = "Error update cat_mailinglist: " . $result->getHtmlErrors();
                    }
                }
            }

            if (count($error) > 0) {
                foreach ($error as $err =>$text) {
                    $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                    $obj->setVar("protocol_letter_id", 0);
                    $obj->setVar("protocol_subscriber_id", 0);
                    $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " ml] " . $text);
                    $obj->setVar("protocol_success", 0);
                    $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                    $obj->setVar("protocol_created", time());
                    if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
                        echo _AM_XNEWSLETTER_MAINTENANCE_ERROR;
                    }
                }
            } else {
                $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                $obj->setVar("protocol_letter_id", 0);
                $obj->setVar("protocol_subscriber_id", 0);
                $status = $number_ids == 0 ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_NODATA : sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_OK, $number_ids);
                $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " ml] " . $status);
                $obj->setVar("protocol_success", 1);
                $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                $obj->setVar("protocol_created", time());

                if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
                    echo _AM_XNEWSLETTER_MAINTENANCE_ERROR;
                }
            }
            redirect_header("maintenance.php", 3,sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_OK, $number_ids));

        } else {
          xoops_confirm(array("ok" => 1, "", "op" => "del_invalid_ml"), "maintenance.php", _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML);
        }
        break;

    case 'del_invalid_cat':
        //remove cat from letter_cats, if cat is missing (if someone deleted cat after creating letter)
        if (isset($_POST["ok"]) && $_POST["ok"] == "1") {
            $update = 0;
            $error = array();
            $number_ids = 0;

            $letter_arr = $xnewsletter->getHandler('xNewsletter_letter')->getall();
            foreach (array_keys($letter_arr) as $letter_id) {
                $letter_cats_new = "";
                $letter_cats_old = $letter_arr[$letter_id]->getVar("letter_cats");
                $letter_cats = array();
                $letter_cats = explode("|", $letter_cats_old);

                foreach ($letter_cats as $cat_id) {
                    // check each cat and create new string 'letter_cats'
                    $crit_cat = new CriteriaCompo();
                    $crit_cat->add(new Criteria('cat_id', $cat_id));
                    $numrows = $xnewsletter->getHandler('xNewsletter_cat')->getCount($crit_cat);
                    if ( $numrows > 0 ) $letter_cats_new .= $cat_id . '|';
                }
                $letter_cats_new = substr($letter_cats_new, 0, -1);

                if ($letter_cats_old != $letter_cats_new) {
                    //update with correct value
                    $obj_letter = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
                    $obj_letter->setVar("letter_cats", $letter_cats_new);
                    if ($xnewsletter->getHandler('xNewsletter_letter')->insert($obj_letter)) {
                        ++$update;
                    } else {
                        $error[] = "Error update cat: " . $result->getHtmlErrors();
                    }
                }
            }

            if (count($error) > 0) {
                foreach ($error as $err =>$text) {
                    $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                    $obj->setVar("protocol_letter_id", 0);
                    $obj->setVar("protocol_subscriber_id", 0);
                    $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " cat] " . $text);
                    $obj->setVar("protocol_success", 0);
                    $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                    $obj->setVar("protocol_created", time());
                    if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
                        echo _AM_XNEWSLETTER_MAINTENANCE_ERROR;
                    }
                }
            } else {
                $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
                $obj->setVar("protocol_letter_id", 0);
                $obj->setVar("protocol_subscriber_id", 0);
                $status = $update == 0 ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_NODATA : sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_OK, $update);
                $obj->setVar("protocol_status", "[" . _MI_XNEWSLETTER_ADMENU11 . " cat] " . $status);
                $obj->setVar("protocol_success", 1);
                $obj->setVar("protocol_submitter", $GLOBALS['xoopsUser']->uid());
                $obj->setVar("protocol_created", time());

                if (!$xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
                    echo _AM_XNEWSLETTER_MAINTENANCE_ERROR;
                }
            }
            redirect_header("maintenance.php", 3,sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_OK, $number_ids));
        } else {
            xoops_confirm(array("ok" => 1, "", "op" => "del_invalid_cat"), "maintenance.php", _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL);
        }
        break;
}
include "admin_footer.php";
