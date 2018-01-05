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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op        = Request::getString('op', 'list');
$letter_id = Request::getInt('letter_id', 0);

switch ($op) {
    case 'show_preview':
    case 'show_letter_preview':
        global $XoopsTpl;

        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $adminObject->displayButton('left');
        //
        $letterTpl = new XoopsTpl();

        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
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
        $attachmentAslinkCriteria = new CriteriaCompo();
        $attachmentAslinkCriteria->add(new Criteria('attachment_letter_id', $letter_id));
        $attachmentAslinkCriteria->add(new Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASLINK));
        $attachmentAslinkCriteria->setSort('attachment_id');
        $attachmentAslinkCriteria->setOrder('ASC');
        $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentAslinkCriteria, true);
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

        echo "<h2>{$letterObj->getVar('letter_title')}</h2>";
        echo "<div style='clear:both'>";
        echo "<div style='padding:10px;border:1px solid black'>";
        echo $htmlBody;
        echo '</div>';
        echo "<div style='padding:10px;border:1px solid black; font-family: monospace;'>";
        echo nl2br(utf8_encode($textBody));
        echo '</div>';
        echo '</div>';
        break;

    case 'list_letters':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->displayButton('left');
        //
        $limit          = $xnewsletter->getConfig('adminperpage');
        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letterCount = $xnewsletter->getHandler('letter')->getCount();
        $start       = Request::getInt('start', 0);
        $letterCriteria->setStart($start);
        $letterCriteria->setLimit($limit);
        $letterObjs = $xnewsletter->getHandler('letter')->getObjects($letterCriteria, true);

        // pagenav
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new XoopsPageNav($letterCount, $limit, $start, 'start', 'op=list');
        $pagenav = $pagenav->renderNav();

        // View Table
        echo "<table class='outer' cellspacing='1'>
            <tr>
                <th>" . _AM_XNEWSLETTER_LETTER_ID . '</th>
                <th>' . _AM_XNEWSLETTER_LETTER_TITLE . '</th>
                <th>' . _AM_XNEWSLETTER_LETTER_CATS . "</th>
                <th style='white-space: nowrap'>" . _AM_XNEWSLETTER_LETTER_SUBMITTER . '<br>' . _AM_XNEWSLETTER_LETTER_CREATED . "</th>
                <th style='white-space: nowrap'>" . _AM_XNEWSLETTER_LETTER_SENDER . '<br>' . _AM_XNEWSLETTER_LETTER_SENT . '</th>
                <th>' . _AM_XNEWSLETTER_LETTER_TEMPLATE . '</th>
                <th>' . _AM_XNEWSLETTER_LETTER_ATTACHMENT . '<br>' . _AM_XNEWSLETTER_LETTER_SIZE . '</th>
                <th>' . _AM_XNEWSLETTER_LETTER_ACCOUNT . '</th>
                <th>' . _AM_XNEWSLETTER_LETTER_EMAIL_TEST . '</th>
                <th>' . _AM_XNEWSLETTER_PROTOCOL_LAST_STATUS . '</th>
                <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>
            </tr>';
        if ($letterCount > 0) {
            $class = 'odd';
            foreach ($letterObjs as $letter_id => $letterObj) {
                echo "<tr class='{$class}'>";
                $class = ('even' == $class) ? 'odd' : 'even';

                echo "<td>{$letter_id}</td>";

                echo "<td>{$letterObj->getVar('letter_title')}</td>";

                echo "<td style='white-space: nowrap'>";
                $letter_cat_ids = explode('|', $letterObj->getVar('letter_cats'));
                foreach ($letter_cat_ids as $letter_cat_id) {
                    $catObj = $xnewsletter->getHandler('cat')->get($letter_cat_id);
                    if (is_object($catObj)) {
                        echo $catObj->getVar('cat_name') . " <a href='cat.php?op=edit_cat&cat_id={$catObj->getVar('cat_id')}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' style='padding:1px'></a>";
                    } else {
                        $letter_cats .= 'Invalid cat_name';
                    }
                    echo '<br>';
                }
                echo '</td>';

                echo '<td>' . XoopsUser::getUnameFromId($letterObj->getVar('letter_submitter'), 's') . '<br>' . formatTimestamp($letterObj->getVar('letter_created'), 's') . '</td>';

                echo '<td>';
                if (0 != $letterObj->getVar('letter_sender')) {
                    echo XoopsUser::getUnameFromId($letterObj->getVar('letter_sender'), 's');
                }
                echo '<br>';
                if (false !== $letterObj->getVar('letter_sent')) {
                    echo formatTimestamp($letterObj->getVar('letter_sent'), 's');
                }
                echo '</td>';

                echo '<td>';
                preg_match('/db:([0-9]*)/', $letterObj->getVar('letter_template'), $matches);
                if (isset($matches[1])
                    && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
                    echo 'db:' . $templateObj->getVar('template_title');
                    echo " <a href='template.php?op=edit_template&template_id={$templateObj->getVar('template_id')}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' style='padding:1px'></a>";
                } else {
                    echo 'file:' . $letterObj->getVar('letter_template');
                }
                echo '</td>';

                $attachmentCriteria = new CriteriaCompo();
                $attachmentCriteria->add(new Criteria('attachment_letter_id', $letter_id));
                $attachmentCount = $xnewsletter->getHandler('attachment')->getCount($attachmentCriteria);
                $attachmentObjs  = $xnewsletter->getHandler('attachment')->getObjects($attachmentCriteria, true);
                echo "<td style='white-space: nowrap'>";
                //echo "({$attachmentCount})";
                $attachmentsSize = 0;
                if ($attachmentCount > 0) {
                    $attachmentsSize = 0;
                    echo '<ul>';
                    foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
                        $attachmentsSize = $attachmentsSize + $attachmentObj->getVar('attachment_size');
                        $size            = xnewsletter_bytesToSize1024($attachmentObj->getVar('attachment_size'));
                        echo "<li><span title='" . $attachmentObj->getVar('attachment_type') . ' ' . $size . "'>{$attachmentObj->getVar('attachment_name')}</span></li>";
                    }
                    echo '</ul>';
                    echo _AM_XNEWSLETTER_LETTER_ATTACHMENT_TOTALSIZE . ": <span title='" . $attachmentsSize . " Bytes'>" . xnewsletter_bytesToSize1024($attachmentsSize) . '</span>';
                    echo '<br>';
                }
                $emailSize = xnewsletter_emailSize($letter_id);
                echo _AM_XNEWSLETTER_LETTER_EMAIL_SIZE . ": <span title='" . $emailSize . ' Bytes (' . _AM_XNEWSLETTER_LETTER_EMAIL_SIZE_DESC . ")'>" . xnewsletter_bytesToSize1024($emailSize) . '</span>';
                echo '</td>';

                $accountCriteria = new CriteriaCompo();
                $accountCriteria->setSort('accounts_id');
                $accountCriteria->setOrder('ASC');
                $accountObj     = $xnewsletter->getHandler('accounts')->get($letterObj->getVar('letter_account'));
                $letter_account = $accountObj ? $accountObj->getVar('accounts_name') : _NONE;
                echo "<td>{$letter_account}</td>";

                echo "<td>{$letterObj->getVar('letter_email_test')}&nbsp;</td>";

                // take last item protocol_subscriber_id=0 from table protocol as actual status
                $protocolCriteria = new CriteriaCompo();
                $protocolCriteria->add(new Criteria('protocol_letter_id', $letter_id));
                $protocolCriteria->add(new Criteria('protocol_subscriber_id', '0'));
                $protocolCriteria->setSort('protocol_id');
                $protocolCriteria->setOrder('DESC');
                $protocolCriteria->setLimit(1);
                $protocolObjs       = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
                $protocol_status    = '';
                $protocol_letter_id = 0;
                foreach ($protocolObjs as $protocolObj) {
                    $protocol_status    .= $protocolObj->getVar('protocol_status');
                    $protocol_letter_id = $protocolObj->getVar('protocol_letter_id');
                }
                echo "<td class='center'><a href=' protocol.php?op=list_letter&letter_id={$protocol_letter_id}'>{$protocol_status}</a></td>";

                echo "<td class='center'>";
                echo "    <a href='?op=edit_letter&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_edit.png' alt='" . _EDIT . "' title='" . _EDIT . "' style='padding:1px'></a>";
                echo "    <a href='?op=clone_letter&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_clone.png' alt='" . _CLONE . "' title='" . _CLONE . "' style='padding:1px'></a>";
                echo "    <a href='?op=delete_letter&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'  style='padding:1px'></a>";
                echo '    <br>';
                echo "    <a href='sendletter.php?op=send_test&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_sendtest.png' alt='" . _AM_XNEWSLETTER_LETTER_ACTION_SENDTEST . "' title='" . _AM_XNEWSLETTER_LETTER_ACTION_SENDTEST . "' style='padding:1px'></a>";
                echo "    <a href='sendletter.php?op=send_letter&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_send.png' alt='" . _AM_XNEWSLETTER_LETTER_ACTION_SEND . "' title='" . _AM_XNEWSLETTER_LETTER_ACTION_SEND . "' style='padding:1px'></a>";
                echo "    <a href='sendletter.php?op=resend_letter&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_resend.png' alt='" . _AM_XNEWSLETTER_LETTER_ACTION_RESEND . "' title='" . _AM_XNEWSLETTER_LETTER_ACTION_RESEND . "' style='padding:1px'></a>";
                echo '    <br>';
                echo "    <a href='?op=show_preview&letter_id={$letter_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_preview.png' alt='" . _AM_XNEWSLETTER_LETTER_ACTION_PREVIEW . "' title='" . _AM_XNEWSLETTER_LETTER_ACTION_PREVIEW . "' style='padding:1px'></a>";
                echo "    <a href='" . XNEWSLETTER_URL . "/print.php?letter_id={$letter_id}' target='_BLANK' ><img src='" . XNEWSLETTER_ICONS_URL . "/printer.png' alt='" . _AM_XNEWSLETTER_LETTER_ACTION_PRINT . "' title='" . _AM_XNEWSLETTER_LETTER_ACTION_PRINT . "' style='padding:1px'></a>";
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '<br>';
        echo "<div>{$pagenav}</div>";
        echo '<br>';
        break;

    case 'new_letter':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $adminObject->displayButton('left');
        //
        $letterObj = $xnewsletter->getHandler('letter')->create();
        $form      = $letterObj->getForm(false, true);
        $form->display();
        break;

    case 'edit_letter':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $adminObject->displayButton('left');
        //
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $form      = $letterObj->getForm(false, true);
        $form->display();
        break;

    case 'delete_attachment':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $adminObject->displayButton('left');
        //
        // update existing_attachments
        $existing_attachments_mode = Request::getArray('existing_attachments_mode', []);
        foreach ($existing_attachments_mode as $existing_attachment_id => $existing_attachment_mode) {
            $attachmentObj = $xnewsletter->getHandler('attachment')->get($existing_attachment_id);
            $attachmentObj->setVar('attachment_mode', $existing_attachment_mode);
            $xnewsletter->getHandler('attachment')->insert($attachmentObj);
        }
        //
        $attachment_id = Request::getInt('deleted_attachment_id', 0, 'POST');
        if (0 == $attachment_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID);
        }
        $attachmentObj   = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $attachment_name = $attachmentObj->getVar('attachment_name');
        //
        if ($xnewsletter->getHandler('attachment')->delete($attachmentObj, true)) {
            //
            $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
            $letterObj->setVar('letter_title', Request::getString('letter_title', ''));
            $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
            $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
            $letterObj->setVar('letter_cats', implode('|', Request::getArray('letter_cats', [])));
            $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
            $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
            //
            $form = $letterObj->getForm(false, true);
            $form->display();
        } else {
            echo $attachmentObj->getHtmlErrors();
        }
        break;

    case 'save_letter':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id); // create if doesn't exist
        $letterObj->setVar('letter_title', Request::getString('letter_title', ''));
        $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
        $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
        $letterObj->setVar('letter_cats', implode('|', Request::getArray('letter_cats', [])));
        $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
        $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
        $letterObj->setVar('letter_submitter', Request::getInt('letter_submitter', 0));
        $letterObj->setVar('letter_created', Request::getInt('letter_created', time()));
        //
        if ($xnewsletter->getHandler('letter')->insert($letterObj)) {
            $letter_id = $letterObj->getVar('letter_id');
            // update existing_attachments
            $existing_attachments_mode = Request::getArray('existing_attachments_mode', []);
            foreach ($existing_attachments_mode as $attachment_id => $attachment_mode) {
                $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
                $attachmentObj->setVar('attachment_mode', $attachment_mode);
                $xnewsletter->getHandler('attachment')->insert($attachmentObj);
            }
            // upload attachments
            $uploadedFiles = [];
            require_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . '/';
            // check upload_dir
            if (!is_dir($uploaddir)) {
                $indexFile = XOOPS_UPLOAD_PATH . '/index.html';
                mkdir($uploaddir, 0777);
                chmod($uploaddir, 0777);
                copy($indexFile, $uploaddir . 'index.html');
            }
            $new_attachments_mode = Request::getArray('new_attachments_mode', []);
            for ($upl = 0; $upl < $xnewsletter->getConfig('xn_maxattachments'); ++$upl) {
                $uploader = new XoopsMediaUploader($uploaddir, $xnewsletter->getConfig('xn_mimetypes'), $xnewsletter->getConfig('xn_maxsize'), null, null);
                if ($uploader->fetchMedia(@$_POST['xoops_upload_file'][$upl])) {
                    //$uploader->setPrefix("xn_") ; keep original name
                    $uploader->fetchMedia($_POST['xoops_upload_file'][$upl]);
                    if (!$uploader->upload()) {
                        $errors = $uploader->getErrors();
                        redirect_header('javascript:history.go(-1)', 3, $errors);
                    } else {
                        preg_match('/ne\w_attachment_index=([0-9]+)/', $_POST['xoops_upload_file'][$upl], $matches);
                        $index           = $matches[1];
                        $uploadedFiles[] = [
                            'name' => $uploader->getSavedFileName(),
                            'type' => $uploader->getMediaType(),
                            'size' => $uploader->getMediaSize(),
                            'mode' => $new_attachments_mode[$index]
                        ];
                    }
                }
            }
            // create items in attachments
            foreach ($uploadedFiles as $file) {
                $attachmentObj = $xnewsletter->getHandler('attachment')->create();
                $attachmentObj->setVar('attachment_letter_id', $letter_id);
                $attachmentObj->setVar('attachment_name', $file['name']);
                $attachmentObj->setVar('attachment_type', $file['type']);
                $attachmentObj->setVar('attachment_submitter', $xoopsUser->uid());
                $attachmentObj->setVar('attachment_created', time());
                $attachmentObj->setVar('attachment_size', $file['size']);
                $attachmentObj->setVar('attachment_mode', $file['mode']);
                //
                $xnewsletter->getHandler('attachment')->insert($attachmentObj);
            }
            // create item in protocol
            $protocolObj = $xnewsletter->getHandler('protocol')->create();
            $protocolObj->setVar('protocol_letter_id', $letter_id);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_success', true);
            $action = Request::getInt('letter_action', _XNEWSLETTER_LETTER_ACTION_VAL_NO);
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
            $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_LETTER_ACTION_SAVED); // old style
            $protocolObj->setVar('protocol_status_str_id', _XNEWSLETTER_PROTOCOL_STATUS_SAVED); // new from v1.3
            $protocolObj->setVar('protocol_status_vars', []); // new from v1.3
            $protocolObj->setVar('protocol_submitter', $xoopsUser->uid());
            $protocolObj->setVar('protocol_created', time());
            //
            if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
                // create protocol is ok
                redirect_header($redirectUrl, 3, _AM_XNEWSLETTER_FORMOK);
            } else {
                echo 'Error create protocol: ' . $protocolObj->getHtmlErrors();
            }
        } else {
            echo 'Error create letter: ' . $letterObj->getHtmlErrors();
        }
        break;

    case 'clone_letter':
    case 'copy_letter':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWLETTER, '?op=new_letter', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTERLIST, '?op=list', 'list');
        $adminObject->displayButton('left');
        //
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $letterObj->setNew();
        $letterObj->setVar('letter_id', 0);
        $letterObj->setVar('letter_title', sprintf(_AM_XNEWSLETTER_LETTER_CLONED, $letterObj->getVar('letter_title')));
        $form = $letterObj->getForm($currentFile, true);
        $form->display();
        break;

    case 'delete_letter':
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('letter')->delete($letterObj)) {
                //delete protocols
                $sql = 'DELETE';
                $sql .= " FROM `{$xoopsDB->prefix('xnewsletter_protocol')}`";
                $sql .= " WHERE `protocol_letter_id`={$letter_id}";
                if (!$result = $xoopsDB->query($sql)) {
                    die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
                }
                // delete attachments
                $attachmentCriteria = new Criteria('attachment_letter_id', $letter_id);
                $xnewsletter->getHandler('attachment')->deleteAll($attachmentCriteria, true, true);
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $letterObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'letter_id' => $letter_id, 'op' => 'delete_letter'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $letterObj->getVar('letter_title')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
