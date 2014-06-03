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

$protocolAdmin = new ModuleAdmin();
$letterAdmin = new ModuleAdmin();

switch ($op)
{
    case "list":
        echo $letterAdmin->addNavigation('protocol.php');
        $limit = $xnewsletter->getConfig('adminperpage');
        $criteria = new CriteriaCompo();
        $criteria->setSort("letter_id");
        $criteria->setOrder("DESC");
        $numrows = $xnewsletter->getHandler('xNewsletter_letter')->getCount();
        $start = xNewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $letter_arr = $xnewsletter->getHandler('xNewsletter_letter')->getall($criteria);
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
                    <th class='center width2'>"._AM_XNEWSLETTER_LETTER_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_TITLE."</th>
                    <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS."</th>
                    <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_CREATED."</th>
                    <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                </tr>";

            $class = "odd";

            //first show misc protocol items
            echo "<tr class='".$class."'>";
            $class = ($class == "even") ? "odd" : "even";
            echo "<td class='center'> - </td>";
            echo "<td class='center'>"._AM_XNEWSLETTER_PROTOCOL_MISC."</td>";

            $crit_prot = new CriteriaCompo();
            $crit_prot->add(new Criteria('protocol_letter_id', '0'));
            $crit_prot->setSort("protocol_id");
            $crit_prot->setOrder("DESC");
            $num_prot = $xnewsletter->getHandler('xNewsletter_protocol')->getCount($crit_prot);
            $crit_prot->setLimit(2);
            $protocol_arr = $xnewsletter->getHandler('xNewsletter_protocol')->getall($crit_prot);
            $protocol_status = "";
            $protocol_created = "";
            $p = 0;
            foreach ($protocol_arr as $protocol) {
                ++$p;
                if (count($protocol_arr)>1) $protocol_status .="($p) ";
                $protocol_status .= $protocol->getVar("protocol_status")."<br/>";
                $protocol_created .= formatTimeStamp($protocol->getVar("protocol_created"),"M")."<br/>";
            }
            if ($num_prot > 2) $protocol_status .= "...";
            echo "	<td class='center'>
                        <a href='protocol.php?op=list_letter&letter_id=0'>".$protocol_status."</a>
                    </td>";
            echo "<td class='center'>".$protocol_created."</td>";

            echo "	<td class='center width5'>
                        <a href='protocol.php?op=list_letter&letter_id=0'><img src=".XNEWSLETTER_ICONS_URL."/xn_details.png alt='"._AM_XNEWSLETTER_DETAILS."' title='"._AM_XNEWSLETTER_DETAILS."' /></a>
                </td>";
            echo "</tr>";

            foreach (array_keys($letter_arr) as $i)
            {
                $crit_prot = new CriteriaCompo();
                $crit_prot->add(new Criteria('protocol_letter_id', $letter_arr[$i]->getVar("letter_id")));
                $crit_prot->setSort("protocol_id");
                $crit_prot->setOrder("DESC");
                $num_prot = $xnewsletter->getHandler('xNewsletter_protocol')->getCount($crit_prot);
                if ($num_prot > 0) {
          $crit_prot->setLimit(2);
          $protocol_arr = $xnewsletter->getHandler('xNewsletter_protocol')->getall($crit_prot);
          $protocol_status = "";
          $protocol_created = "";

          echo "<tr class='".$class."'>";
          $class = ($class == "even") ? "odd" : "even";
          echo "<td class='center'>".$i."</td>";
          echo "<td class='center'>".$letter_arr[$i]->getVar("letter_title")."</td>";

          $p = 0;
          foreach ($protocol_arr as $protocol) {
            ++$p;
            if (count($protocol_arr)>1) $protocol_status .="($p) ";
            $protocol_status .= $protocol->getVar("protocol_status")."<br/>";
            $protocol_created .= formatTimeStamp($protocol->getVar("protocol_created"),"M")."<br/>";
          }
          if ($num_prot > 2) $protocol_status .= "...";
          echo "<td class='center'>
              <a href='protocol.php?op=list_letter&letter_id=".$i."'>".$protocol_status."</a>
            </td>";
          echo "<td class='center'>".$protocol_created."</td>";

          echo "<td class='center width5'>
            <a href='protocol.php?op=list_letter&letter_id=".$i."'><img src=".XNEWSLETTER_ICONS_URL."/xn_details.png alt='"._AM_XNEWSLETTER_DETAILS."' title='"._AM_XNEWSLETTER_DETAILS."' /></a>
            </td>";
          echo "</tr>";
        }
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                      <th class='center width2'>"._AM_XNEWSLETTER_LETTER_ID."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_TITLE."</th>
                        <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS."</th>
            <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_CREATED."</th>
                        <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                    </tr>";
            echo "</table><br /><br />";
        }

    break;
    case "list_letter":
        $letter_id = isset($_REQUEST["letter_id"]) ? $_REQUEST["letter_id"] :'0';
        echo $protocolAdmin->addNavigation('protocol.php');
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, 'protocol.php?op=list', 'list');
        if ($letter_id > '0') $protocolAdmin->addItemButton(_AM_XNEWSLETTER_LETTER_DELETE_ALL, 'protocol.php?op=delete_protocol_list&letter_id='.$letter_id, 'delete');
        echo $protocolAdmin->renderButton();
        $limit = $xnewsletter->getConfig('adminperpage');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('protocol_letter_id', $letter_id));
        $criteria->setSort("protocol_id");
        $criteria->setOrder("DESC");
        $numrows = $xnewsletter->getHandler('xNewsletter_protocol')->getCount($criteria);
        $start = xNewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $protocol_arr = $xnewsletter->getHandler('xNewsletter_protocol')->getall($criteria);
        if ($numrows > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
              $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', 'op=list_letter&letter_id='.$letter_id);
              $pagenav = $pagenav->renderNav(4);
            } else {
              $pagenav = '';
            }

        // View Table
        if ($numrows>0)
        {
          $obj_letter = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
          echo "<h2 style='text-align:center'>".$obj_letter->getVar("letter_title")."</h2>";
          echo "<table class='outer width100' cellspacing='1'>
            <tr>
              <th class='center width2'>"._AM_XNEWSLETTER_PROTOCOL_ID."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_STATUS."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_SUCCESS."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_SUBMITTER."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_CREATED."</th>
                <th class='center width10'>"._AM_XNEWSLETTER_FORMACTION."</th>
            </tr>";

          $class = "odd";
          $img_ok = "<img src='".XNEWSLETTER_ICONS_URL."/xn_ok.png' alt='"._AM_XNEWSLETTER_OK."' title='"._AM_XNEWSLETTER_OK."' />&nbsp;&nbsp;";
          $img_failed = "<img src='".XNEWSLETTER_ICONS_URL."/xn_failed.png' alt='"._AM_XNEWSLETTER_FAILED."' title='"._AM_XNEWSLETTER_FAILED."' />&nbsp;&nbsp;";

          foreach (array_keys($protocol_arr) as $i)
          {
            echo "<tr class='".$class."'>";
            $class = ($class == "even") ? "odd" : "even";
            echo "<td class='center'>".$i."</td>";
            $obj_subscr = $xnewsletter->getHandler('xNewsletter_subscr')->get($protocol_arr[$i]->getVar("protocol_subscriber_id"));
            $subscriber = ($obj_subscr) ? $obj_subscr->getVar("subscr_email") : _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
            if ($subscriber == "") $subscriber = "-";
            $success = ( $protocol_arr[$i]->getVar("protocol_success") == 1 ) ? $img_ok : $img_failed;
            echo "<td class='center'>".$subscriber."</td>";
            echo "<td class='center'>".$protocol_arr[$i]->getVar("protocol_status")."</td>";
            echo "<td class='center'>".$success."</td>";
            echo "<td class='center'>".XoopsUser::getUnameFromId($protocol_arr[$i]->getVar("protocol_submitter"),"S")."</td>";
            echo "<td class='center'>".formatTimeStamp($protocol_arr[$i]->getVar("protocol_created"),"L")."</td>";

            echo "<td class='center width5'>";
              //<a href='protocol.php?op=edit_protocol&protocol_id=".$i."'><img src=".XNEWSLETTER_ICONS_URL."/xn_edit.png alt='"._EDIT."' title='"._EDIT."' /></a>
            echo "<a href='protocol.php?op=delete_protocol&protocol_id=".$i."'><img src=".XNEWSLETTER_ICONS_URL."/xn_delete.png alt='"._DELETE."' title='"._DELETE."' /></a>
              </td>";
            echo "</tr>";
          }
          echo "</table><br /><br />";
          echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
          echo "<table class='outer width100' cellspacing='1'>
              <tr>
                  <th class='center width2'>"._AM_XNEWSLETTER_PROTOCOL_ID."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_LETTER_ID."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_STATUS."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_SUBMITTER."</th>
                <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_CREATED."</th>
                <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
              </tr>";
          echo "</table><br /><br />";
            }

    break;

    case "new_protocol":
        echo $protocolAdmin->addNavigation("protocol.php");
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, 'protocol.php?op=list', 'list');
        echo $protocolAdmin->renderButton();

        $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
        $form = $obj->getForm();
        $form->display();
    break;

    case "save_protocol":
        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
           redirect_header("protocol.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        if (isset($_REQUEST["protocol_id"])) {
           $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->get($_REQUEST["protocol_id"]);
        } else {
           $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
        }

        //Form protocol_letter_id
        $obj->setVar("protocol_letter_id", $_REQUEST["protocol_letter_id"]);
        //Form protocol_subscriber_id
        $obj->setVar("protocol_subscriber_id", $_REQUEST["protocol_subscriber_id"]);
        //Form protocol_status
        $obj->setVar("protocol_status", $_REQUEST["protocol_status"]);
    //Form protocol_success
        $obj->setVar("protocol_success", $_REQUEST["protocol_success"]);
        //Form protocol_submitter
        $obj->setVar("protocol_submitter", $_REQUEST["protocol_submitter"]);
        //Form protocol_created
        $obj->setVar("protocol_created", strtotime($_REQUEST["protocol_created"]));

    if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($obj)) {
       redirect_header("protocol.php?op=list", 2, _AM_XNEWSLETTER_FORMOK);
    }

    echo $obj->getHtmlErrors();
    $form =& $obj->getForm();
        $form->display();
    break;

    case "edit_protocol":
        echo $protocolAdmin->addNavigation("protocol.php");
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_NEWPROTOCOL, 'protocol.php?op=new_protocol', 'add');
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, 'protocol.php?op=list', 'list');
        echo $protocolAdmin->renderButton();
        $obj = $xnewsletter->getHandler('xNewsletter_protocol')->get($_REQUEST["protocol_id"]);
        $form = $obj->getForm();
        $form->display();
    break;

    case "delete_protocol":
        $obj =& $xnewsletter->getHandler('xNewsletter_protocol')->get($_REQUEST["protocol_id"]);
        if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header("protocol.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('xNewsletter_protocol')->delete($obj)) {
                redirect_header("protocol.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "protocol_id" => $_REQUEST["protocol_id"], "op" => "delete_protocol"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $obj->getVar("protocol_id")));
        }
    break;

  case "delete_protocol_list":
    $letter_id = isset($_REQUEST["letter_id"]) ? $_REQUEST["letter_id"] : 0;
    if ($letter_id > 0) {
      $obj_letter =& $xnewsletter->getHandler('xNewsletter_letter')->get($_REQUEST["letter_id"]);
      if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
          redirect_header("protocol.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        $sql = "DELETE FROM `".$xoopsDB->prefix("xnewsletter_protocol")."` WHERE `protocol_letter_id`=$letter_id;";
        $result = $xoopsDB->query($sql);
        if ($result) {
          redirect_header("protocol.php", 3, _AM_XNEWSLETTER_FORMDELOK);
        } else {
          redirect_header("protocol.php", 3, _AM_XNEWSLETTER_FORMDELNOTOK);
        }
      } else {
        xoops_confirm(array("ok" => 1, "letter_id" => $letter_id, "op" => "delete_protocol_list"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL_LIST, $obj_letter->getVar("letter_title")));
      }

    }
  break;
}
include "admin_footer.php";
