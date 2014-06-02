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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

include "admin_header.php";
xoops_cp_header();
//global $pathIcon;
// We recovered the value of the argument op in the URL$
$op = xNewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');
$subscr_id = xNewsletter_CleanVars($_REQUEST, 'subscr_id', 0, 'int');

$filter_subscr = xNewsletter_CleanVars($_REQUEST, 'filter_subscr', '=', 'string');
$filter_subscr_firstname = xNewsletter_CleanVars($_REQUEST, 'filter_subscr_firstname', '', 'string');
$filter_subscr_lastname = xNewsletter_CleanVars($_REQUEST, 'filter_subscr_lastname', '', 'string');
$filter_subscr_email = xNewsletter_CleanVars($_REQUEST, 'filter_subscr_email', '', 'string');

if ($op == 'apply_filter') {
  if ($filter_subscr == "LIKE" && !$filter_subscr_firstname=='') $filter_subscr_firstname = "%".$filter_subscr_firstname."%";
  if ($filter_subscr == "LIKE" && !$filter_subscr_lastname=='') $filter_subscr_lastname = "%".$filter_subscr_lastname."%";
  if ($filter_subscr == "LIKE" && !$filter_subscr_email=='') $filter_subscr_email = "%".$filter_subscr_email."%";
  if ($filter_subscr_firstname == '' && $filter_subscr_lastname == '' && $filter_subscr_email == '') $op = 'list';
}

