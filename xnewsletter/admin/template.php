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
$op          = XoopsRequest::getString('op', 'list');
$template_id = XoopsRequest::getInt('template_id', 0);

switch ($op) {
    case 'show_preview':
    case 'show_template_preview':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $templateObj = $xnewsletter->getHandler('template')->get($template_id);
        //
        $letterTpl = new XoopsTpl();
        // subscr data
        $letterTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW);
        $letterTpl->assign('salutation', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW); // new from v1.3
        $letterTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW);
        $letterTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
        $letterTpl->assign('subscr_email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW);
        $letterTpl->assign('email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW); // new from v1.3
        // letter data
        $letterTpl->assign('letter_id', ''); // new from v1.3
        $letterTpl->assign('title', '<{$title}>'); // new from v1.3
        $letterTpl->assign('content', xnewsletter_randomLipsum(5, 'paras', 0));
        //$letterTpl->assign('content', '<{$content}>');
        // letter attachments as link
        $letterTpl->assign('attachments', array());
        for ($i = 1; $i <= 5; ++$i) {
            $attachment_array['attachment_id']  = $i;
            $attachment_array['attachment_letter_id']  = '#';
            $attachment_array['attachment_name']  = '<{$attachment_name}>';
            $attachment_array['attachment_type']  = '<{$attachment_type}>';
            $attachment_array['attachment_submitter']  = '<{$attachment_submitter}>';
            $attachment_array['attachment_created']  = time();
            $attachment_array['attachment_size']  = '<{$attachment_size}>';
            $attachment_array['attachment_mode']  = '<{$attachment_mode}>';
            $attachment_array['attachment_url']  = '#';
            $attachment_array['attachment_link'] = '#';
            $letterTpl->append('attachments', $attachment_array);
        }
        // extra data
        $letterTpl->assign('date', time()); // new from v1.3
        $letterTpl->assign('unsubscribe_url', '#');
        $letterTpl->assign('catsubscr_id', '0');

        $htmlBody = $letterTpl->fetchFromData($templateObj->getVar('template_content', 'n'));
        $textBody = xnewsletter_html2text($htmlBody); // new from v1.3

        echo "<h2>{$templateObj->getVar('template_title')}</h2>";
        echo "<div style='clear:both'>";
        echo "<div style='padding:10px;border:1px solid black'>";
        echo $htmlBody;
        echo "</div>";
        echo "<div style='padding:10px;border:1px solid black; font-family: monospace;'>";
        echo nl2br(utf8_decode($textBody));
        echo "</div>";
        echo "</div>";
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'list':
    case 'list_templates':
    default:
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        echo $indexAdmin->renderButton();
        //
        $templateCount = $xnewsletter->getHandler('template')->getCount();
        $GLOBALS['xoopsTpl']->assign('templateCount', $templateCount);
        if ($templateCount > 0) {
            $templateCriteria = new CriteriaCompo();
            //
            $templateCriteria->setSort('template_title DESC, template_id');
            $templateCriteria->setOrder('DESC');
            //
            $start = XoopsRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $templateCriteria->setStart($start);
            $templateCriteria->setLimit($limit);
            //
            if ($templateCount > $limit) {
                xoops_load('xoopspagenav');
                $pagenav = new XoopsPageNav($templateCount, $limit, $start, 'start', 'op=list');
                $pagenav = $pagenav->renderNav(4);
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('subscrs_pagenav', $pagenav);
            //
            $templateObjs = $xnewsletter->getHandler('template')->getAll($templateCriteria);
            $templates = $xnewsletter->getHandler('template')->getObjects($templateCriteria, true, false); // as array
            foreach ($templates as $template_id => $template) {
                $template['template_submitter_uname'] = XoopsUser::getUnameFromId($template['template_submitter'], 'S');
                $template['template_created_formatted'] = formatTimestamp($template['template_created'], $xnewsletter->getConfig('dateformat'));
                $GLOBALS['xoopsTpl']->append('templates', $template);
            }
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_templates_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOTEMPLATES;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'new_template':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $templateObj = $xnewsletter->getHandler('template')->create();
        $form        = $templateObj->getForm();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'save_template':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $templateObj = $xnewsletter->getHandler('template')->get($template_id);
        $templateObj->setVar("template_title", XoopsRequest::getString('template_title', ''));
        $templateObj->setVar("template_description", $_REQUEST['template_description']);
        $templateObj->setVar("template_content", $_REQUEST['template_content']);
        $templateObj->setVar("template_submitter", XoopsRequest::getInt('template_submitter', 0));
        $templateObj->setVar("template_created", XoopsRequest::getInt('template_created', time()));
        //
        if ($xnewsletter->getHandler('template')->insert($templateObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }
        //
        echo $templateObj->getHtmlErrors();
        $form = $templateObj->getForm();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
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
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_template':
        $templateObj = $xnewsletter->getHandler('template')->get($template_id);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
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
        include_once __DIR__ . '/admin_footer.php';
        break;
}
