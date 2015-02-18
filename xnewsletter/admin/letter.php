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
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';

// We recovered the value of the argument op in the URL$
$op        = XoopsRequest::getString('op', 'list');
$letter_id = XoopsRequest::getInt('letter_id', 0);

switch ($op) {
    case 'show_preview':
    case 'show_letter_preview':
        // render start here
        xoops_cp_header();
        // render submenu
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
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
        $letterTpl->assign('letter_id', $letter_id); // new from v1.3
        $letterTpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
        $letterTpl->assign('content', $letterObj->getVar('letter_content', 'n'));
        // letter attachments as link
        $attachmentAslinkCriteria = new CriteriaCompo();
        $attachmentAslinkCriteria->add(new Criteria('attachment_letter_id', $letter_id));
        $attachmentAslinkCriteria->add(new Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASLINK));
        $attachmentAslinkCriteria->setSort('attachment_id');
        $attachmentAslinkCriteria->setOrder('ASC');
        $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentAslinkCriteria, true);
        $letterTpl->assign('attachments', array());
        foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
            $attachment_array                    = $attachmentObj->toArray();
            $attachment_array['attachment_url']  = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
            $attachment_array['attachment_link'] = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
            $letterTpl->append('attachments', $attachment_array);
        }
        // extra data
        $letterTpl->assign('date', time()); // new from v1.3
        $letterTpl->assign('unsubscribe_url', '#');
        $letterTpl->assign('catsubscr_id', '0');

        preg_match('/db:([0-9]*)/', $letterObj->getVar('letter_template'), $matches);
        if (isset($matches[1]) && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
            // get template from database
            $htmlBody = $letterTpl->fetchFromData($templateObj->getVar('template_content', 'n'));
        } else {
            // get template from filesystem
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
            if (!is_dir($template_path)) {
                $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
            }
            $template = $template_path . $letterObj->getVar('letter_template') . '.tpl';
            $htmlBody = $letterTpl->fetch($template);
        }
        $textBody = xnewsletter_html2text($htmlBody); // new from v1.3

        echo "<h2>{$letterObj->getVar("letter_title")}</h2>";
        echo "<div style='clear:both'>";
        echo "<div style='padding:0px; margin:0px; border:none;'>";
        echo $htmlBody;
        echo "</div>";
        echo "<div style='padding:10px; margin:0px; border:1px solid black; font-family: monospace;'>";
        echo nl2br(utf8_decode($textBody));
        echo "</div>";
        echo "</div>";
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'list':
    case 'list_letters':
    default:
        // render start here
        xoops_cp_header();
        // render submenu
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        echo $indexAdmin->renderButton();
        //
        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letterCount = $xnewsletter->getHandler('letter')->getCount();
        $GLOBALS['xoopsTpl']->assign('letterCount', $letterCount);
        if ($letterCount > 0) {
            $limit = $xnewsletter->getConfig('adminperpage');
            $start = XoopsRequest::getInt('start', 0);
            $letterCriteria->setStart($start);
            $letterCriteria->setLimit($limit);
            //
            $letterObjs = $xnewsletter->getHandler('letter')->getObjects($letterCriteria, true);
            $letters = $xnewsletter->getHandler('letter')->getObjects($letterCriteria, true, false); // as array
            // pagenav
            if ($letterCount > $limit) {
                xoops_load('xoopspagenav');
                $pagenav = new XoopsPageNav($letterCount, $limit, $start, 'start', 'op=list');
                $pagenav = $pagenav->renderNav();
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('letters_pagenav', $pagenav);
            // fill letters array
            foreach ($letterObjs as $letter_id => $letterObj) {
                $letter = $letterObj->toArray();
                //
                $letter_cat_ids = explode('|', $letter['letter_cats']);
                unset($letter['letter_cats']);
                foreach ($letter_cat_ids as $letter_cat_id) {
                    if ($catObj = $xnewsletter->getHandler('cat')->get($letter_cat_id)) {
                        $cat = $catObj->toArray();
                        $letter['letter_cats']['letter_cat_id'] = $cat;
                    }
                }
                //
                $letter['letter_submitter_uname'] = XoopsUser::getUnameFromId($letter['letter_submitter'], 's');
                $letter['letter_created_formatted'] = formatTimeStamp($letter['letter_created'], 's');
                //
                $letter['letter_sender_uname'] = ($letter['letter_sender'] != 0) ? XoopsUser::getUnameFromId($letter['letter_sender'], 's') : '';
                $letter['letter_sent_formatted'] = ($letter['letter_sent'] != false) ? formatTimeStamp($letter['letter_sent'], 's') : '';
                //
                preg_match('/db:([0-9]*)/', $letter['letter_template'], $matches);
                if (isset($matches[1]) && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
                    $template = $templateObj->toArray();
                    $template['template_submitter_uname'] = XoopsUser::getUnameFromId($template['template_submitter'], 's');
                    $template['template_created_formatted'] = formatTimeStamp($template['template_created'], 's');
                } else {
                    $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
                    if (!is_dir($template_path)) {
                        $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
                    }
                    $templateFile = $template_path . $letter['letter_template'] . '.tpl';
                    $template = array(
                        'template_id' => null,
                        'template_title' => $letter['letter_template'],
                        'template_description' => '',
                        'template_content' => file_get_contents($templateFile),
                        'template_submitter' => null,
                        'template_submitter_uname' => '',
                        'template_created' => filemtime($templateFile),
                        'template_created_formatted' => formatTimeStamp(filemtime($templateFile), 's')
                    );
                }
                unset($letter['letter_template']);
                $letter['letter_template'] = $template;
                //
                $attachmentCriteria = new CriteriaCompo();
                $attachmentCriteria->add(new Criteria('attachment_letter_id', $letter_id));
                $attachmentCount = $xnewsletter->getHandler('attachment')->getCount($attachmentCriteria);
                $attachmentObjs  = $xnewsletter->getHandler('attachment')->getObjects($attachmentCriteria, true);
                $attachmentsSize = 0;
                foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
                    $attachment = $attachmentObj->toArray();
                    $attachmentsSize = $attachmentsSize + $attachment['attachment_size'];
                    $attachment['attachment_size1024'] = xnewsletter_bytesToSize1024($attachment['attachment_size']);
                    $letter['letter_attachments'][$attachment_id] = $attachment;
                }
                $letter['letter_attachments_size'] = $attachmentsSize;
                $letter['letter_attachments_size1024'] = xnewsletter_bytesToSize1024($attachmentsSize);
                //
                $emailSize = xnewsletter_emailSize($letter_id);
                $letter['letter_size'] = $emailSize;
                $letter['letter_size1024'] = xnewsletter_bytesToSize1024($emailSize);
                //
                $accountCriteria = new CriteriaCompo();
                $accountCriteria->setSort('accounts_id');
                $accountCriteria->setOrder('ASC');
                $accountObj = $xnewsletter->getHandler('accounts')->get($letter['letter_account']);
                $account = $accountObj->toArray();
                unset($letter['letter_account']);
                $letter['letter_account'] = $account;
                //
                // take last item protocol_subscriber_id=0 from table protocol as actual status
                $protocolCriteria = new CriteriaCompo();
                $protocolCriteria->add(new Criteria('protocol_letter_id', $letter_id));
                $protocolCriteria->add(new Criteria('protocol_subscriber_id', '0'));
                $protocolCriteria->setSort('protocol_id');
                $protocolCriteria->setOrder('DESC');
                $protocolCriteria->setLimit(1);
                $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
                foreach ($protocolObjs as $protocol_id => $protocolObj) {
                    $protocol = $protocolObj->toArray();
                    $letter['letter_protocols'][$protocol_id] = $protocol;
                }
                //
                $GLOBALS['xoopsTpl']->append('letters', $letter);
            }
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_letters_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOLETTERS;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'new_letter':
        // render start here
        xoops_cp_header();
        // render submenu
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $letterObj = $xnewsletter->getHandler('letter')->create();
        $form      = $letterObj->getForm(false, true);
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'edit_letter':
        // render start here
        xoops_cp_header();
        // render submenu
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $form      = $letterObj->getForm(false, true);
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_attachment':
        // render start here
        xoops_cp_header();
        // render submenu
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        // update existing_attachments
        $existing_attachments_mode = XoopsRequest::getArray('existing_attachments_mode', array());
        foreach ($existing_attachments_mode as $existing_attachment_id => $existing_attachment_mode) {
            $attachmentObj = $xnewsletter->getHandler('attachment')->get($existing_attachment_id);
            $attachmentObj->setVar('attachment_mode', $existing_attachment_mode);
            $xnewsletter->getHandler('attachment')->insert($attachmentObj);
        }
        //
        $attachment_id = XoopsRequest::getInt('deleted_attachment_id', 0, 'POST');
        if ($attachment_id == 0) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID);
            exit();
        }
        $attachmentObj   = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $attachment_name = $attachmentObj->getVar('attachment_name');
        //
        if ($xnewsletter->getHandler('attachment')->delete($attachmentObj, true)) {
            //
            $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
            $letterObj->setVar('letter_title', XoopsRequest::getString('letter_title', ''));
            $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
            $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
            $letterObj->setVar('letter_cats', implode('|', XoopsRequest::getArray('letter_cats', array())));
            $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
            $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
            //
            $form = $letterObj->getForm(false, true);
            $form->display();
        } else {
            echo $attachmentObj->getHtmlErrors();
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'save_letter':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id); // create if doesn't exist
        $letterObj->setVar('letter_title', XoopsRequest::getString('letter_title', ''));
        $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
        $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
        $letterObj->setVar('letter_cats', implode('|', XoopsRequest::getArray('letter_cats', array())));
        $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
        $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
        $letterObj->setVar('letter_submitter', XoopsRequest::getInt('letter_submitter', 0));
        $letterObj->setVar('letter_created', XoopsRequest::getInt('letter_created', time()));
        //
        if ($xnewsletter->getHandler('letter')->insert($letterObj)) {
            $letter_id = $letterObj->getVar('letter_id');
            // update existing_attachments
            $existing_attachments_mode = XoopsRequest::getArray('existing_attachments_mode', array());
            foreach ($existing_attachments_mode as $attachment_id => $attachment_mode) {
                $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
                $attachmentObj->setVar('attachment_mode', $attachment_mode);
                $xnewsletter->getHandler('attachment')->insert($attachmentObj);
            }
            // upload attachments
            $uploadedFiles = array();
            include_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . '/';
            // check upload_dir
            if (!is_dir($uploaddir)) {
                $indexFile = XOOPS_UPLOAD_PATH . "/index.html";
                mkdir($uploaddir, 0777);
                chmod($uploaddir, 0777);
                copy($indexFile, $uploaddir . "index.html");
            }
            $new_attachments_mode = XoopsRequest::getArray('new_attachments_mode', array());
            for ($upl = 0; $upl < $xnewsletter->getConfig('xn_maxattachments'); ++$upl) {
                $uploader = new XoopsMediaUploader($uploaddir, $xnewsletter->getConfig('xn_mimetypes'), $xnewsletter->getConfig('xn_maxsize'), null, null);
                if ($uploader->fetchMedia(@$_POST['xoops_upload_file'][$upl])) {
                    //$uploader->setPrefix("xn_") ; keep original name
                    $uploader->fetchMedia($_POST['xoops_upload_file'][$upl]);
                    if (!$uploader->upload()) {
                        // ERROR
                        echo $uploader->getErrors();
                        exit();
                    } else {
                        preg_match('/ne\w_attachment_index=([0-9]+)/', $_POST['xoops_upload_file'][$upl], $matches);
                        $index = $matches[1];
                        $uploadedFiles[] = array(
                            'name' => $uploader->getSavedFileName(),
                            'type' => $uploader->getMediaType(),
                            'size' => $uploader->getMediaSize(),
                            'mode' => $new_attachments_mode[$index]
                        );
                    }
                }
            }
            // create items in attachments
            foreach ($uploadedFiles as $file) {
                $attachmentObj = $xnewsletter->getHandler('attachment')->create();
                $attachmentObj->setVar('attachment_letter_id', $letter_id);
                $attachmentObj->setVar('attachment_name', $file['name']);
                $attachmentObj->setVar('attachment_type', $file['type']);
                $attachmentObj->setVar('attachment_submitter', $GLOBALS['xoopsUser']->uid());
                $attachmentObj->setVar('attachment_created', time());
                $attachmentObj->setVar('attachment_size', $file['size']);
                $attachmentObj->setVar('attachment_mode', $file['mode']);
                //
                $xnewsletter->getHandler('attachment')->insert($attachmentObj);
            }
            $action = XoopsRequest::getInt('letter_action', _XNEWSLETTER_LETTER_ACTION_VAL_NO);
            switch ($action) {
                case _XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW:
                    $redirectUrl = "?op=show_preview&letter_id={$letter_id}";
                    break;
                case _XNEWSLETTER_LETTER_ACTION_VAL_SEND:
                    $redirectUrl = "sendletter.php?op=send_letter&letter_id={$letter_id}";
                    break;
                case _XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST:
                    $redirectUrl = "sendletter.php?op=send_test&letter_id={$letter_id}";
                    break;
                default:
                    $redirectUrl = '?op=list_letters';
                    break;
            }
            // create item in protocol
            $xnewsletter->getHandler('protocol')->protocol($letter_id, 0, _AM_XNEWSLETTER_LETTER_ACTION_SAVED, _XNEWSLETTER_PROTOCOL_STATUS_SAVED, array(), true);
/*
            $protocolObj = $xnewsletter->getHandler('protocol')->create();
            $protocolObj->setVar('protocol_letter_id', $letter_id);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_success', true);
            $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_LETTER_ACTION_SAVED); // old style
            $protocolObj->setVar('protocol_status_str_id', _XNEWSLETTER_PROTOCOL_STATUS_SAVED); // new from v1.3
            $protocolObj->setVar('protocol_status_vars', array()); // new from v1.3
            $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
            $protocolObj->setVar('protocol_created', time());
            //
            if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
                // create protocol is ok
                redirect_header($redirectUrl, 3, _AM_XNEWSLETTER_FORMOK);
            } else {
                echo 'Error create protocol: ' . $protocolObj->getHtmlErrors();
            }
*/
            redirect_header($redirectUrl, 3, _AM_XNEWSLETTER_FORMOK);
        } else {
            echo 'Error create letter: ' . $letterObj->getHtmlErrors();
        }
        break;

    case 'clone_letter':
    case 'copy_letter':
        // render start here
        xoops_cp_header();
        // render submenu
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $letterObj->setNew();
        $letterObj->setVar('letter_id', 0);
        $letterObj->setVar('letter_title', sprintf(_AM_XNEWSLETTER_LETTER_CLONED, $letterObj->getVar('letter_title')));
        $form = $letterObj->getForm($currentFile, true);
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_letter':
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('letter')->delete($letterObj)) {
                //delete protocols
                $sql = "DELETE";
                $sql .= " FROM `{$GLOBALS['xoopsDB']->prefix('xnewsletter_protocol')}`";
                $sql .= " WHERE `protocol_letter_id`={$letter_id}";
                if (!$result = $GLOBALS['xoopsDB']->query($sql)) {
                    die('MySQL-Error: ' . mysql_error());
                }
                // delete attachments
                $attachmentCriteria = new Criteria('attachment_letter_id', $letter_id);
                $xnewsletter->getHandler('attachment')->deleteAll($attachmentCriteria, true, true);
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $letterObj->getHtmlErrors();
            }
        } else {
            // render start here
            xoops_cp_header();
            // render confirm form
            xoops_confirm(
                array('ok' => true, 'letter_id' => $letter_id, 'op' => 'delete_letter'),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $letterObj->getVar('letter_title'))
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;
}
