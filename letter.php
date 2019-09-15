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
require_once __DIR__ . '/header.php';

$uid    = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : [0 => XOOPS_GROUP_ANONYMOUS];

$op        = Request::getString('op', 'list_letters');
$letter_id = Request::getInt('letter_id', 0);
$cat_id    = Request::getInt('cat_id', 0);

switch ($op) {
    case 'list_subscrs':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter_list_subscrs.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST_SUBSCR, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // check right to edit/delete subscription of other persons
        $permissionChangeOthersSubscriptions = false;
        foreach ($groups as $group) {
            if (in_array($group, $helper->getConfig('xn_groups_change_other')) || XOOPS_GROUP_ADMIN == $group) {
                $permissionChangeOthersSubscriptions = true;
                break;
            }
        }
        $xoopsTpl->assign('permissionChangeOthersSubscriptions', $permissionChangeOthersSubscriptions);
        // get search subscriber form
        if ($permissionChangeOthersSubscriptions) {
            require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
            $form = new \XoopsThemeForm(_AM_XNEWSLETTER_FORMSEARCH_SUBSCR_EXIST, 'form_search', 'subscription.php', 'post', true);
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, 'subscr_email', 60, 255, '', true));
            $form->addElement(new \XoopsFormButton('', 'submit', _AM_XNEWSLETTER_SEARCH, 'submit'));
            $xoopsTpl->assign('searchSubscriberForm', $form->render());
        } else {
            $xoopsTpl->assign('searchSubscriberForm', '');
        }
        // get cat objects
        $catCriteria = new \CriteriaCompo();
        $catCriteria->setSort('cat_id');
        $catCriteria->setOrder('ASC');
        $catObjs = $helper->getHandler('Cat')->getAll($catCriteria, null, true, true);
        // cats table
        foreach ($catObjs as $cat_id => $catObj) {
            $permissionShowCats[$cat_id] = $grouppermHandler->checkRight('newsletter_list_cat', $cat_id, $groups, $helper->getModule()->mid());
            if (true === $permissionShowCats[$cat_id]) {
                $cat_array         = $catObj->toArray();
                $catsubscrCriteria = new \CriteriaCompo();
                $catsubscrCriteria->add(new \Criteria('catsubscr_catid', $cat_id));
                $cat_array['catsubscrCount'] = $helper->getHandler('Catsubscr')->getCount($catsubscrCriteria);
                $xoopsTpl->append('cats', $cat_array);
            }
        }
        // get cat_id
        $cat_id = Request::getInt('cat_id', 0);
        $xoopsTpl->assign('cat_id', $cat_id);
        if ($cat_id > 0) {
            $catObj = $helper->getHandler('Cat')->get($cat_id);
            // subscrs table
            if (true === $permissionShowCats[$cat_id]) {
                $counter = 1;
                $sql     = 'SELECT `subscr_sex`, `subscr_lastname`, `subscr_firstname`, `subscr_email`, `subscr_id`';
                $sql     .= " FROM {$xoopsDB->prefix('xnewsletter_subscr')} INNER JOIN {$xoopsDB->prefix('xnewsletter_catsubscr')} ON `subscr_id` = `catsubscr_subscrid`";
                $sql     .= " WHERE (((`catsubscr_catid`)={$cat_id}) AND ((`catsubscr_quited`)=0)) ORDER BY `subscr_lastname`, `subscr_email`;";
                if (!$subscrs = $xoopsDB->query($sql)) {
                    die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
                }
                while ($subscr_array = mysqli_fetch_assoc($subscrs)) {
                    $subscr_array['counter'] = ++$counter;
                    $xoopsTpl->append('subscrs', $subscr_array);
                }
            }
        }
        break;
    case 'show_preview':
    case 'show_letter_preview':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter_preview.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_PREVIEW, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get letter_id
        $letter_id = Request::getInt('letter_id', 0);
        // get letter object
        $letterObj = $helper->getHandler('Letter')->get($letter_id);
        // subscr data
        $xoopsTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW);
        $xoopsTpl->assign('salutation', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW); // new from v1.3
        $xoopsTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW);
        $xoopsTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
        $xoopsTpl->assign('subscr_email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW);
        $xoopsTpl->assign('email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW); // new from v1.3
        // letter data
        $xoopsTpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
        $xoopsTpl->assign('content', $letterObj->getVar('letter_content', 'n'));
        // letter attachments as link
        $attachmentAslinkCriteria = new \CriteriaCompo();
        $attachmentAslinkCriteria->add(new \Criteria('attachment_letter_id', $letter_id));
        $attachmentAslinkCriteria->add(new \Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASLINK));
        $attachmentAslinkCriteria->setSort('attachment_id');
        $attachmentAslinkCriteria->setOrder('ASC');
        $attachmentObjs = $helper->getHandler('Attachment')->getObjects($attachmentAslinkCriteria, true);
        foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
            $attachment_array                    = $attachmentObj->toArray();
            $attachment_array['attachment_url']  = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
            $attachment_array['attachment_link'] = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
            $xoopsTpl->append('attachments', $attachment_array);
        }
        // extra data
        $xoopsTpl->assign('date', time()); // new from v1.3
        $xoopsTpl->assign('unsubscribe_url', XOOPS_URL . '/modules/xnewsletter/');
        $xoopsTpl->assign('catsubscr_id', '0');

        $letter_array = $letterObj->toArray();

        preg_match('/db:([0-9]*)/', $letterObj->getVar('letter_template'), $matches);
        if (isset($matches[1]) && ($templateObj = $helper->getHandler('Template')->get((int)$matches[1]))) {
            // get template from database
            $htmlBody = $xoopsTpl->fetchFromData($templateObj->getVar('template_content', 'n'));
        } else {
            // get template from filesystem
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
            if (!is_dir($template_path)) {
                $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
            }
            $template = $template_path . $letterObj->getVar('letter_template') . '.tpl';
            $htmlBody = $xoopsTpl->fetch($template);
        }
        $textBody = xnewsletter_html2text($htmlBody); // new from v1.3

        $letter_array['letter_content_templated']      = $htmlBody;
        $letter_array['letter_content_templated_html'] = $htmlBody;
        $letter_array['letter_content_templated_text'] = $textBody; // new from v1.3
        $letter_array['letter_created_formatted']      = formatTimestamp($letterObj->getVar('letter_created'), $helper->getConfig('dateformat'));
        $letter_array['letter_submitter_name']         = \XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
        $xoopsTpl->assign('letter', $letter_array);
        break;
    case 'print_letter':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter_print.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        //$xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // get letter_id
        $letter_id = Request::getInt('letter_id', 0);
        // get letter object
        $letterObj = $helper->getHandler('Letter')->get($letter_id);
        // subscr data
        $xoopsTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW);
        $xoopsTpl->assign('salutation', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW); // new from v1.3
        $xoopsTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW);
        $xoopsTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
        $xoopsTpl->assign('subscr_email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW);
        $xoopsTpl->assign('email', _AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW); // new from v1.3
        // letter data
        $xoopsTpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
        $xoopsTpl->assign('content', $letterObj->getVar('letter_content', 'n'));
        // extra data
        $xoopsTpl->assign('date', time()); // new from v1.3
        $xoopsTpl->assign('unsubscribe_url', XOOPS_URL . '/modules/xnewsletter/');
        $xoopsTpl->assign('catsubscr_id', '0');

        $letter_array = $letterObj->toArray();

        preg_match('/db:([0-9]*)/', $letterObj->getVar('letter_template'), $matches);
        if (isset($matches[1]) && ($templateObj = $helper->getHandler('Template')->get((int)$matches[1]))) {
            // get template from database
            $htmlBody = $xoopsTpl->fetchFromData($templateObj->getVar('template_content', 'n'));
        } else {
            // get template from filesystem
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
            if (!is_dir($template_path)) {
                $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
            }
            $template = $template_path . $letterObj->getVar('letter_template') . '.tpl';
            $htmlBody = $xoopsTpl->fetch($template);
        }
        $textBody = xnewsletter_html2text($htmlBody); // new from v1.3

        $letter_array['letter_content_templated']      = $htmlBody;
        $letter_array['letter_content_templated_html'] = $htmlBody;
        $letter_array['letter_content_templated_text'] = $textBody; // new from v1.3
        $letter_array['letter_created_formatted']      = formatTimestamp($letterObj->getVar('letter_created'), $helper->getConfig('dateformat'));
        $letter_array['letter_submitter_name']         = \XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
        $xoopsTpl->assign('letter', $letter_array);
        break;
    case 'list_letters':
    default:
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter_list_letters.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get letters array
        $letterCriteria = new \CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letterCount = $helper->getHandler('Letter')->getCount();
        $start       = Request::getInt('start', 0);
        $limit       = $helper->getConfig('adminperpage');
        $letterCriteria->setStart($start);
        $letterCriteria->setLimit($limit);
        $letterObjs = $helper->getHandler('Letter')->getAll($letterCriteria, null, true, true);

        // pagenav
        $pagenav = new \XoopsPageNav($letterCount, $limit, $start, 'start', "op={$op}");
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());

        // letters table
        $showAdminColumns = false;
        if ($letterCount > 0) {
            foreach ($letterObjs as $letter_id => $letterObj) {
                $userPermissions = xnewsletter_getUserPermissionsByLetter($letter_id);
                if (($userPermissions['read'] && $letterObj->getVar('letter_sent') > 0)
                    || (true === $userPermissions['send'])) {
                    $letter_array                             = $letterObj->toArray();
                    $letter_array['letter_created_formatted'] = formatTimestamp($letterObj->getVar('letter_created'), $helper->getConfig('dateformat'));
                    $letter_array['letter_submitter_name']    = \XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
                    $letter_array['letter_sent_formatted']    = 0 != $letterObj->getVar('letter_sent') ? formatTimestamp($letterObj->getVar('letter_sent'), $helper->getConfig('dateformat')) : '';
                    $letter_array['letter_sender_name']       = \XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_sender'));

                    preg_match('/db:([0-9]*)/', $letter_array['letter_template'], $matches);
                    if (isset($matches[1])
                        && ($templateObj = $helper->getHandler('Template')->get((int)$matches[1]))) {
                        $letter_array['letter_template'] = 'db:' . $templateObj->getVar('template_title');
                    } else {
                        $letter_array['letter_template'] = 'file:' . $letter_array['letter_template'];
                    }

                    $letter_cat_ids = explode('|', $letterObj->getVar('letter_cats'));
                    // skip letter
                    if ((0 != $cat_id) && !in_array($cat_id, $letter_cat_ids)) {
                        continue;
                    }
                    // get categories
                    $catsAvailableCount = 0;
                    unset($letter_array['letter_cats']); // IN PROGRESS
                    foreach ($letter_cat_ids as $letter_cat_id) {
                        $catObj = $helper->getHandler('Cat')->get($letter_cat_id);
                        if ($grouppermHandler->checkRight('newsletter_read_cat', $catObj->getVar('cat_id'), $groups, $helper->getModule()->mid())) {
                            ++$catsAvailableCount;
                            $letter_array['letter_cats'][] = $catObj->toArray();
                        }
                        unset($catObj);
                    }
                    if ($catsAvailableCount > 0) {
                        $letters_array[] = $letter_array;
                    }
                    // count letter attachements
                    $attachmentCriteria = new \CriteriaCompo();
                    $attachmentCriteria->add(new \Criteria('attachment_letter_id', $letterObj->getVar('letter_id')));
                    $letter_array['attachmentCount'] = $helper->getHandler('Attachment')->getCount($attachmentCriteria);
                    // get protocols
                    if ($userPermissions['edit']) {
                        // take last item protocol_subscriber_id=0 from table protocol as actual status
                        $protocolCriteria = new \CriteriaCompo();
                        $protocolCriteria->add(new \Criteria('protocol_letter_id', $letterObj->getVar('letter_id')));
                        //$criteria->add(new \Criteria('protocol_subscriber_id', '0'));
                        $protocolCriteria->setSort('protocol_id');
                        $protocolCriteria->setOrder('DESC');
                        $protocolCriteria->setLimit(1);
                        $protocolObjs       = $helper->getHandler('Protocol')->getAll($protocolCriteria);
                        $protocol_status    = '';
                        $protocol_letter_id = 0;
                        foreach ($protocolObjs as $protocolObj) {
                            $letter_array['protocols'][] = [
                                'protocol_status'    => $protocolObj->getVar('protocol_status'),
                                'protocol_letter_id' => $protocolObj->getVar('protocol_letter_id'),
                            ];
                        }
                    }
                    // check if table show admin columns
                    if ((true === $userPermissions['edit']) || (true === $userPermissions['delete'])
                        || (true === $userPermissions['create'])
                        || (true === $userPermissions['send'])) {
                        $showAdminColumns = true;
                    }
                    $letter_array['userPermissions'] = $userPermissions;
                    $xoopsTpl->append('letters', $letter_array);
                }
            }
        }
        $xoopsTpl->assign('showAdminColumns', $showAdminColumns);
        break;
    case 'new_letter':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter.tpl"; // IN PROGRESS
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_CREATE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $letterObj = $helper->getHandler('Letter')->create();
        $form      = $letterObj->getForm();
        $content   = $form->render();
        $xoopsTpl->assign('content', $content);
        break;
    case 'edit_letter':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_EDIT, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $letterObj = $helper->getHandler('Letter')->get($letter_id);
        $form      = $letterObj->getForm();
        $content   = $form->render();
        $xoopsTpl->assign('content', $content);
        break;
    case 'delete_attachment':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_EDIT, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // update existing_attachments
        $existing_attachments_mode = Request::getArray('existing_attachments_mode', []);
        foreach ($existing_attachments_mode as $attachment_id => $attachment_mode) {
            $attachmentObj = $helper->getHandler('Attachment')->get($attachment_id);
            $attachmentObj->setVar('attachment_mode', $attachment_mode);
            $helper->getHandler('Attachment')->insert($attachmentObj);
        }

        $attachment_id = Request::getInt('deleted_attachment_id', 0, 'POST');
        if (0 == $attachment_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID);
        }
        $attachmentObj   = $helper->getHandler('Attachment')->get($attachment_id);
        $attachment_name = $attachmentObj->getVar('attachment_name');

        if ($helper->getHandler('Attachment')->delete($attachmentObj, true)) {
            $letterObj = $helper->getHandler('Letter')->get($letter_id);
            $letterObj->setVar('letter_title', Request::getString('letter_title', ''));
            $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
            $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
            $letterObj->setVar('letter_cats', implode('|', Request::getArray('letter_cats', [])));
            $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
            $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);

            $form    = $letterObj->getForm(false, true);
            $content = $form->render();
            $xoopsTpl->assign('content', $content);
        } else {
            $content = $attachmentObj->getHtmlErrors();
            $xoopsTpl->assign('content', $content);
        }
        break;
    case 'save_letter':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_empty.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $letterObj = $helper->getHandler('Letter')->get($letter_id); // create if doesn't exist
        $letterObj->setVar('letter_title', Request::getString('letter_title', ''));
        $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
        $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
        $letterObj->setVar('letter_cats', implode('|', Request::getArray('letter_cats', [])));
        $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
        $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
        $letterObj->setVar('letter_submitter', Request::getInt('letter_submitter', 0));
        $letterObj->setVar('letter_created', Request::getInt('letter_created', time()));

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
            $uploaddir = XOOPS_UPLOAD_PATH . $helper->getConfig('xn_attachment_path') . $letter_id . '/';
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
                $attachmentObj->setVar('attachment_letter_id', $letter_id);
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
            $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_LETTER_ACTION_SAVED);
            $protocolObj->setVar('protocol_status_str_id', _XNEWSLETTER_PROTOCOL_STATUS_SAVED); // new from v1.3
            $protocolObj->setVar('protocol_status_vars', []); // new from v1.3
            $protocolObj->setVar('protocol_submitter', $xoopsUser->uid());
            $protocolObj->setVar('protocol_created', time());

            if ($helper->getHandler('Protocol')->insert($protocolObj)) {
                // create protocol is ok
                redirect_header($redirectUrl, 3, _AM_XNEWSLETTER_FORMOK);
            } else {
                echo 'Error create protocol: ' . $protocolObj->getHtmlErrors();
            }
        } else {
            echo 'Error create letter: ' . $letterObj->getHtmlErrors();
        }
        break;
    case 'copy_letter':
    case 'clone_letter':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_letter.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_COPY, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $oldLetterObj = $helper->getHandler('Letter')->get($letter_id);
        $newLetterObj = $helper->getHandler('Letter')->create();
        $newLetterObj->setVar('letter_title', sprintf(_AM_XNEWSLETTER_LETTER_CLONED, $oldLetterObj->getVar('letter_title')));
        $newLetterObj->setVar('letter_content', $oldLetterObj->getVar('letter_content', 'n'));
        $newLetterObj->setVar('letter_template', $oldLetterObj->getVar('letter_template'));
        $newLetterObj->setVar('letter_cats', $oldLetterObj->getVar('letter_cats'));
        $newLetterObj->setVar('letter_account', $oldLetterObj->getVar('letter_account'));
        $newLetterObj->setVar('letter_email_test', $oldLetterObj->getVar('letter_email_test'));
        unset($oldLetterObj);
        $action  = XOOPS_URL . "/modules/xnewsletter/{$currentFile}?op=copy_letter";
        $form    = $newLetterObj->getForm($action);
        $content = $form->render();
        $xoopsTpl->assign('content', $content);
        break;
    case 'delete_letter':
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_empty.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_DELETE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // IN PROGRESS FROM HERE

        $letterObj = $helper->getHandler('Letter')->get($letter_id);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Letter')->delete($letterObj)) {
                //delete protocols
                $sql = 'DELETE';
                $sql .= " FROM `{$xoopsDB->prefix('xnewsletter_protocol')}`";
                $sql .= " WHERE `protocol_letter_id`={$letter_id}";
                if (!$result = $xoopsDB->query($sql)) {
                    die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
                }
                // delete attachments
                $attachmentCriteria = new \Criteria('attachment_letter_id', $letter_id);
                $helper->getHandler('Attachment')->deleteAll($attachmentCriteria, true, true);
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $letterObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'letter_id' => $letter_id, 'op' => 'delete_letter'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $letterObj->getVar('letter_title')));
        }
        break;
}

require_once __DIR__ . '/footer.php';
