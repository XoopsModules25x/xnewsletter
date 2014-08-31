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
include 'admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op             = xnewsletterRequest::getString('op', 'list');
$attachment_id 	= xnewsletterRequest::getInt('attachment_id', 0);

switch ($op) {
    case 'list':
    default:
        echo $indexAdmin->addNavigation($currentFile);
        echo $indexAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $attachmentCriteria = new CriteriaCompo();
        $attachmentCriteria->setSort('attachment_letter_id DESC, attachment_id');
        $attachmentCriteria->setOrder('DESC');
        $attachmentCount = $xnewsletter->getHandler('attachment')->getCount();
        $start = xnewsletterRequest::getInt('start', 0);
        $attachmentCriteria->setStart($start);
        $attachmentCriteria->setLimit($limit);
        $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentCriteria, true);
        if ($attachmentCount > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($attachmentCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }
        // output table
        echo "<table class='outer' cellspacing='1'>";
        echo "<tr>";
        echo "    <th>" . _AM_XNEWSLETTER_ATTACHMENT_ID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_ATTACHMENT_LETTER_ID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_ATTACHMENT_NAME . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_ATTACHMENT_SIZE . "<br />" . _AM_XNEWSLETTER_ATTACHMENT_TYPE . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_ATTACHMENT_SUBMITTER . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_ATTACHMENT_CREATED . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_FORMACTION . "</th>";
        echo "</tr>";
        if ($attachmentCount > 0) {
            $class = 'odd';
            foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
                $letterObj = $xnewsletter->getHandler('letter')->get($attachmentObj->getVar('attachment_letter_id'));
                echo "<tr class='{$class}'>";
                $class = ($class == 'even') ? 'odd' : 'even';
                echo "<td class='center'>{$attachment_id}</td>";
                echo "<td>" . $letterObj->getVar('letter_title') . "</td>";
                echo "<td>" . $attachmentObj->getVar('attachment_name') . "</td>";
                echo "<td>";
                echo "<span title='" . $attachmentObj->getVar('attachment_size') . " B'>" . xnewsletter_bytesToSize1024($attachmentObj->getVar('attachment_size')) . "</span>";
                echo "<br />";
                echo $attachmentObj->getVar('attachment_type');
                echo "</td>";
                echo "<td>" . XoopsUser::getUnameFromId($attachmentObj->getVar('attachment_submitter'), 'S') . "</td>";
                echo "<td>" . formatTimeStamp($attachmentObj->getVar('attachment_created'), 'S') . "</td>";
                echo "<td>";
                echo "    <a href='?op=edit_attachment&attachment_id={$attachment_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo "    &nbsp;";
                echo "    <a href='?op=delete_attachment&attachment_id={$attachment_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                echo "</td>";
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<div>" . $pagenav . "</div>";
        echo "<br />";
        break;

    case 'edit_attachment':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $form = $attachmentObj->getForm();
        $form->display();
        break;

    case 'save_attachment':
        if (!$GLOBALS['xoopsSecurity']->check()) {
           redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        //
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $attachmentObj->setVar('attachment_mode', xnewsletterRequest::getInt('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT));
        //
        if ($xnewsletter->getHandler('attachment')->insert($attachmentObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
            exit();
        } else {
            echo $attachmentObj->getHtmlErrors();
            $form = $attachmentObj->getForm();
            $form->display();
        }
        break;

    case 'delete_attachment':
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('attachment')->delete($attachmentObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $attachmentObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array('ok' => true, 'attachment_id' => $attachment_id, 'op' => 'delete_attachment'), $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $attachmentObj->getVar('attachment_letter_id')));
        }
    break;
}

include 'admin_footer.php';
