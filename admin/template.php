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

include "admin_header.php";
xoops_cp_header();
//global $pathIcon, $indexAdmin;

// We recovered the value of the argument op in the URL$
$op = xnewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');
$template_id 	= xnewsletter_CleanVars($_REQUEST, 'template_id', 0, 'int');

switch ($op) {
    case "list" :
    default :
        echo $indexAdmin->addNavigation('template.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, 'template.php?op=new_template', 'add');
        echo $indexAdmin->renderButton();
        $limit = $GLOBALS['xoopsModuleConfig']['adminperpage'];
        $criteria = new CriteriaCompo();
        $criteria->setSort("template_title DESC, template_id");
        $criteria->setOrder("DESC");
        $numrows = $xnewsletter->getHandler('xnewsletter_template')->getCount();
        $start = xnewsletter_CleanVars ( $_REQUEST, 'start', 0, 'int' );
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $template_arr = $xnewsletter->getHandler('xnewsletter_template')->getall($criteria);
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
                    <th class='center width2'>"._AM_XNEWSLETTER_TEMPLATE_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_TITLE."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_CONTENT."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_SUBMITTER."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_CREATED."</th>
                    <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                </tr>
            ";

            $class = "odd";

            foreach (array_keys($template_arr) as $i) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $i . "</td>";
                echo "<td class='center'>" . $template_arr[$i]->getVar("template_title") . "</td>";
                echo "<td class='center'>" . $template_arr[$i]->getVar("template_content") . "</td>";
                echo "<td class='center'>" . XoopsUser::getUnameFromId($template_arr[$i]->getVar("template_submitter"), "S") . "</td>";
                echo "<td class='center'>" . formatTimeStamp($template_arr[$i]->getVar("template_created"), "S") . "</td>";
                echo "
                <td class='center width5' nowrap='nowrap'>
                    <a href='template.php?op=edit_template&template_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>
                    &nbsp;<a href='template.php?op=delete_template&template_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>
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
                    <th class='center width2'>"._AM_XNEWSLETTER_TEMPLATE_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_TITLE."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_CONTENT."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_SUBMITTER."</th>
                    <th class='center'>"._AM_XNEWSLETTER_TEMPLATE_CREATED."</th>
                    <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION."</th>
                </tr>
            </table><br /><br />
            ";
        }
        break;

    case "new_template" :
        echo $indexAdmin->addNavigation("template.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, 'template.php?op=list', 'list');
        echo $indexAdmin->renderButton();

        $obj =& $xnewsletter->getHandler('xnewsletter_template')->create();
        $form = $obj->getForm();
        $form->display();
        break;

    case "save_template" :
        if (!$GLOBALS["xoopsSecurity"]->check()) {
           redirect_header("template.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }

        $obj =& $xnewsletter->getHandler('xnewsletter_template')->get($template_id);
        //Form template_name
        $obj->setVar("template_title",        xnewsletter_CleanVars($_REQUEST, "template_title", "", "string"));
        //Form template_type
        $obj->setVar("template_content",      xnewsletter_CleanVars($_REQUEST, "template_content", "", "string"));
        //Form template_submitter
        $obj->setVar("template_submitter",    xnewsletter_CleanVars($_REQUEST, "template_submitter", 0, "int"));
        //Form template_created
        $obj->setVar("template_created",      xnewsletter_CleanVars($_REQUEST, "template_created", time(), "int"));

        if ($xnewsletter->getHandler('xnewsletter_template')->insert($obj)) {
            redirect_header("template.php?op=list", 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $obj->getHtmlErrors();
        $form =& $obj->getForm();
        $form->display();
        break;

    case "edit_template" :
        echo $indexAdmin->addNavigation("template.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, 'template.php?op=new_template', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, 'template.php?op=list', 'list');
        echo $indexAdmin->renderButton();
        $obj = $xnewsletter->getHandler('xnewsletter_template')->get($template_id);
        $form = $obj->getForm();
        $form->display();
        break;

    case "delete_template" :
        $obj =& $xnewsletter->getHandler('xnewsletter_template')->get($template_id);
        if (isset($_POST["ok"]) && $_POST["ok"] == 1) {
            if (!$GLOBALS["xoopsSecurity"]->check()) {
                redirect_header("template.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('xnewsletter_template')->delete($obj)) {
                redirect_header("template.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "template_id" => $template_id, "op" => "delete_template"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $obj->getVar("template_letter_id")));
        }
    break;
}
include "admin_footer.php";
