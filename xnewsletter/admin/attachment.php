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
$attachment_id 	= xNewsletter_CleanVars($_REQUEST, 'attachment_id', 0, 'int');

switch ($op) {
    case "list" :
    default :
        echo $indexAdmin->addNavigation('attachment.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWATTACHMENT, 'attachment.php?op=new_attachment', 'add');
        echo $indexAdmin->renderButton();
        $limit = $GLOBALS['xoopsModuleConfig']['adminperpage'];
        $criteria = new CriteriaCompo();
        $criteria->setSort("attachment_letter_id DESC, attachment_id");
        $criteria->setOrder("DESC");
        $numrows = $xnewsletter->getHandler('xNewsletter_attachment')->getCount();
        $start = xNewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $attachment_arr = $xnewsletter->getHandler('xNewsletter_attachment')->getall($criteria);
        if ($numrows > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($numrows>0) {
            echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>"._AM_XNEWSLETTER_ATTACHMENT_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_ATTACHMENT_LETTER_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_ATTACHMENT_NAME."</th>
                    <th class='center'>"._AM_XNEWSLETTER_ATTACHMENT_TYPE."</th>
                    <th class='center'>"._AM_XNEWSLETTER_ATTACHMENT_SUBMITTER."</th>
                    <th class='center'>"._AM_XNEWSLETTER_ATTACHMENT_CREATED."</th>
                    <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                </tr>
            ";

            $class = "odd";

            foreach (array_keys($attachment_arr) as $i) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>".$i."</td>";

                $letter =& $xnewsletter->getHandler('xNewsletter_letter')->get($attachment_arr[$i]->getVar("attachment_letter_id"));
                $title_letter = $letter->getVar("letter_title");
                echo "<td class='center'>" . $title_letter . "</td>";
                echo "<td class='center'>" . $attachment_arr[$i]->getVar("attachment_name") . "</td>";
                echo "<td class='center'>" .$attachment_arr[$i]->getVar("attachment_type")."</td>";
                echo "<td class='center'>" . XoopsUser::getUnameFromId($attachment_arr[$i]->getVar("attachment_submitter"), "S") . "</td>";
                echo "<td class='center'>" . formatTimeStamp($attachment_arr[$i]->getVar("attachment_created"), "S") . "</td>";

                echo "
                <td class='center width5' nowrap='nowrap'>
                    <a href='attachment.php?op=edit_attachment&attachment_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>
                    &nbsp;<a href='attachment.php?op=delete_attachment&attachment_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>
                </td>
                ";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_ATTACHMENT_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_LETTER_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_NAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_TYPE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_SUBMITTER . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
                </tr>
            </table><br /><br />
            ";
        }
        break;

    case "new_attachment" :
        echo $indexAdmin->addNavigation("attachment.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, 'attachment.php?op=list', 'list');
        echo $indexAdmin->renderButton();

        $obj =& $xnewsletter->getHandler('xNewsletter_attachment')->create();
        $form = $obj->getForm();
        $form->display();
        break;

    case "save_attachment" :
        if (!$GLOBALS["xoopsSecurity"]->check()) {
           redirect_header("attachment.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }

        $obj =& $xnewsletter->getHandler('xNewsletter_attachment')->get($attachment_id);
        //Form attachment_letter_id
        $obj->setVar("attachment_letter_id",    xNewsletter_CleanVars($_REQUEST, "attachment_letter_id", 0, "int"));
        //Form attachment_name
        $obj->setVar("attachment_name",         xNewsletter_CleanVars($_REQUEST, "attachment_name", "", "string"));
        //Form attachment_type
        $obj->setVar("attachment_type",         xNewsletter_CleanVars($_REQUEST, "attachment_type", 0, "int"));
        //Form attachment_submitter
        $obj->setVar("attachment_submitter",    xNewsletter_CleanVars($_REQUEST, "attachment_submitter", 0, "int"));
        //Form attachment_created
        $obj->setVar("attachment_created",      xNewsletter_CleanVars($_REQUEST, "attachment_created", time(), "int"));

        if ($xnewsletter->getHandler('xNewsletter_attachment')->insert($obj)) {
            redirect_header("attachment.php?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $obj->getHtmlErrors();
        $form =& $obj->getForm();
        $form->display();
        break;

    case "edit_attachment" :
        echo $indexAdmin->addNavigation("attachment.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWATTACHMENT, 'attachment.php?op=new_attachment', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, 'attachment.php?op=list', 'list');
        echo $indexAdmin->renderButton();
        $obj = $xnewsletter->getHandler('xNewsletter_attachment')->get($attachment_id);
        $form = $obj->getForm();
        $form->display();
        break;

    case "delete_attachment" :
        $obj =& $xnewsletter->getHandler('xNewsletter_attachment')->get($attachment_id);
        if (isset($_POST["ok"]) && $_POST["ok"] == 1) {
            if (!$GLOBALS["xoopsSecurity"]->check()) {
                redirect_header("attachment.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('xNewsletter_attachment')->delete($obj)) {
                redirect_header("attachment.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "attachment_id" => $attachment_id, "op" => "delete_attachment"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $obj->getVar("attachment_letter_id")));
        }
    break;
}
include "admin_footer.php";
