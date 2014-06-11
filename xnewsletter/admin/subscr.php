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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include "admin_header.php";
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op                      = xnewsletterRequest::getString('op', 'list');
$subscr_id               = xnewsletterRequest::getInt('subscr_id', 0);

$filter_subscr           = xnewsletterRequest::getString('filter_subscr', '=');
$filter_subscr_firstname = xnewsletterRequest::getString('filter_subscr_firstname', '');
$filter_subscr_lastname  = xnewsletterRequest::getString('filter_subscr_lastname', '');
$filter_subscr_email     = xnewsletterRequest::getString('filter_subscr_email', '');

if ($op == 'apply_filter') {
    if ($filter_subscr == "LIKE" && !$filter_subscr_firstname=='') $filter_subscr_firstname = "%".$filter_subscr_firstname."%";
    if ($filter_subscr == "LIKE" && !$filter_subscr_lastname=='') $filter_subscr_lastname = "%".$filter_subscr_lastname."%";
    if ($filter_subscr == "LIKE" && !$filter_subscr_email=='') $filter_subscr_email = "%".$filter_subscr_email."%";
    if ($filter_subscr_firstname == '' && $filter_subscr_lastname == '' && $filter_subscr_email == '') $op = 'list';
}

$subscrAdmin = new ModuleAdmin();
switch ($op) {
    case "show_catsubscr":
        echo $subscrAdmin->addNavigation($currentFile);
        $apply_filter = xnewsletterRequest::getString('apply_filter', 'list');
        $linklist = "?op=$apply_filter&filter_subscr=$filter_subscr";
        $linklist .= "&filter_subscr_firstname=$filter_subscr_firstname";
        $linklist .= "&filter_subscr_lastname=$filter_subscr_lastname";
        $linklist .= "&filter_subscr_email=$filter_subscr_email";
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, $linklist, 'view_detailed');
        echo $subscrAdmin->renderButton();

        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);

        echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_LETTERLIST . "</th>
                </tr>";

        $class = "odd";
        echo "<tr class='"  .$class . "'>";
        $class = ($class == "even") ? "odd" : "even";
        echo "<td class='center'>" . $subscr_id . "</td>";
        echo "<td class='center'>" . $subscrObj->getVar("subscr_email") . "</td>";
        echo "<td class='center'>";
        $catsubscrCriteria = new CriteriaCompo();
        $catsubscrCriteria->add(new Criteria("catsubscr_subscrid", $subscr_id));
        $catsubscrsCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
        if ($catsubscrsCount > 0) {
            $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
            foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                $cat_id = $catsubscrObj->getVar("catsubscr_catid");
                $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
                echo $catObj->getVar("cat_name") . "<br/>";
            }
        } else {
            echo _AM_XNEWSLETTER_SUBSCR_NO_CATSUBSCR;
        }
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    break;



    case "list":
    case "apply_filter":
    default:
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        if ($op == 'apply_filter') $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, '?op=list', 'view_detailed');
        echo $subscrAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $subscrCriteria = new CriteriaCompo();

        if ($op == 'apply_filter') {
            if ($filter_subscr_firstname != '')
                $subscrCriteria->add(new Criteria("subscr_firstname", $filter_subscr_firstname,$filter_subscr));
            if ($filter_subscr_lastname != '')
                $subscrCriteria->add(new Criteria("subscr_lastname", $filter_subscr_lastname,$filter_subscr));
            if ($filter_subscr_email != '')
                $subscrCriteria->add(new Criteria("subscr_email", $filter_subscr_email,$filter_subscr));
        }
        $subscrCriteria->setSort("subscr_id");
        $subscrCriteria->setOrder("DESC");
        $subscrsCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
        $start = xnewsletterRequest::getInt('start', 0);
        $subscrCriteria->setStart($start);
        $subscrCriteria->setLimit($limit);
        $subscrObjs = $xnewsletter->getHandler('subscr')->getAll($subscrCriteria);
        if ($subscrsCount > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $linklist = "op={$op}";
            $linklist .= "&filter_subscr={$filter_subscr}";
            $linklist .= "&filter_subscr_firstname={$filter_subscr_firstname}";
            $linklist .= "&filter_subscr_lastname={$filter_subscr_lastname}";
            $linklist .= "&filter_subscr_email={$filter_subscr_email}";
            $pagenav = new XoopsPageNav($subscrsCount, $limit, $start, 'start', $linklist);
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }
        if ($filter_subscr == "LIKE") {
            //clean up var for refill form
            $filter_subscr_firstname = str_replace("%", "", $filter_subscr_firstname);
            $filter_subscr_lastname = str_replace("%", "", $filter_subscr_lastname);
            $filter_subscr_email = str_replace("%", "", $filter_subscr_email);
        }
        // View Table
        if ($subscrsCount > 0) {
            echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_SEX . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_FIRSTNAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_LASTNAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_UID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>";

            $class = "odd";
            echo "<form id='form_filter' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_filter'>";

            $inputstyle = "style='border: 1px solid #000000;";
            echo "<tr class='" . $class . "'>";
            $class = ($class == "even") ? "odd" : "even";
            echo "<td class='center' colspan='2'>" . _AM_XNEWSLETTER_SEARCH . ":&nbsp;&nbsp;";
            echo "<select id='filter_subscr' title='" . _AM_XNEWSLETTER_SEARCH . "' name='filter_subscr' size='1'>";
            echo "<option ";
            if ($filter_subscr == "=") echo "selected='selected' ";
            echo "value='='>" . _AM_XNEWSLETTER_SEARCH_EQUAL . "</option>";
            echo "<option ";
            if ($filter_subscr == "LIKE") echo "selected='selected' ";
            echo " value='LIKE'>" . _AM_XNEWSLETTER_SEARCH_CONTAINS . "</option>";
            echo "</select>";
            echo "</td>";
            echo "<td class='center'><input {$inputstyle} id='filter_subscr_firstname' type='text' value='{$filter_subscr_firstname}' maxlength='100' size='15' title='' name='filter_subscr_firstname'></td>";
            echo "<td class='center'><input {$inputstyle} id='filter_subscr_lastname' type='text' value='{$filter_subscr_lastname}' maxlength='100' size='15' title='' name='filter_subscr_lastname'></td>";
            echo "<td class='center'><input {$inputstyle} id='filter_subscr_email' type='text' value='{$filter_subscr_email}' maxlength='255' size='40' title='' name='filter_subscr_email'></td>";
            echo "<td class='center'>&nbsp;</td>";
            echo "<td class='center'>&nbsp;</td>";
            echo "<td class='center'>
                    <input id='submit' class='formButton' type='submit' title='" . _AM_XNEWSLETTER_SEARCH . "' value='" . _AM_XNEWSLETTER_SEARCH . "' name='submit'>
                    </td>";
            echo "</tr>";
            echo "<input id='op' type='hidden' value='apply_filter' name='op'>";
            echo "</form>";

            foreach ($subscrObjs as $subscr_id => $subscrObj) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $subscr_id . "</td>";
                echo "<td class='center'>" . $subscrObj->getVar("subscr_sex") . "&nbsp;</td>";
                echo "<td class='center'>" . $subscrObj->getVar("subscr_firstname") . "&nbsp;</td>";
                echo "<td class='center'>" . $subscrObj->getVar("subscr_lastname") . "&nbsp;</td>";
                echo "<td class='center'>" . $subscrObj->getVar("subscr_email") . "&nbsp;</td>";
                echo "<td class='center'>";
                if ($subscrObj->getVar("subscr_uid") > 0) {
                    echo XoopsUser::getUnameFromId($subscrObj->getVar("subscr_uid"), "S");
                } else {
                    echo "-";
                }
                echo "</td>";
                echo "<td class='left'>&nbsp;";
                if ( $subscrObj->getVar("subscr_activated") != 1 ) {
                    echo '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_failed.png" alt="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" title="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" /> ';
                } else {
                    echo '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_ok.png" alt="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" title="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" /> ';
                }
                echo formatTimestamp($subscrObj->getVar("subscr_created"), $xnewsletter->getConfig('dateformat')) . " [" . $subscrObj->getVar("subscr_ip") . "]&nbsp;</td>";

                echo "<td class='center width5' nowrap='nowrap'>";
                echo "	<a href='?op=edit_subscr&subscr_id=" . $subscr_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo "	&nbsp;<a href='?op=delete_subscr&subscr_id=" . $subscr_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                echo "	&nbsp;<a href='?op=show_catsubscr&subscr_id=" . $subscr_id;
                echo "&filter_subscr=$filter_subscr";
                echo "&filter_subscr_firstname=$filter_subscr_firstname";
                echo "&filter_subscr_lastname=$filter_subscr_lastname";
                echo "&filter_subscr_email=$filter_subscr_email";
                echo "&apply_filter=$op";
                echo " '>";
                echo "<img src=" . XNEWSLETTER_ICONS_URL . "/xn_details.png alt='" . _AM_XNEWSLETTER_DETAILS . "' title='" . _AM_XNEWSLETTER_DETAILS . "' />";
                echo "</a>";
                echo "  </td>";
                echo "</tr>";

                $filter_subscr           = xnewsletterRequest::getString('filter_subscr', '=');
                $filter_subscr_firstname = xnewsletterRequest::getString('filter_subscr_firstname', '');
                $filter_subscr_lastname  = xnewsletterRequest::getString('filter_subscr_lastname', '');
                $filter_subscr_email     = xnewsletterRequest::getString('filter_subscr_email', '');

            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_FIRSTNAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_LASTNAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_UID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_SEX . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_SUBMITTER . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_CREATED . "</th>
                        <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                    </tr>";
            echo "</table><br /><br />";
        }
    break;



    case "new_subscr":
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->create();
        $form = $subscrObj->getFormAdmin();
        $form->display();
    break;



    case "save_subscr":
        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
            redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }

        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $subscrObj->setVar("subscr_email", $_REQUEST["subscr_email"]);
        $subscrObj->setVar("subscr_firstname", $_REQUEST["subscr_firstname"]);
        $subscrObj->setVar("subscr_lastname", $_REQUEST["subscr_lastname"]);
        $subscrObj->setVar("subscr_uid", $_REQUEST["subscr_uid"]);
        $subscrObj->setVar("subscr_sex", $_REQUEST["subscr_sex"]);
        $subscrObj->setVar("subscr_submitter", $_REQUEST["subscr_submitter"]);
        $subscrObj->setVar("subscr_created", $_REQUEST["subscr_created"]);
        $subscrObj->setVar("subscr_ip", $_REQUEST["subscr_ip"]);
        $subscrObj->setVar("subscr_actkey", $_REQUEST["subscr_actkey"]);
        $subscrObj->setVar("subscr_activated", xnewsletterRequest::getInt('subscr_activated', 0));

        if ($xnewsletter->getHandler('subscr')->insert($subscrObj)) {
            redirect_header("?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $subscrObj->getHtmlErrors();
        $form = $subscrObj->getFormAdmin();
        $form->display();
    break;



    case "edit_subscr":
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $form = $subscrObj->getFormAdmin();
        $form->display();
    break;



    case "delete_subscr":
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        if (isset($_POST["ok"]) && $_POST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('subscr')->delete($subscrObj)) {
                // Newsletterlist delete
                $sql = "DELETE FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}` WHERE catsubscr_subscrid=" . $subscr_id;
                $result = $xoopsDB->queryF($sql);
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $subscrObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "subscr_id" => $_REQUEST["subscr_id"], "op" => "delete_subscr"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $subscrObj->getVar("subscr_email")));
        }
    break;
}
include "admin_footer.php";
