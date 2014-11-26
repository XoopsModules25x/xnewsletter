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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op          = xnewsletterRequest::getString('op', 'list');
$template_id = xnewsletterRequest::getInt('template_id', 0);

switch ($op) {
    case 'list':
    case 'list_templates':
    default:
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit            = $xnewsletter->getConfig('adminperpage');
        $templateCriteria = new CriteriaCompo();
        $templateCriteria->setSort('template_title DESC, template_id');
        $templateCriteria->setOrder('DESC');
        $templatesCount = $xnewsletter->getHandler('template')->getCount();
        $start          = xnewsletterRequest::getInt('start', 0);
        $templateCriteria->setStart($start);
        $templateCriteria->setLimit($limit);
        $templateObjs = $xnewsletter->getHandler('template')->getAll($templateCriteria);
        if ($templatesCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new XoopsPageNav($templatesCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "<table class='outer width100' cellspacing='1'>";
        echo "<tr>";
        echo "    <th>" . _AM_XNEWSLETTER_TEMPLATE_ID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_TEMPLATE_TITLE . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_TEMPLATE_DESCRIPTION . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_TEMPLATE_SUBMITTER . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_TEMPLATE_CREATED . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_FORMACTION . "</th>";
        echo "</tr>";

        if ($templatesCount > 0) {
            $class = 'odd';
            foreach ($templateObjs as $template_id => $templateObj) {
                echo "<tr class='{$class}'>";
                $class = ($class == 'even') ? 'odd' : 'even';
                echo "<td>" . $template_id . "</td>";
                echo "<td>" . $templateObj->getVar("template_title") . "</td>";
                echo "<td>" . $templateObj->getVar("template_description") . "</td>";
                echo "<td class='center'>" . XoopsUser::getUnameFromId($templateObj->getVar("template_submitter"), "S") . "</td>";
                echo "<td class='center'>" . formatTimeStamp($templateObj->getVar("template_created"), "S") . "</td>";
                echo "<td class='center' nowrap='nowrap'>";
                echo "    <a href='?op=edit_template&template_id=" . $template_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo "    &nbsp;";
                echo "    <a href='?op=delete_template&template_id=" . $template_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<div>{$pagenav}</div>";
        echo "<br />";
        break;

    case 'new_template':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $templateObj = $xnewsletter->getHandler('template')->create();
        $form        = $templateObj->getForm();
        $form->display();
        break;

    case 'save_template':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $templateObj = $xnewsletter->getHandler('template')->get($template_id);
        $templateObj->setVar("template_title", xnewsletterRequest::getString('template_title', ''));
        $templateObj->setVar("template_description", $_REQUEST['template_description']);
        $templateObj->setVar("template_content", $_REQUEST['template_content']);
        $templateObj->setVar("template_submitter", xnewsletterRequest::getInt('template_submitter', 0));
        $templateObj->setVar("template_created", xnewsletterRequest::getInt('template_created', time()));
        //
        if ($xnewsletter->getHandler('template')->insert($templateObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }
        //
        echo $templateObj->getHtmlErrors();
        $form = $templateObj->getForm();
        $form->display();
        break;

    case 'edit_template':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $templateObj = $xnewsletter->getHandler('template')->get($template_id);
        $form        = $templateObj->getForm();
        $form->display();
        break;

    case 'delete_template':
        $templateObj = $xnewsletter->getHandler('template')->get($template_id);
        if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('template')->delete($templateObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                array('ok' => true, 'template_id' => $template_id, 'op' => 'delete_template'),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $templateObj->getVar('template_title'))
            );
        }
        break;
}
include_once __DIR__ . '/admin_footer.php';
