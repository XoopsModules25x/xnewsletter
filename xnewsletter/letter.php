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
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id: letter.php 12559 2014-06-02 08:10:39Z beckmi $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once 'header.php';

$uid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);

$op = xnewsletterRequest::getString('op', 'list_letters');
$letter_id = xnewsletterRequest::getInt('letter_id', 0);
$cat_id = xnewsletterRequest::getInt('cat_id', 0);

$delete_att_1 = xnewsletterRequest::getString('delete_attachment_1', 'none');
$delete_att_2 = xnewsletterRequest::getString('delete_attachment_2', 'none');
$delete_att_3 = xnewsletterRequest::getString('delete_attachment_3', 'none');
$delete_att_4 = xnewsletterRequest::getString('delete_attachment_4', 'none');
$delete_att_5 = xnewsletterRequest::getString('delete_attachment_5', 'none');

if ($delete_att_1 != 'none') {
    $op = "delete_attachment";
    $id_del = 1;
} elseif ($delete_att_2 != 'none') {
    $op = "delete_attachment";
    $id_del = 2;
} elseif ($delete_att_3 != 'none') {
    $op = "delete_attachment";
    $id_del = 3;
} elseif ($delete_att_4 != 'none') {
    $op = "delete_attachment";
    $id_del = 4;
} elseif ($delete_att_5 != 'none') {
    $op = "delete_attachment";
    $id_del = 5;
} else {
    $id_del = 0;
}

switch ($op) {
    case 'list_subscrs':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter_list_subscrs.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST_SUBSCR, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // check right to edit/delete subscription of other persons
        $permissionChangeOthersSubscriptions = false;
        foreach ($groups as $group) {
            if (in_array($group, $xnewsletter->getConfig('xn_groups_change_other')) || XOOPS_GROUP_ADMIN == $group) {
                $permissionChangeOthersSubscriptions = true;
                break;
            }
        }
        $xoopsTpl->assign('permissionChangeOthersSubscriptions', $permissionChangeOthersSubscriptions);
        // get search subscriber form
        if ($permissionChangeOthersSubscriptions) {
            include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
            $form = new XoopsThemeForm(_AM_XNEWSLETTER_FORMSEARCH_SUBSCR_EXIST, 'form_search', 'subscription.php', 'post', true);
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, 'subscr_email', 60, 255, '', true));
            $form->addElement(new XoopsFormButton('', 'submit', _AM_XNEWSLETTER_SEARCH, 'submit'));
            $xoopsTpl->assign('searchSubscriberForm', $form->render());
        } else {
            $xoopsTpl->assign('searchSubscriberForm', '');
        }
        // get cat objects
        $catCriteria = new CriteriaCompo();
        $catCriteria->setSort('cat_id');
        $catCriteria->setOrder('ASC');
        $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria, null, true, true);
        // cats table
        foreach ($catObjs as $cat_id => $catObj) {
            $permissionShowCats[$cat_id] = $gperm_handler->checkRight('newsletter_list_cat', $cat_id, $groups, $xnewsletter->getModule()->mid());
            if ($permissionShowCats[$cat_id] == true) {
                $cat_array = $catObj->toArray();
                $catsubscrCriteria = new CriteriaCompo();
                $catsubscrCriteria->add(new Criteria('catsubscr_catid', $cat_id));
                $cat_array['catsubscrCount'] = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
                $xoopsTpl->append('cats', $cat_array);
            }
        }
        // get cat_id
        $cat_id = xnewsletterRequest::getInt('cat_id', 0);
        $xoopsTpl->assign('cat_id', $cat_id);
        if ($cat_id > 0) {
            $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
            // subscrs table
            if ($permissionShowCats[$cat_id] == true) {
                $counter = 1;
                $sql ="SELECT `subscr_sex`, `subscr_lastname`, `subscr_firstname`, `subscr_email`, `subscr_id`";
                $sql.= " FROM {$xoopsDB->prefix("xnewsletter_subscr")} INNER JOIN {$xoopsDB->prefix("xnewsletter_catsubscr")} ON `subscr_id` = `catsubscr_subscrid`";
                $sql.= " WHERE (((`catsubscr_catid`)={$cat_id}) AND ((`catsubscr_quited`)=0)) ORDER BY `subscr_lastname`, `subscr_email`;";
                if(!$subscrs = $xoopsDB->query($sql)) die ("MySQL-Error: " . mysql_error());
                while ($subscr_array = mysql_fetch_assoc($subscrs)) {
                    $subscr_array['counter'] = ++$counter;
                    $xoopsTpl->append('subscrs', $subscr_array);
                }
            }
        }
        break;

    case 'delete_attachment':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_empty.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