$subscrAdmin = new ModuleAdmin();
switch ($op)
{
    case "show_catsubscr":
      echo $subscrAdmin->addNavigation('subscr.php');
      $apply_filter = xNewsletter_CleanVars($_REQUEST, 'apply_filter', 'list', 'string');
      $linklist = "subscr.php?op=$apply_filter&filter_subscr=$filter_subscr";
            $linklist .= "&filter_subscr_firstname=$filter_subscr_firstname";
            $linklist .= "&filter_subscr_lastname=$filter_subscr_lastname";
            $linklist .= "&filter_subscr_email=$filter_subscr_email";
      $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, $linklist, 'view_detailed');
      echo $subscrAdmin->renderButton();

      $obj_subscr =& $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);

      echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_LETTERLIST . "</th>
                </tr>";

            $class = "odd";
      echo "<tr class='"  .$class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='right'>" . $subscr_id . "&nbsp;</td>";
                echo "<td class='left'>" . $obj_subscr->getVar("subscr_email") . "</td>";
        echo "<td class='left'>";
        $crit_catsubscr = new CriteriaCompo();
        $crit_catsubscr->add(new Criteria("catsubscr_subscrid", $subscr_id));
        $numrows = $xnewsletter->getHandler('xNewsletter_catsubscr')->getCount($crit_catsubscr);
        if ($numrows > 0) {
          $catsubscr_arr = $xnewsletter->getHandler('xNewsletter_catsubscr')->getall($crit_catsubscr);
          foreach (array_keys($catsubscr_arr) as $i)
          {
            $cat_id = $catsubscr_arr[$i]->getVar("catsubscr_catid");
            $obj_cat =& $xnewsletter->getHandler('xNewsletter_cat')->get($cat_id);
            $cat_name = $obj_cat->getVar("cat_name");
            echo $cat_name."<br/>";
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
        echo $subscrAdmin->addNavigation('subscr.php');
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, 'subscr.php?op=new_subscr', 'add');
    if ($op == 'apply_filter') $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, 'subscr.php?op=list', 'view_detailed');
        echo $subscrAdmin->renderButton();
        $limit = $GLOBALS['xoopsModuleConfig']['adminperpage'];
        $crit_subscr = new CriteriaCompo();

    if ($op == 'apply_filter') {
      if ($filter_subscr_firstname != '')
        $crit_subscr->add(new Criteria("subscr_firstname", $filter_subscr_firstname,$filter_subscr));
      if ($filter_subscr_lastname != '')
        $crit_subscr->add(new Criteria("subscr_lastname", $filter_subscr_lastname,$filter_subscr));
      if ($filter_subscr_email != '')
        $crit_subscr->add(new Criteria("subscr_email", $filter_subscr_email,$filter_subscr));
    }
        $crit_subscr->setSort("subscr_id");
        $crit_subscr->setOrder("DESC");
        $numrows = $xnewsletter->getHandler('xNewsletter_subscr')->getCount($crit_subscr);
        $start = xNewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $crit_subscr->setStart($start);
        $crit_subscr->setLimit($limit);
        $subscr_arr = $xnewsletter->getHandler('xNewsletter_subscr')->getall($crit_subscr);
        if ($numrows > $limit) {
      include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
      $linklist = "op=$op&filter_subscr=$filter_subscr";
            $linklist .= "&filter_subscr_firstname=$filter_subscr_firstname";
            $linklist .= "&filter_subscr_lastname=$filter_subscr_lastname";
            $linklist .= "&filter_subscr_email=$filter_subscr_email";
      $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', $linklist);
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
        if ($numrows>0)
        {
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
      echo "<form id='form_filter' enctype='multipart/form-data' method='post' action='subscr.php' name='form_filter'>";

      $inputstyle = "style='border: 1px solid #000000;";
      echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='left' colspan='2'>" . _AM_XNEWSLETTER_SEARCH . ":&nbsp;&nbsp;";
        echo "<select id='filter_subscr' title='" . _AM_XNEWSLETTER_SEARCH . "' name='filter_subscr' size='1'>";
        echo "<option ";
        if ($filter_subscr == "=") echo "selected='selected' ";
        echo "value='='>" . _AM_XNEWSLETTER_SEARCH_EQUAL . "</option>";
        echo "<option ";
        if ($filter_subscr == "LIKE") echo "selected='selected' ";
        echo " value='LIKE'>" . _AM_XNEWSLETTER_SEARCH_CONTAINS . "</option>";
        echo "</select>";
        echo "</td>";
                echo "<td class='right'><input $inputstyle id='filter_subscr_firstname' type='text' value='$filter_subscr_firstname' maxlength='100' size='15' title='' name='filter_subscr_firstname'></td>";
                echo "<td class='left'><input $inputstyle id='filter_subscr_lastname' type='text' value='$filter_subscr_lastname' maxlength='100' size='15' title='' name='filter_subscr_lastname'></td>";
                echo "<td class='left'><input $inputstyle id='filter_subscr_email' type='text' value='$filter_subscr_email' maxlength='255' size='40' title='' name='filter_subscr_email'></td>";
                echo "<td class='right' colspan='3'>
                    <input id='submit' class='formButton' type='submit' title='" . _AM_XNEWSLETTER_SEARCH . "' value='" . _AM_XNEWSLETTER_SEARCH . "' name='submit'>
                    </td>";
                echo "</tr>";
        echo "<input id='op' type='hidden' value='apply_filter' name='op'>";
        echo "</form>";

            foreach (array_keys($subscr_arr) as $i)
            {
                echo "<tr class='".$class."'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='right'>".$i."&nbsp;</td>";
                echo "<td class='left'>" . $subscr_arr[$i]->getVar("subscr_sex") . "&nbsp;</td>";
                echo "<td class='left'>" . $subscr_arr[$i]->getVar("subscr_firstname") . "&nbsp;</td>";
                echo "<td class='left'>" . $subscr_arr[$i]->getVar("subscr_lastname") . "</td>";
                echo "<td class='left'>&nbsp;".$subscr_arr[$i]->getVar("subscr_email") . "&nbsp;</td>";
        echo "<td class='center'>";
        if ($subscr_arr[$i]->getVar("subscr_uid") > 0) {
          echo XoopsUser::getUnameFromId($subscr_arr[$i]->getVar("subscr_uid"), "S");
        } else {
          echo "-";
        }
        echo "</td>";
                echo "<td class='left'>&nbsp;";
                if ( $subscr_arr[$i]->getVar("subscr_activated") != 1 ) {
                    echo '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_failed.png" alt="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" title="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" /> ';
                } else {
                    echo '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_ok.png" alt="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" title="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" /> ';
                }
                echo formatTimestamp($subscr_arr[$i]->getVar("subscr_created"), $xnewsletter->getConfig('dateformat')) . " [".$subscr_arr[$i]->getVar("subscr_ip")."]&nbsp;</td>";

                echo "<td class='center width5' nowrap='nowrap'>";
                echo "	<a href='subscr.php?op=edit_subscr&subscr_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo "	&nbsp;<a href='subscr.php?op=delete_subscr&subscr_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                echo "	&nbsp;<a href='subscr.php?op=show_catsubscr&subscr_id=" . $i;
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

$filter_subscr = xNewsletter_CleanVars($_REQUEST, 'filter_subscr', '=', 'string');
$filter_subscr_firstname = xNewsletter_CleanVars($_REQUEST, 'filter_subscr_firstname', '', 'string');
$filter_subscr_lastname = xNewsletter_CleanVars($_REQUEST, 'filter_subscr_lastname', '', 'string');
$filter_subscr_email = xNewsletter_CleanVars($_REQUEST, 'filter_subscr_email', '', 'string');

            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_EMAIL."</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_FIRSTNAME."</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_LASTNAME."</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_UID."</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_SEX."</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_SUBMITTER."</th>
                        <th class='center'>"._AM_XNEWSLETTER_SUBSCR_CREATED."</th>
                        <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                    </tr>";
            echo "</table><br /><br />";
        }

    break;

    case "new_subscr":
      echo $subscrAdmin->addNavigation("subscr.php");
      $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, 'subscr.php?op=list', 'list');
      echo $subscrAdmin->renderButton();

      $obj =& $xnewsletter->getHandler('xNewsletter_subscr')->create();
      $form = $obj->getFormAdmin();
      $form->display();
      break;

    case "save_subscr":
        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
            redirect_header("subscr.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }

        $obj =& $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);

        //Form subscr_email
        $obj->setVar("subscr_email", $_REQUEST["subscr_email"]);
        //Form subscr_firstname
        $obj->setVar("subscr_firstname", $_REQUEST["subscr_firstname"]);
        //Form subscr_lastname
        $obj->setVar("subscr_lastname", $_REQUEST["subscr_lastname"]);
        //Form subscr_uid
        $obj->setVar("subscr_uid", $_REQUEST["subscr_uid"]);
        //Form subscr_sex
        $obj->setVar("subscr_sex", $_REQUEST["subscr_sex"]);
        //Form subscr_submitter
        $obj->setVar("subscr_submitter", $_REQUEST["subscr_submitter"]);
        //Form subscr_created
        $obj->setVar("subscr_created", $_REQUEST["subscr_created"]);
        //Form subscr_ip
        $obj->setVar("subscr_ip", $_REQUEST["subscr_ip"]);
        //Form subscr_actkey
        $obj->setVar("subscr_actkey", $_REQUEST["subscr_actkey"]);

        $obj->setVar("subscr_activated", xNewsletter_CleanVars($_REQUEST, 'subscr_activated', 0, 'int'));

        if ($xnewsletter->getHandler('xNewsletter_subscr')->insert($obj)) {
            redirect_header("subscr.php?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $obj->getHtmlErrors();
        $form =& $obj->getFormAdmin();
        $form->display();
    break;

    case "edit_subscr":
        echo $subscrAdmin->addNavigation("subscr.php");
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, 'subscr.php?op=new_subscr', 'add');
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, 'subscr.php?op=list', 'list');
        echo $subscrAdmin->renderButton();
        $obj = $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);
        $form = $obj->getFormAdmin();
        $form->display();
    break;

    case "delete_subscr":
        $obj =& $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);
        if (isset($_POST["ok"]) && $_POST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header("subscr.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('xNewsletter_subscr')->delete($obj)) {
                // Newsletterlist delete
                $sql = "DELETE FROM `".$xoopsDB->prefix('xnewsletter_catsubscr')."` WHERE catsubscr_subscrid=" . $subscr_id;
                $result = $xoopsDB->queryF($sql);
                redirect_header("subscr.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "subscr_id" => $_REQUEST["subscr_id"], "op" => "delete_subscr"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $obj->getVar("subscr_email")));
        }
    break;
}
include "admin_footer.php";
