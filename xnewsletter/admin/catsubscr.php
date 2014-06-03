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

include "admin_header.php";
xoops_cp_header();
//global $pathIcon, $indexAdmin;
// We recovered the value of the argument op in the URL$
$op = xnewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');

switch ($op)
{
    case "list":
    default:
      echo $indexAdmin->addNavigation('catsubscr.php');
      $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, 'catsubscr.php?op=new_catsubscr', 'add');
      echo $indexAdmin->renderButton();

      $limit = $GLOBALS['xoopsModuleConfig']['adminperpage'];
      $criteria = new CriteriaCompo();
      $criteria->setSort("cat_id ASC, cat_name");
      $criteria->setOrder("ASC");
      $numrows = $xnewsletter->getHandler('xnewsletter_cat')->getCount();
      $start = xnewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
      $criteria->setStart($start);
      $criteria->setLimit($limit);
      $cat_arr = $xnewsletter->getHandler('xnewsletter_cat')->getall($criteria);
      if ($numrows > $limit) {
        include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
        $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', 'op=list');
        $pagenav = $pagenav->renderNav(4);
      } else {
        $pagenav = '';
      }

      // View Table
      if ($numrows>0)
      {
        echo "<table class='outer width100' cellspacing='1'>
          <tr>
            <th class='center width2'>"._AM_XNEWSLETTER_CAT_ID."</th>
            <th class='center'>"._AM_XNEWSLETTER_CAT_NAME."</th>
            <th class='center'>"._AM_XNEWSLETTER_CAT_INFO."</th>
            <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID."</th>
            <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
          </tr>";

        $class = "odd";

        foreach (array_keys($cat_arr) as $i)
        {
          echo "<tr class='".$class."'>";
          $class = ($class == "even") ? "odd" : "even";
          echo "<td class='center'>".$i."</td>";
          echo "<td class='center'>
                  <a href='catsubscr.php?op=list_cat&cat_id=".$i."'>".$cat_arr[$i]->getVar("cat_name")."</a>
                </td>";
          echo "<td class='center'>".$cat_arr[$i]->getVar("cat_info")."</td>";

          $crit_catsubscr = new CriteriaCompo();
          $crit_catsubscr->add(new Criteria("catsubscr_catid", $i));
          $numrows = $xnewsletter->getHandler('xnewsletter_catsubscr')->getCount($crit_catsubscr);
          echo "<td class='center'>".$numrows."</td>";

          echo "<td class='center width5'>
            <a href='catsubscr.php?op=list_cat&cat_id=".$i."'><img src=".XNEWSLETTER_ICONS_URL."/xn_details.png alt='"._AM_XNEWSLETTER_DETAILS."' title='"._AM_XNEWSLETTER_DETAILS."' /></a>
            </td>";
          echo "</tr>";
        }
        echo "</table><br /><br />";
        echo "<br /><div class='center'>" . $pagenav . "</div><br />";
      } else {
        echo "<table class='outer width100' cellspacing='1'>
            <tr>
              <th class='center width2'>"._AM_XNEWSLETTER_CAT_ID."</th>
              <th class='center'>"._AM_XNEWSLETTER_CAT_NAME."</th>
              <th class='center'>"._AM_XNEWSLETTER_CAT_INFO."</th>
              <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
            </tr>";
        echo "</table><br /><br />";
      }
      break;

    case "list_cat":

        $cat_id = isset($_REQUEST["cat_id"]) ? $_REQUEST["cat_id"] : 0;

        echo $indexAdmin->addNavigation('catsubscr.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, 'catsubscr.php?op=list', 'list');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, 'catsubscr.php?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();

        $limit = $xnewsletter->getConfig('adminperpage');
        $crit_catsubscr = new CriteriaCompo();
        $crit_catsubscr->add(new Criteria("catsubscr_catid", $cat_id));
        $crit_catsubscr->setSort("catsubscr_id ASC, catsubscr_catid");
        $crit_catsubscr->setOrder("ASC");
        $numrows = $xnewsletter->getHandler('xnewsletter_catsubscr')->getCount($crit_catsubscr);
        $start = xnewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $crit_catsubscr->setStart($start);
        $crit_catsubscr->setLimit($limit);
        $catsubscr_arr = $xnewsletter->getHandler('xnewsletter_catsubscr')->getall($crit_catsubscr);
        if ($numrows > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', 'op=list_cat&cat_id='.$cat_id);
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($numrows>0)
        {
            echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>"._AM_XNEWSLETTER_CATSUBSCR_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_CATID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_QUITED."</th>
                    <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_SUBMITTER."</th>
                    <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_CREATED."</th>
                    <th class='center width10'>"._AM_XNEWSLETTER_FORMACTION."</th>
                </tr>";

            $class = "odd";

            foreach (array_keys($catsubscr_arr) as $i)
            {
                echo "<tr class='".$class."'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>".$i."</td>";

                $cat =& $xnewsletter->getHandler('xnewsletter_cat')->get($cat_id);
                $cat_name = $cat->getVar("cat_name");
                echo "<td class='center'>".$cat_name."</td>";
                $subscr_id = $catsubscr_arr[$i]->getVar("catsubscr_subscrid");
                $subscr =& $xnewsletter->getHandler('xnewsletter_subscr')->get($subscr_id);
                $subscr_email = ($subscr) ? $subscr->getVar("subscr_email") : "";
                echo "<td class='center'>".$subscr_email."</td>";
                if ($catsubscr_arr[$i]->getVar("catsubscr_quited") > 0) {
                    $catsubscr_quited = formatTimeStamp($catsubscr_arr[$i]->getVar("catsubscr_quited"),"M");
                } else {
                    $catsubscr_quited = "";
                }
                echo "<td class='center'>".$catsubscr_quited."</td>";
                echo "<td class='center'>".XoopsUser::getUnameFromId($catsubscr_arr[$i]->getVar("catsubscr_submitter"),"S")."</td>";
                echo "<td class='center'>".formatTimeStamp($catsubscr_arr[$i]->getVar("catsubscr_created"),"S")."</td>";

                echo "<td class='center width5' nowrap='nowrap'>
                    <a href='catsubscr.php?op=edit_catsubscr&catsubscr_id=".$i."&cat_id=".$cat_id."'><img src=".XNEWSLETTER_ICONS_URL."/xn_edit.png alt='"._EDIT."' title='"._EDIT."' /></a>
                    &nbsp;<a href='catsubscr.php?op=delete_catsubscr&catsubscr_id=".$i."&cat_id=".$cat_id."&cat_name=".$cat_name."&subscr_email=".$subscr_email."&subscr_id=".$subscr_id."'><img src=".XNEWSLETTER_ICONS_URL."/xn_delete.png alt='"._DELETE."' title='"._DELETE."'></a>
                    </td>";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                      <th class='center width2'>"._AM_XNEWSLETTER_CATSUBSCR_ID."</th>
                        <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_CATID."</th>
                        <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_SUBSCRID."</th>
                        <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_SUBMITTER."</th>
                        <th class='center'>"._AM_XNEWSLETTER_CATSUBSCR_CREATED."</th>
                        <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                    </tr>";
            echo "</table><br /><br />";
    }

    break;

    case "new_catsubscr":
        echo $indexAdmin->addNavigation("catsubscr.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, 'catsubscr.php?op=list', 'list');
        echo $indexAdmin->renderButton();

        $obj =& $xnewsletter->getHandler('xnewsletter_catsubscr')->create();
        $form = $obj->getForm();
        $form->display();
    break;

    case "save_catsubscr":
        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
            redirect_header("catsubscr.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        if (isset($_REQUEST["catsubscr_id"])) {
            $obj =& $xnewsletter->getHandler('xnewsletter_catsubscr')->get($_REQUEST["catsubscr_id"]);
        } else {
            $obj =& $xnewsletter->getHandler('xnewsletter_catsubscr')->create();
        }

        //Form catsubscr_catid
        $obj->setVar("catsubscr_catid", $_REQUEST["catsubscr_catid"]);
        //Form catsubscr_subscrid
        $catsubscr_subscrid = $_REQUEST["catsubscr_subscrid"];
        $obj->setVar("catsubscr_subscrid", $catsubscr_subscrid);
        //Form catsubscr_quited
        $catsubscr_quit_now = xnewsletter_CleanVars($_REQUEST, 'catsubscr_quit_now', 0, 'int');
        if ($catsubscr_quit_now == 1) {
            $obj->setVar("catsubscr_quited",  time());
        } elseif ($catsubscr_quit_now == 2) {
            $obj->setVar("catsubscr_quited", "0");
        }
        //Form catsubscr_submitter
        $obj->setVar("catsubscr_submitter", $_REQUEST["catsubscr_submitter"]);
        //Form catsubscr_created
        $obj->setVar("catsubscr_created", $_REQUEST["catsubscr_created"]);

        if ($xnewsletter->getHandler('xnewsletter_catsubscr')->insert($obj)) {
            //add subscriber to mailinglist
            $obj_cat = $xnewsletter->getHandler('xnewsletter_cat')->get($_REQUEST["catsubscr_catid"]);
            if ($obj_cat->getVar("cat_mailinglist") > 0) {
                require_once( XOOPS_ROOT_PATH."/modules/xnewsletter/include/mailinglist.php" );
                subscribingMLHandler(1, $catsubscr_subscrid, $obj_cat->getVar("cat_mailinglist"));
            }
            redirect_header("catsubscr.php?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $obj->getHtmlErrors();
        $form =& $obj->getForm();
        $form->display();
    break;

    case "edit_catsubscr":
    $cat_id = isset($_REQUEST["cat_id"]) ? $_REQUEST["cat_id"] : 0;

      echo $indexAdmin->addNavigation("catsubscr.php");
    $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, 'catsubscr.php?op=list_cat&cat_id='.$cat_id, 'list');
    $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, 'catsubscr.php?op=new_catsubscr', 'add');
    echo $indexAdmin->renderButton();

        $obj = $xnewsletter->getHandler('xnewsletter_catsubscr')->get($_REQUEST["catsubscr_id"]);
        $form = $obj->getForm();
        $form->display();
    break;

    case "delete_catsubscr":
        $obj =& $xnewsletter->getHandler('xnewsletter_catsubscr')->get($_REQUEST["catsubscr_id"]);
        if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header("catsubscr.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('xnewsletter_catsubscr')->delete($obj)) {
        //remove subscriber from mailinglist
        $subscr_id = $_REQUEST["subscr_id"];
        $obj_cat =& $xnewsletter->getHandler('xnewsletter_cat')->get($_REQUEST["cat_id"]);
        if ($obj_cat->getVar("cat_mailinglist") > 0) {
          require_once( XOOPS_ROOT_PATH."/modules/xnewsletter/include/mailinglist.php" );
          subscribingMLHandler(0, $subscr_id, $obj_cat->getVar("cat_mailinglist"));
        }
                redirect_header("catsubscr.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
      $confirmtext = str_replace("%c", $_REQUEST["cat_name"], _AM_XNEWSLETTER_CATSUBSCR_SUREDELETE);
      $confirmtext = str_replace("%s", $_REQUEST["subscr_email"], $confirmtext);
      $confirmtext = str_replace('"', " ", $confirmtext);

            xoops_confirm(array("ok" => 1, "catsubscr_id" => $_REQUEST["catsubscr_id"], "op" => "delete_catsubscr"), $_SERVER["REQUEST_URI"], sprintf($confirmtext));
        }
    break;
}
include "admin_footer.php";