// IN PROGRESS FROM HERE
        // get attachment
        $attachment_id = xnewsletterRequest::getString("attachment_{$id_del}", 'none');
        if ($attachment_id == 'none') {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID);
        }
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $attachment_name = $attachmentObj->getVar("attachment_name");
        // delete attachment
        if ($xnewsletter->getHandler('attachment')->delete($attachmentObj, true)) {
            // delete file
            $uploadDir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id;
            if (file_exists($uploadDir . '/' . $attachment_name)) {
                unlink($uploadDir . '/' . $attachment_name);
            }
            // get letter
            $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
            $letterObj->setVar('letter_title', $_REQUEST['letter_title']);
            $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
            $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
// IN PROGRESS
// IN PROGRESS
// IN PROGRESS
            //Form letter_cats
            $letter_cats = '';
            //$cat_arr = isset($_REQUEST['letter_cats']) ? $_REQUEST['letter_cats'] : '';
            $cats_arr = xnewsletterRequest::getArray('letter_cats', array());
            if (count($cats_arr) > 0) {
                foreach ($cats_arr as $cat) {
                    $letter_cats .= $cat . '|';
                }
                $letter_cats = substr($letter_cats, 0, -1);
            } else {
                $letter_cats = $cats_arr;
            }
            //no cat
            if ($letter_cats == false) {
                $form = $letterObj->getForm();
                $content = $form->render();
                $xoopsTpl->assign('content', $content);
                break;
            }
            $letterObj->setVar('letter_cats', $letter_cats);
// IN PROGRESS
// IN PROGRESS
// IN PROGRESS
            $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
            $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
            // get letter form
            $form = $letterObj->getForm(false, true);
            $form->display();
        } else {
            echo $attachmentObj->getHtmlErrors();
        }
        break;

    case 'show_preview':
    case 'show_letter_preview':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter_preview.tpl";
        include XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_PREVIEW, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get letter_id
        $letter_id = xnewsletterRequest::getInt('letter_id', 0);
        // get letter object
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
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

        preg_match('/db:([0-9]*)/', $letterObj->getVar("letter_template"), $matches);
        if(isset($matches[1]) && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
            // get template from database
            $htmlBody = $xoopsTpl->fetchFromData($templateObj->getVar('template_content', "n"));
        } else {
            // get template from filesystem
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
            if (!is_dir($template_path)) $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
            $template = $template_path . $letterObj->getVar('letter_template') . ".tpl";
            $htmlBody = $xoopsTpl->fetch($template);
        }
        $textBody = xnewsletter_html2text($htmlBody); // new from v1.3
        
        $letter_array['letter_content_templated'] = $htmlBody;
        $letter_array['letter_content_templated_html'] = $htmlBody;
        $letter_array['letter_content_templated_text'] = $textBody; // new from v1.3
        $letter_array['letter_created_formatted'] = formatTimestamp($letterObj->getVar('letter_created'), $xnewsletter->getConfig('dateformat'));
        $letter_array['letter_submitter_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
        $xoopsTpl->assign('letter', $letter_array);
        break;

    case 'print_letter':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter_print.tpl";
        include XOOPS_ROOT_PATH . "/header.php";

        //$xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // get letter_id
        $letter_id = xnewsletterRequest::getInt('letter_id', 0);
        // get letter object
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
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

        preg_match('/db:([0-9]*)/', $letterObj->getVar("letter_template"), $matches);
        if(isset($matches[1]) && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
            // get template from database
            $htmlBody = $xoopsTpl->fetchFromData($templateObj->getVar('template_content', "n"));
        } else {
            // get template from filesystem
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
            if (!is_dir($template_path)) $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
            $template = $template_path . $letterObj->getVar('letter_template') . ".tpl";
            $htmlBody = $xoopsTpl->fetch($template);
        }
        $textBody = xnewsletter_html2text($htmlBody); // new from v1.3

        $letter_array['letter_content_templated'] = $htmlBody;
        $letter_array['letter_content_templated_html'] = $htmlBody;
        $letter_array['letter_content_templated_text'] = $textBody; // new from v1.3
        $letter_array['letter_created_formatted'] = formatTimestamp($letterObj->getVar('letter_created'), $xnewsletter->getConfig('dateformat'));
        $letter_array['letter_submitter_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
        $xoopsTpl->assign('letter', $letter_array);
        break;

    case 'list_letters':
    default:
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter_list_letters.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get letters array
        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letterCount = $xnewsletter->getHandler('letter')->getCount();
        $start = xnewsletterRequest::getInt('start', 0);
        $limit = $xnewsletter->getConfig('adminperpage');
        $letterCriteria->setStart($start);
        $letterCriteria->setLimit($limit);
        $letterObjs = $xnewsletter->getHandler('letter')->getAll($letterCriteria, null, true, true);

        // pagenav
        $pagenav = new XoopsPageNav($letterCount, $limit, $start, 'start', "op={$op}");
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());

        // letters table
        $showAdminColumns = false;
        if ($letterCount> 0) {
            foreach ($letterObjs as $letter_id => $letterObj) {
                $userPermissions = xnewsletter_getUserPermissionsByLetter($letter_id);
                if (
                    ($userPermissions['read'] && $letterObj->getVar('letter_sent') > 0) ||
                    ($userPermissions['send'] == true)
                ) {
                    $letter_array = $letterObj->toArray();
                    $letter_array['letter_created_formatted'] = formatTimestamp($letterObj->getVar('letter_created'), $xnewsletter->getConfig('dateformat'));
                    $letter_array['letter_submitter_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
                    $letter_array['letter_sent_formatted'] = $letterObj->getVar('letter_sent') != 0 ? formatTimestamp($letterObj->getVar('letter_sent'), $xnewsletter->getConfig('dateformat')) : '';
                    $letter_array['letter_sender_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_sender'));
                    //
                    preg_match('/db:([0-9]*)/', $letter_array['letter_template'], $matches);
                    if (isset($matches[1]) && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
                        $letter_array['letter_template'] = "db:" . $templateObj->getVar('template_title');
                    } else {
                        $letter_array['letter_template'] = "file:" . $letter_array['letter_template'];
                    }
                    //
                    $letter_cat_ids = explode('|', $letterObj->getVar('letter_cats'));
                    // skip letter
                    if (($cat_id != 0) && !in_array($cat_id, $letter_cat_ids)) {
                        continue;
                    }
                    // get categories
                    $catsAvailableCount = 0;
                    unset($letter_array['letter_cats']); // IN PROGRESS
                    foreach ($letter_cat_ids as $letter_cat_id) {
                        $catObj = $xnewsletter->getHandler('cat')->get($letter_cat_id);
                        if ($gperm_handler->checkRight('newsletter_read_cat', $catObj->getVar('cat_id'), $groups, $xnewsletter->getModule()->mid())) {
                            ++$catsAvailableCount;
                            $letter_array['letter_cats'][] = $catObj->toArray();
                        }
                        unset($catObj);
                    }
                    if ($catsAvailableCount > 0) {
                        $letters_array[] = $letter_array;
                    }
                    // count letter attachements
                    $attachmentCriteria = new CriteriaCompo();
                    $attachmentCriteria->add(new Criteria('attachment_letter_id', $letterObj->getVar('letter_id')));
                    $letter_array['attachmentCount'] = $xnewsletter->getHandler('attachment')->getCount($attachmentCriteria);
                    // get protocols
                    if ($userPermissions['edit']) {
                        // take last item protocol_subscriber_id=0 from table protocol as actual status
                        $protocolCriteria = new CriteriaCompo();
                        $protocolCriteria->add(new Criteria('protocol_letter_id', $letterObj->getVar('letter_id')));
                        //$criteria->add(new Criteria('protocol_subscriber_id', '0'));
                        $protocolCriteria->setSort('protocol_id');
                        $protocolCriteria->setOrder('DESC');
                        $protocolCriteria->setLimit(1);
                        $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
                        $protocol_status = '';
                        $protocol_letter_id = 0;
                        foreach ($protocolObjs as $protocolObj) {
                            $letter_array['protocols'][] = array(
                                'protocol_status' => $protocolObj->getVar('protocol_status'),
                                'protocol_letter_id' => $protocolObj->getVar('protocol_letter_id')
                                );
                        }
                    }
                    // check if table show admin columns
                    if (($userPermissions['edit'] == true) ||
                        ($userPermissions['delete'] == true) ||
                        ($userPermissions['create'] == true) ||
                        ($userPermissions['send'] == true)
                    ) {
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
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter.tpl"; // IN PROGRESS
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_CREATE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

// IN PROGRESS FROM HERE
        $letterObj = $xnewsletter->getHandler('letter')->create();
        $form = $letterObj->getForm();
        $content = $form->render();
        $xoopsTpl->assign('content', $content);
        break;

    case 'copy_letter':
    case 'clone_letter':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_COPY, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $oldLetterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $newLetterObj = $xnewsletter->getHandler('letter')->create();
        $newLetterObj->setVar('letter_title', sprintf(_AM_XNEWSLETTER_LETTER_CLONED, $oldLetterObj->getVar('letter_title')));
        $newLetterObj->setVar('letter_content', $oldLetterObj->getVar('letter_content', 'n'));
        $newLetterObj->setVar('letter_template', $oldLetterObj->getVar('letter_template'));
        $newLetterObj->setVar('letter_cats', $oldLetterObj->getVar('letter_cats'));
        $newLetterObj->setVar('letter_account', $oldLetterObj->getVar('letter_account'));
        $newLetterObj->setVar('letter_email_test', $oldLetterObj->getVar('letter_email_test'));
        unset($oldLetterObj);
        $action = XOOPS_URL . "/modules/xnewsletter/{$currentFile}?op=copy_letter";
        $form = $newLetterObj->getForm($action);
        $content = $form->render();
        $xoopsTpl->assign('content', $content);
        break;

    case 'save_letter':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_empty.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

// IN PROGRESS FROM HERE

        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $letterObj->setVar('letter_title', xnewsletterRequest::getString('letter_title', ''));
        $letterObj->setVar('letter_content', $_REQUEST['letter_content']);
        $letterObj->setVar('letter_template', $_REQUEST['letter_template']);
        $letterObj->setVar('letter_cats', implode('|', xnewsletterRequest::getArray('letter_cats', array())));
        $letterObj->setVar('letter_account', $_REQUEST['letter_account']);
        $letterObj->setVar('letter_email_test', $_REQUEST['letter_email_test']);
        $letterObj->setVar('letter_submitter', xnewsletterRequest::getInt('letter_submitter', 0));
        $letterObj->setVar('letter_created', xnewsletterRequest::getInt('letter_created', 0));
        //
        if ($xnewsletter->getHandler('letter')->insert($letterObj)) {
            $letter_id = $letterObj->getVar("letter_id");
            // upload attachments
            $uploaded_files = array();
            include_once XOOPS_ROOT_PATH . "/class/uploader.php";
            $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . '/';
            if (!is_dir($uploaddir)) {
                $indexFile = XOOPS_UPLOAD_PATH . "/index.html";
                mkdir($uploaddir, 0777);
                chmod($uploaddir, 0777);
                copy($indexFile, $uploaddir . "index.html");
            }
            $uploader = new XoopsMediaUploader($uploaddir, $xnewsletter->getConfig('xn_mimetypes'), $xnewsletter->getConfig('xn_maxsize'), null, null);
            for ($upl = 0 ;$upl < 5; ++$upl) {
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$upl])) {
                    //$uploader->setPrefix("xn_") ; keep original name
                    $uploader->fetchMedia($_POST['xoops_upload_file'][$upl]);
                    if (!$uploader->upload()) {
                        $errors = $uploader->getErrors();
                        redirect_header("javascript:history.go(-1)", 3, $errors);
                    } else {
                        $uploaded_files[] = array('name' => $uploader->getSavedFileName(), 'origname' => $uploader->getMediaType());
                    }
                }
            }
            // create items in attachments
            foreach ($uploaded_files as $file) {
                $attachmentObj =& $xnewsletter->getHandler('attachment')->create();
                $attachmentObj->setVar('attachment_letter_id', $letter_id);
                $attachmentObj->setVar('attachment_name', $file['name']);
                $attachmentObj->setVar('attachment_type', $file['origname']);
                $attachmentObj->setVar('attachment_submitter', $xoopsUser->uid());
                $attachmentObj->setVar('attachment_created', time());
                $xnewsletter->getHandler('attachment')->insert($attachmentObj);
            }
            // create item in protocol
            $protocolObj = $xnewsletter->getHandler('protocol')->create();
            $protocolObj->setVar('protocol_letter_id', $letter_id);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_success', true);
            $action = xnewsletterRequest::getInt('letter_action', _XNEWSLETTER_LETTER_ACTION_VAL_NO);
            switch ($action) {
                case _XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW :
                    $url = "{$currentFile}?op=show_preview&letter_id={$letter_id}";
                    break;
                case _XNEWSLETTER_LETTER_ACTION_VAL_SEND :
                    $url = "sendletter.php?op=send_letter&letter_id={$letter_id}";
                    break;
                case _XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST :
                    $url = "sendletter.php?op=send_test&letter_id={$letter_id}";
                    break;
                default:
                    $url = "{$currentFile}?op=list_letters";
                    break;
            }
            $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_LETTER_ACTION_SAVED);
            $protocolObj->setVar('protocol_submitter', $xoopsUser->uid());
            $protocolObj->setVar('protocol_created', time());
            //
            if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
                // create protocol is ok
                redirect_header($url, 3, _AM_XNEWSLETTER_FORMOK);
            }
        } else {
            echo "Error create protocol: " . $protocolObj->getHtmlErrors();
        }
        break;

    case 'edit_letter':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_EDIT, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        $form = $letterObj->getForm();
        $content = $form->render();
        $xoopsTpl->assign('content', $content);
        break;

    case 'delete_letter':
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_empty.tpl";
        include_once XOOPS_ROOT_PATH . "/header.php";
        //
        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new xnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
        $breadcrumb->addLink(_MD_XNEWSLETTER_LETTER_DELETE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

// IN PROGRESS FROM HERE

        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
        if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('letter')->delete($letterObj)) {
                // delete protocol
                $sql = "DELETE FROM `{$xoopsDB->prefix("xnewsletter_protocol")}`";
                $sql.= " WHERE `protocol_letter_id`={$letter_id}";
                if(!$result = $xoopsDB->query($sql)) die("MySQL-Error: " . mysql_error());

                // delete attachments
                $attachmentCriteria = new CriteriaCompo();
                $attachmentCriteria->add(new Criteria('attachment_letter_id', $letter_id));
                $attachmentObjs = $xnewsletter->getHandler('attachment')->getAll($attachmentCriteria);
                foreach (array_keys($attachmentObjs) as $attachment_id) {
                    $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
                    $attachment_name = $attachmentObj->getVar('attachment_name');
                    $xnewsletter->getHandler('attachment')->delete($attachmentObj, true);
                    // delete file
                    $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . "/";
                    unlink($uploaddir . $attachment_name);
                }
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $letterObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => true, "letter_id" => $letter_id, "op" => "delete_letter"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $letterObj->getVar("letter_title")));
        }
        break;
}

include 'footer.php';
