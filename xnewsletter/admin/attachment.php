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
$op            = XoopsRequest::getString('op', 'list');
$attachment_id = XoopsRequest::getInt('attachment_id', 0);

switch ($op) {
    case 'list':
    case 'list_attachments':
    default:
        echo $indexAdmin->addNavigation($currentFile);
        echo $indexAdmin->renderButton();
        //
        $attachmentCount = $xnewsletter->getHandler('attachment')->getCount();
        $GLOBALS['xoopsTpl']->assign('attachmentCount', $attachmentCount);
        if ($attachmentCount > 0) {
            $attachmentCriteria = new CriteriaCompo();
            //
            $attachmentCriteria->setSort('attachment_letter_id DESC, attachment_id');
            $attachmentCriteria->setOrder('DESC');
            //
            $start = XoopsRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $attachmentCriteria->setStart($start);
            $attachmentCriteria->setLimit($limit);
            //
            if ($attachmentCount > $limit) {
                xoops_load('xoopspagenav');
                $pagenav = new XoopsPageNav($attachmentCount, $limit, $start, 'start', 'op=list');
                $pagenav = $pagenav->renderNav(4);
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('attachments_pagenav', $pagenav);
            //
            $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentCriteria, true);
            $attachments = $xnewsletter->getHandler('attachment')->getObjects($attachmentCriteria, true, false); // as array
            $GLOBALS['xoopsTpl']->assign('attachments', array());
            foreach ($attachments as $attachment_id => $attachment) {
                $letterObj = $xnewsletter->getHandler('letter')->get($attachment['attachment_letter_id']);
                $attachment['attachment_letter_title'] = $letterObj->getVar('letter_title');
                $attachment['attachment_size1024'] = xnewsletter_bytesToSize1024($attachment['attachment_size']);
                $attachment['attachment_submitter_uname'] = XoopsUser::getUnameFromId($attachment['attachment_submitter'], 'S');
                $attachment['attachment_created_formatted'] = formatTimestamp($attachment['attachment_created'], $xnewsletter->getConfig('dateformat'));
                $GLOBALS['xoopsTpl']->append('attachments', $attachment);
            }
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_attachments_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOATTACHMENTS;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'edit_attachment':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $form          = $attachmentObj->getForm();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'save_attachment':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        //
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $attachmentObj->setVar('attachment_mode', XoopsRequest::getInt('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT));
        //
        if ($xnewsletter->getHandler('attachment')->insert($attachmentObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
            exit();
        } else {
            echo $attachmentObj->getHtmlErrors();
            $form = $attachmentObj->getForm();
            $form->display();
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_attachment':
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('attachment')->delete($attachmentObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $attachmentObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                array('ok' => true, 'attachment_id' => $attachment_id, 'op' => 'delete_attachment'),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $attachmentObj->getVar('attachment_letter_id'))
            );
        }
        include_once __DIR__ . '/admin_footer.php';
        break;
}
