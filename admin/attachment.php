<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
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
 * @copyright  Goffy ( wedega.com )
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_attachments.tpl';

// We recovered the value of the argument op in the URL$
$op            = Request::getString('op', 'list');
$attachment_id = Request::getInt('attachment_id', 0);

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $limit              = $helper->getConfig('adminperpage');
        $attachmentCriteria = new \CriteriaCompo();
        $attachmentCriteria->setSort('attachment_letter_id DESC, attachment_id');
        $attachmentCriteria->setOrder('DESC');
        $attachmentCount = $helper->getHandler('Attachment')->getCount();
        $start           = Request::getInt('start', 0);
        $attachmentCriteria->setStart($start);
        $attachmentCriteria->setLimit($limit);
        $attachmentsAll = $helper->getHandler('Attachment')->getAll($attachmentCriteria);
        if ($attachmentCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($attachmentCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }

        if ($attachmentCount > 0) {
            $GLOBALS['xoopsTpl']->assign('attachmentCount', $attachmentCount);
            $class = 'odd';
            foreach ($attachmentsAll as $attachment_id => $attachmentObj) {
                $attachment = $attachmentObj->getValuesAttachment();
                $letterObj = $helper->getHandler('Letter')->get($attachmentObj->getVar('attachment_letter_id'));
                $attachment['letter_title'] = $letterObj->getVar('letter_title');

                $attachment['attsize'] = xnewsletter_bytesToSize1024($attachmentObj->getVar('attachment_size'));
                $GLOBALS['xoopsTpl']->append('attachments_list', $attachment);
                unset($attachment);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_ATTACHMENT);
        }
        break;
    case 'edit_attachment':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $attachmentObj = $helper->getHandler('Attachment')->get($attachment_id);
        $form          = $attachmentObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_attachment':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $attachmentObj = $helper->getHandler('Attachment')->get($attachment_id);
        $attachmentObj->setVar('attachment_mode', Request::getInt('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT));

        if ($helper->getHandler('Attachment')->insert($attachmentObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        } else {
            $GLOBALS['xoopsTpl']->assign('error', $attachmentObj->getHtmlErrors());
            $form = $attachmentObj->getForm();
            $GLOBALS['xoopsTpl']->assign('form', $form->render());
        }
        break;
    case 'delete_attachment':
        $attachmentObj = $helper->getHandler('Attachment')->get($attachment_id);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Attachment')->delete($attachmentObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $attachmentObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'attachment_id' => $attachment_id, 'op' => 'delete_attachment'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $attachmentObj->getVar('attachment_name')));
        }
        break;
}

require_once __DIR__ . '/admin_footer.php';
