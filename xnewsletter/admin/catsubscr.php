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
include_once dirname(__FILE__) . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op = xnewsletterRequest::getString('op', 'list');

switch ($op) {
    case "list":
    default:
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $catCriteria = new CriteriaCompo();
        $catCriteria->setSort("cat_id ASC, cat_name");
        $catCriteria->setOrder("ASC");
        $catsCount = $xnewsletter->getHandler('cat')->getCount();
        $start = xnewsletterRequest::getInt('start', 0);
        $catCriteria->setStart($start);
        $catCriteria->setLimit($limit);
        $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);
        if ($catsCount > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($catsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($catsCount>0) {
            echo "
                <table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_CAT_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CAT_NAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CAT_INFO . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_SUBSCRID . "</th>
                        <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                    </tr>";

            $class = "odd";

            foreach ($catObjs as $cat_id => $catObj) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $cat_id . "</td>";
                echo "
                    <td class='center'>
                        <a href='?op=list_cat&cat_id=" . $cat_id . "'>" . $catObj->getVar("cat_name") . "</a>
                    </td>";
                echo "<td class='center'>" . $catObj->getVar("cat_info") . "</td>";
                $catsubscrCriteria = new CriteriaCompo();
                $catsubscrCriteria->add(new Criteria("catsubscr_catid", $cat_id));
                $catsCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
                echo "<td class='center'>" . $catsCount . "</td>";
                echo "
                    <td class='center width5'>
                        <a href='?op=list_cat&cat_id=" . $cat_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_details.png alt='" . _AM_XNEWSLETTER_DETAILS . "' title='" . _AM_XNEWSLETTER_DETAILS . "' /></a>
                    </td>";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "
                <table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_CAT_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CAT_NAME . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CAT_INFO . "</th>
                        <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                    </tr>";
            echo "</table><br /><br />";
        }
    break;

    case "list_cat":

        $cat_id = isset($_REQUEST["cat_id"]) ? $_REQUEST["cat_id"] : 0;

        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $catsubscrCriteria = new CriteriaCompo();
        $catsubscrCriteria->add(new Criteria("catsubscr_catid", $cat_id));
        $catsubscrCriteria->setSort("catsubscr_id ASC, catsubscr_catid");
        $catsubscrCriteria->setOrder("ASC");
        $catsCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
        $start = xnewsletterRequest::getInt('start', 0);
        $catsubscrCriteria->setStart($start);
        $catsubscrCriteria->setLimit($limit);
        $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
        if ($catsCount > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($catsCount, $limit, $start, 'start', 'op=list_cat&cat_id=' . $cat_id);
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($catsCount>0)
        {
            echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_CATSUBSCR_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_CATID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_SUBSCRID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_QUITED . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_SUBMITTER . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_CREATED . "</th>
                    <th class='center width10'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>";

            $class = "odd";

            foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $catsubscr_id . "</td>";

                $cat = $xnewsletter->getHandler('cat')->get($cat_id);
                $cat_name = $cat->getVar("cat_name");
                echo "<td class='center'>" . $cat_name . "</td>";
                $subscr_id = $catsubscrObj->getVar("catsubscr_subscrid");
                $subscr = $xnewsletter->getHandler('subscr')->get($subscr_id);
                $subscr_email = ($subscr) ? $subscr->getVar("subscr_email") : "";
                echo "<td class='center'>" . $subscr_email . "</td>";
                if ($catsubscrObj->getVar("catsubscr_quited") > 0) {
                    $catsubscr_quited = formatTimeStamp($catsubscrObj->getVar("catsubscr_quited"), "M");
                } else {
                    $catsubscr_quited = "";
                }
                echo "<td class='center'>" . $catsubscr_quited . "</td>";
                echo "<td class='center'>" . XoopsUser::getUnameFromId($catsubscrObj->getVar("catsubscr_submitter"), "S") . "</td>";
                echo "<td class='center'>" . formatTimeStamp($catsubscrObj->getVar("catsubscr_created"), "S") . "</td>";

                echo "<td class='center width5' nowrap='nowrap'>
                    <a href='?op=edit_catsubscr&catsubscr_id=" . $catsubscr_id . "&cat_id=" . $cat_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>
                    &nbsp;<a href='?op=delete_catsubscr&catsubscr_id=" . $catsubscr_id . "&cat_id=" . $cat_id . "&cat_name=" . $cat_name . "&subscr_email=" . $subscr_email . "&subscr_id=" . $subscr_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>
                    </td>";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                      <th class='center width2'>" . _AM_XNEWSLETTER_CATSUBSCR_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_CATID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_SUBSCRID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_SUBMITTER . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_CATSUBSCR_CREATED . "</th>
                        <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                    </tr>";
            echo "</table><br /><br />";
    }

    break;

    case "new_catsubscr":
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
        $form = $catsubscrObj->getForm();
        $form->display();
    break;

    case "save_catsubscr":
        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
            redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        if (isset($_REQUEST["catsubscr_id"])) {
            $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($_REQUEST["catsubscr_id"]);
        } else {
            $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
        }

        //Form catsubscr_catid
        $catsubscrObj->setVar("catsubscr_catid", $_REQUEST["catsubscr_catid"]);
        //Form catsubscr_subscrid
        $catsubscr_subscrid = $_REQUEST["catsubscr_subscrid"];
        $catsubscrObj->setVar("catsubscr_subscrid", $catsubscr_subscrid);
        //Form catsubscr_quited
        $catsubscr_quit_now = xnewsletterRequest::getInt('catsubscr_quit_now', 0);
        if ($catsubscr_quit_now == 1) {
            $catsubscrObj->setVar("catsubscr_quited",  time());
        } elseif ($catsubscr_quit_now == 2) {
            $catsubscrObj->setVar("catsubscr_quited", "0");
        }
        //Form catsubscr_submitter
        $catsubscrObj->setVar("catsubscr_submitter", $_REQUEST["catsubscr_submitter"]);
        //Form catsubscr_created
        $catsubscrObj->setVar("catsubscr_created", $_REQUEST["catsubscr_created"]);

        if ($xnewsletter->getHandler('catsubscr')->insert($catsubscrObj)) {
            //add subscriber to mailinglist
            $catsubscrObj_cat = $xnewsletter->getHandler('cat')->get($_REQUEST["catsubscr_catid"]);
            if ($catsubscrObj_cat->getVar("cat_mailinglist") > 0) {
                require_once( XOOPS_ROOT_PATH."/modules/xnewsletter/include/mailinglist.php" );
                subscribingMLHandler(1, $catsubscr_subscrid, $catsubscrObj_cat->getVar("cat_mailinglist"));
            }
            redirect_header("?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $catsubscrObj->getHtmlErrors();
        $form = $catsubscrObj->getForm();
        $form->display();
    break;

    case "edit_catsubscr":
        $cat_id = isset($_REQUEST["cat_id"]) ? $_REQUEST["cat_id"] : 0;

        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, '?op=list_cat&cat_id=' . $cat_id, 'list');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();
        //
        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($_REQUEST["catsubscr_id"]);
        $form = $catsubscrObj->getForm();
        $form->display();
    break;

    case "delete_catsubscr":
        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($_REQUEST["catsubscr_id"]);
        if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header("catsubscr.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('catsubscr')->delete($catsubscrObj)) {
        //remove subscriber from mailinglist
        $subscr_id = $_REQUEST["subscr_id"];
        $catsubscrObj_cat = $xnewsletter->getHandler('cat')->get($_REQUEST["cat_id"]);
        if ($catsubscrObj_cat->getVar("cat_mailinglist") > 0) {
          require_once( XOOPS_ROOT_PATH . "/modules/xnewsletter/include/mailinglist.php" );
          subscribingMLHandler(0, $subscr_id, $catsubscrObj_cat->getVar("cat_mailinglist"));
        }
                redirect_header("catsubscr.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $catsubscrObj->getHtmlErrors();
            }
        } else {
      $confirmtext = str_replace("%c", $_REQUEST["cat_name"], _AM_XNEWSLETTER_CATSUBSCR_SUREDELETE);
      $confirmtext = str_replace("%s", $_REQUEST["subscr_email"], $confirmtext);
      $confirmtext = str_replace('"', " ", $confirmtext);

            xoops_confirm(array("ok" => 1, "catsubscr_id" => $_REQUEST["catsubscr_id"], "op" => "delete_catsubscr"), $_SERVER["REQUEST_URI"], sprintf($confirmtext));
        }
    break;
}
include_once dirname(__FILE__) . '/admin_footer.php';
