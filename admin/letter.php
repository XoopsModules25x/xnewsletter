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
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use Xmf\Request;
use XoopsModules\Xnewsletter;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_letters.tpl';

// We recovered the value of the argument op in the URL$
$op       = Request::getString('op', 'list');
$letterId = Request::getInt('letter_id', 0);

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'show_preview':
    case 'show_letter_preview':
        global $XoopsTpl;

        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $letterTpl = new \XoopsTpl();

        $letterObj = $helper->getHandler('Letter')->get($letterId);
        // subscr data
        $letterTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW);
        $letterTpl->assign('salutation', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW); // new from v1.3
        $letterTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW);
        $letterTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
        $letterTpl->assign('subscr_email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW);
        $letterTpl->assign('email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW); // new from v1.3
        // letter data
        $letterTpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
        $letterTpl->assign('content', $letterObj->getVar('letter_content', 'n'));
        // letter attachments as link
        $attachmentAslinkCriteria = new \CriteriaCompo();
        $attachmentAslinkCriteria->add(new \Criteria('attachment_letter_id', $letterId));
        $attachmentAslinkCriteria->add(new \Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASLINK));
        $attachmentAslinkCriteria->setSort('attachment_id');
        $attachmentAslinkCriteria->setOrder('ASC');
        $attachmentObjs = $helper->getHandler('Attachment')->getObjects($attachmentAslinkCriteria, true);
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

        $templateObj = $helper->getHandler('Template')->get($letterObj->getVar('letter_templateid'));
        if (is_object($templateObj)) {
            if ( (int)$templateObj->getVar('template_type') === _XNEWSLETTER_MAILINGLIST_TPL_CUSTOM_VAL) {
                // get template from database
                $htmlBody = $letterTpl->fetchFromData($templateObj->getVar('template_content', 'n'));
            } else {
                // get template from filesystem
                $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
                if (!is_dir($template_path)) {
                    $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
                }
                $template = $template_path . $templateObj->getVar('template_title') . '.tpl';
                $htmlBody = $letterTpl->fetch($template);
            }
            try {
                $textBody = xnewsletter_html2text($htmlBody);
            }
            catch (Html2TextException $e) {
                $helper->addLog($e);
            }
        } else {
            $htmlBody = _AM_XNEWSLETTER_TEMPLATE_ERR;
        }

        $preview =  "<h2>{$letterObj->getVar('letter_title')}</h2>";
        $preview .= "<div style='clear:both'>";
        $preview .= "<div style='padding:10px;border:1px solid #000000'>";
        $preview .= $htmlBody;
//        $preview .= '</div>';
//        $preview .= "<div style='padding:10px;border:1px solid black; font-family: monospace;'>";
        //$preview .= nl2br(utf8_encode($textBody));
        $preview .= '</div>';
        $preview .= '</div>';
        $GLOBALS['xoopsTpl']->assign('preview', $preview);
        break;
    case 'list_letters':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $limit          = $helper->getConfig('adminperpage');
        $letterCriteria = new \CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letterCount = $helper->getHandler('Letter')->getCount();
        $start       = Request::getInt('start', 0);
        $letterCriteria->setStart($start);
        $letterCriteria->setLimit($limit);
        $lettersAll = $helper->getHandler('Letter')->getAll($letterCriteria);

        if ($letterCount > $limit) {
            // pagenav
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($letterCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }

        if ($letterCount > 0) {
            $GLOBALS['xoopsTpl']->assign('letterCount', $letterCount);

            $class = 'odd';
            foreach ($lettersAll as $letter_id => $letterObj) {
                $letter = $letterObj->getValuesLetter();
                $letter_cat_ids = explode('|', $letter['letter_cats']);
                $cats = '';
                foreach ($letter_cat_ids as $letter_cat_id) {
                    $catObj = $helper->getHandler('Cat')->get($letter_cat_id);
                    if (is_object($catObj)) {
                        $cats .= $catObj->getVar('cat_name') . '<br>';
                    } else {
                        $cats .= 'Invalid cat_name<br>';
                    }
                }
                $letter['cats_text'] = $cats;
                // check whether template exist or not
                $templateObj = $helper->getHandler('Template')->get($letter['templateid']);
                $letter['template_err'] = false;
                if (is_object($templateObj)) {
                    if ($templateObj->getVar('template_type') === _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL) {
                        $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
                        if (!is_dir($template_path)) {
                            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
                        }
                        $filename = $template_path . $templateObj->getVar('template_title') . '.tpl';
                        if (!file_exists ( $filename )) {
                            $letter['template_err'] = true;
                            $letter['template_err_text'] = str_replace('%s', $template_path, _AM_XNEWSLETTER_TEMPLATE_ERR_FILE);
                        }
                    }
                } else {
                    $letter['template_err'] = true;
                    $letter['template_err_text'] = _AM_XNEWSLETTER_TEMPLATE_ERR_TABLE;
                }

                $attachments = '';
                $attachmentCriteria = new \CriteriaCompo();
                $attachmentCriteria->add(new \Criteria('attachment_letter_id', $letter_id));
                $attachmentCount = $helper->getHandler('Attachment')->getCount($attachmentCriteria);
                $attachmentObjs  = $helper->getHandler('Attachment')->getObjects($attachmentCriteria, true);
                $attachmentsSize = 0;
                if ($attachmentCount > 0) {
                    $attachmentsSize = 0;
                    $attachments .= '<br><br>' . _AM_XNEWSLETTER_LETTER_ATTACHMENT . ':<ul>';
                    foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
                        $attachmentsSize = $attachmentsSize + $attachmentObj->getVar('attachment_size');
                        $size            = xnewsletter_bytesToSize1024($attachmentObj->getVar('attachment_size'));
                        $attachments .=  "<li><span title='" . $attachmentObj->getVar('attachment_type') . ' ' . $size . "'>{$attachmentObj->getVar('attachment_name')}</span></li>";
                    }
                    $attachments .=  '</ul>';
                    $attachments .=  _AM_XNEWSLETTER_LETTER_ATTACHMENT_TOTALSIZE . ": <span title='" . $attachmentsSize . " Bytes'>" . xnewsletter_bytesToSize1024($attachmentsSize) . '</span>';
                }
                try {
                    $emailSize = xnewsletter_emailSize($letter_id);
                }
                catch (Html2TextException $e) {
                    $helper->addLog($e);
                }
                $lettersize =  _AM_XNEWSLETTER_LETTER_EMAIL_SIZE . ": <span title='" . $emailSize . ' Bytes (' . _AM_XNEWSLETTER_LETTER_EMAIL_SIZE_DESC . ")'>" . xnewsletter_bytesToSize1024($emailSize) . '</span>';
                $letter['size_attachments'] = $lettersize . $attachments;

                $accountCriteria = new \CriteriaCompo();
                $accountCriteria->setSort('accounts_id');
                $accountCriteria->setOrder('ASC');
                $accountObj     = $helper->getHandler('Accounts')->get($letterObj->getVar('letter_account'));
                $letter_account = $accountObj ? $accountObj->getVar('accounts_name') : _NONE;
                $letter['letter_account'] = $letter_account;

                // take last item protocol_subscriber_id=0 from table protocol as actual status
                $protocolCriteria = new \CriteriaCompo();
                $protocolCriteria->add(new \Criteria('protocol_letter_id', $letter_id));
                $protocolCriteria->add(new \Criteria('protocol_subscriber_id', '0'));
                $protocolCriteria->setSort('protocol_id');
                $protocolCriteria->setOrder('DESC');
                $protocolCriteria->setLimit(1);
                $protocolObjs       = $helper->getHandler('Protocol')->getAll($protocolCriteria);
                $protocol_status    = '';
                $protocol_letter_id = 0;
                foreach ($protocolObjs as $protocolObj) {
                    $protocol_status    .= $protocolObj->getVar('protocol_status');
                    $protocol_letter_id = $protocolObj->getVar('protocol_letter_id');
                }
                $letter['protocol_status'] = $protocol_status;
                $letter['protocol_letter_id'] = $protocol_letter_id;


                $GLOBALS['xoopsTpl']->append('letters_list', $letter);
                unset($subscr);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_LETTER);
        }
        break;
    case 'new_letter':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $letterObj = $helper->getHandler('Letter')->create();
        $form      = $letterObj->getForm(false, true);
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_letter':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $letterObj = $helper->getHandler('Letter')->get($letterId);
        $form      = $letterObj->getForm(false, true);
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_attachment':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));
        //
        // update existing_attachments
        $existing_attachments_mode = Request::getArray('existing_attachments_mode', []);
        foreach ($existing_attachments_mode as $existing_attachment_id => $existing_attachment_mode) {
            $attachmentObj = $helper->getHandler('Attachment')->get($existing_attachment_id);
            $attachmentObj->setVar('attachment_mode', $existing_attachment_mode);
            $helper->getHandler('Attachment')->insert($attachmentObj);
        }

        $attachment_id = Request::getInt('deleted_attachment_id', 0, 'POST');
        if (0 == $attachment_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID);
        }
        $attachmentObj   = $helper->getHandler('Attachment')->get($attachment_id);
        $attachment_name = $attachmentObj->getVar('attachment_name');

        if ($helper->getHandler('Attachment')->delete($attachmentObj, true)) {
            $letterObj = $helper->getHandler('Letter')->get($letterId);
            $letterObj->setVar('letter_title',      Request::getString('letter_title', ''));
            $letterObj->setVar('letter_content',    Request::getText('letter_content', ''));
            $letterObj->setVar('letter_templateid', Request::getInt('letter_templateid', 0));
            $letterObj->setVar('letter_cats',       implode('|', Request::getArray('letter_cats', [])));
            $letterObj->setVar('letter_account',    Request::getInt('letter_account', 0));
            $letterObj->setVar('letter_email_test', Request::getString('letter_email_test', ''));

            $form = $letterObj->getForm(false, true);
            $GLOBALS['xoopsTpl']->assign('form', $form->render());
        } else {
            $GLOBALS['xoopsTpl']->assign('error', $attachmentObj->getHtmlErrors());
        }
        break;
    case 'save_letter':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $letterObj = $helper->getHandler('Letter')->get($letterId); // create if doesn't exist
        $letterObj->setVar('letter_title',      Request::getString('letter_title', ''));
        $letterObj->setVar('letter_content',    Request::getText('letter_content', ''));
        $letterObj->setVar('letter_templateid', Request::getInt('letter_templateid', 0));
        $letterObj->setVar('letter_cats',       implode('|', Request::getArray('letter_cats', [])));
        $letterObj->setVar('letter_account',    Request::getInt('letter_account', 0));
        $letterObj->setVar('letter_email_test', Request::getString('letter_email_test', ''));
        $letterObj->setVar('letter_submitter',  Request::getInt('letter_submitter', 0));
        $letterObj->setVar('letter_created',    Request::getInt('letter_created', time()));

        if ($helper->getHandler('Letter')->insert($letterObj)) {
            $letter_id = $letterObj->getVar('letter_id');
            // update existing_attachments
            $existing_attachments_mode = Request::getArray('existing_attachments_mode', []);
            foreach ($existing_attachments_mode as $attachment_id => $attachment_mode) {
                $attachmentObj = $helper->getHandler('Attachment')->get($attachment_id);
                $attachmentObj->setVar('attachment_mode', $attachment_mode);
                $helper->getHandler('Attachment')->insert($attachmentObj);
            }
            // upload attachments
            $uploadedFiles = [];
            require_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $uploaddir = XOOPS_UPLOAD_PATH . $helper->getConfig('xn_attachment_path') . $letterId . '/';
            // check upload_dir
            if (!is_dir($uploaddir)) {
                $indexFile = XOOPS_UPLOAD_PATH . '/index.html';
                if (!mkdir($uploaddir, 0777) && !is_dir($uploaddir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploaddir));
                }
                chmod($uploaddir, 0777);
                copy($indexFile, $uploaddir . 'index.html');
            }
            $new_attachments_mode = Request::getArray('new_attachments_mode', []);
            for ($upl = 0; $upl < $helper->getConfig('xn_maxattachments'); ++$upl) {
                $uploader = new \XoopsMediaUploader($uploaddir, $helper->getConfig('xn_mimetypes'), $helper->getConfig('xn_maxsize'), null, null);
                if ($uploader->fetchMedia(@$_POST['xoops_upload_file'][$upl])) {
                    //$uploader->setPrefix("xn_") ; keep original name
                    $uploader->fetchMedia($_POST['xoops_upload_file'][$upl]);
                    if (!$uploader->upload()) {
                        $errors = $uploader->getErrors();
                        redirect_header('<script>javascript:history.go(-1)</script>', 3, $errors);
                    } else {
                        preg_match('/ne\w_attachment_index=([0-9]+)/', $_POST['xoops_upload_file'][$upl], $matches);
                        $index           = $matches[1];
                        $uploadedFiles[] = [
                            'name' => $uploader->getSavedFileName(),
                            'type' => $uploader->getMediaType(),
                            'size' => $uploader->getMediaSize(),
                            'mode' => $new_attachments_mode[$index],
                        ];
                    }
                }
            }
            // create items in attachments
            foreach ($uploadedFiles as $file) {
                $attachmentObj = $helper->getHandler('Attachment')->create();
                $attachmentObj->setVar('attachment_letter_id', $letterId);
                $attachmentObj->setVar('attachment_name', $file['name']);
                $attachmentObj->setVar('attachment_type', $file['type']);
                $attachmentObj->setVar('attachment_submitter', $xoopsUser->uid());
                $attachmentObj->setVar('attachment_created', time());
                $attachmentObj->setVar('attachment_size', $file['size']);
                $attachmentObj->setVar('attachment_mode', $file['mode']);

                $helper->getHandler('Attachment')->insert($attachmentObj);
            }
            // create item in protocol
            $protocolObj = $helper->getHandler('Protocol')->create();
            $protocolObj->setVar('protocol_letter_id', $letterId);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_success', true);
            $action = Request::getInt('letter_action', _XNEWSLETTER_LETTER_ACTION_VAL_NO);
            switch ($action) {
                case _XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW:
                    $redirectUrl = "?op=show_preview&letter_id={$letterId}";
                    break;
                case _XNEWSLETTER_LETTER_ACTION_VAL_SEND:
                    $redirectUrl = "sendletter.php?op=send_letter&letter_id={$letterId}";
                    break;
                case _XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST:
                    $redirectUrl = "sendletter.php?op=send_test&letter_id={$letterId}";
                    break;
                default:
                    $redirectUrl = '?op=list_letters';
                    break;
            }
            $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_LETTER_ACTION_SAVED); // old style
            $protocolObj->setVar('protocol_status_str_id', _XNEWSLETTER_PROTOCOL_STATUS_SAVED); // new from v1.3
            $protocolObj->setVar('protocol_status_vars', []); // new from v1.3
            $protocolObj->setVar('protocol_submitter', $xoopsUser->uid());
            $protocolObj->setVar('protocol_created', time());

            if ($helper->getHandler('Protocol')->insert($protocolObj)) {
                // create protocol is ok
                redirect_header($redirectUrl, 3, _AM_XNEWSLETTER_FORMOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $protocolObj->getHtmlErrors());
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', $letterObj->getHtmlErrors());
        }
        break;
    case 'clone_letter':
    case 'copy_letter':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $letterObj = $helper->getHandler('Letter')->get($letterId);
        $letterObj->setNew();
        $letterObj->setVar('letter_id', 0);
        $letterObj->setVar('letter_title', sprintf(_AM_XNEWSLETTER_LETTER_CLONED, $letterObj->getVar('letter_title')));
        $form = $letterObj->getForm($currentFile, true);
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_letter':
        $letterObj = $helper->getHandler('Letter')->get($letterId);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Letter')->delete($letterObj)) {
                //delete protocols
                $sql = 'DELETE';
                $sql .= " FROM `{$xoopsDB->prefix('xnewsletter_protocol')}`";
                $sql .= " WHERE `protocol_letter_id`={$letterId}";
                if (!$result = $xoopsDB->query($sql)) {
                    die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
                }
                // delete attachments
                $attachmentCriteria = new \Criteria('attachment_letter_id', $letterId);
                $helper->getHandler('Attachment')->deleteAll($attachmentCriteria, true, true);
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $letterObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'letter_id' => $letterId, 'op' => 'delete_letter'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $letterObj->getVar('letter_title')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
