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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */

use XoopsModules\Xnewsletter;

require_once __DIR__ . '/common.php';

/**
 * @param $cats
 *
 * @return string
 */
function xnewsletter_block_addCatSelect($cats)
{
    if (is_array($cats)) {
        $cat_sql = '(' . current($cats);
        array_shift($cats);
        foreach ($cats as $cat) {
            $cat_sql .= ',' . $cat;
        }
        $cat_sql .= ')';
    }

    return $cat_sql;
}

/**
 * @return bool
 */
function xnewsletter_checkModuleAdmin()
{
    if (file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))) {
        require_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');

        return true;
    }
    echo xoops_error("Error: You don't use the Frameworks \"admin module\". Please install this Frameworks");

    return false;
}

/**
 * Checks if a user is admin of xnewsletter
 *
 * @return bool
 */
function xnewsletter_userIsAdmin()
{
    global $xoopsUser;
    $helper = Xnewsletter\Helper::getInstance();

    static $xnewsletter_isAdmin;

    if (isset($xnewsletter_isAdmin)) {
        return $xnewsletter_isAdmin;
    }

    if (!$xoopsUser) {
        $xnewsletter_isAdmin = false;
    } else {
        $xnewsletter_isAdmin = $xoopsUser->isAdmin($helper->getModule()->mid());
    }

    return $xnewsletter_isAdmin;
}

/**
 * @param      $email
 * @param bool $antispam
 *
 * @return bool|mixed
 */
function xnewsletter_checkEmail($email, $antispam = false)
{
    require_once XOOPS_ROOT_PATH . '/include/functions.php';

    return checkEmail($email, $antispam);
}

/**
 * @param $html
 *
 * @throws Html2TextException
 * @return string
 */
function xnewsletter_html2text($html)
{
    require_once XNEWSLETTER_ROOT_PATH . '/include/html2text/html2text.php';

    return convert_html_to_text($html);
}

/**
 * @param        $global
 * @param        string $key
 * @param string $default
 * @param string $type
 * @param bool   $notset
 *
 * @return bool|int|mixed|string
 */
function xnewsletter_CleanVars(&$global, $key, $default = '', $type = 'int', $notset = false)
{
    require_once XOOPS_ROOT_PATH . '/include/functions.php';
    switch ($type) {
        case 'string':
                        if(defined('FILTER_SANITIZE_ADD_SLASHES')){
                $ret = isset($global[$key]) ? filter_var($global[$key], FILTER_SANITIZE_ADD_SLASHES) : $default;
            } else {
                $ret = isset($global[$key]) ? filter_var($global[$key], FILTER_SANITIZE_MAGIC_QUOTES) : $default;
            }
            if ($notset) {
                if ('' == trim($ret)) {
                    $ret = $default;
                }
            }
            break;
        case 'date':
            $ret = isset($global[$key]) ? strtotime($global[$key]) : $default;
            break;
        case 'email':
            $ret = isset($global[$key]) ? filter_var($global[$key], FILTER_SANITIZE_EMAIL) : $default;
            $ret = checkEmail($ret);
            break;
        case 'array':
            if (isset($global[$key])) {
                //ToDo!!
                $ret = $global[$key];
            }
            break;
        case 'int':
        default:
            $ret = isset($global[$key]) ? filter_var($global[$key], FILTER_SANITIZE_NUMBER_INT) : $default;
            break;
    }
    if (false === $ret) {
        return $default;
    }

    return $ret;
}

/**
 * @param string $str
 * @param array  $vars associative array
 *
 * @param string $char
 * @return string
 */
function xnewsletter_sprintf($str = '', $vars = [], $char = '')
{
    if (!$str) {
        return '';
    }
    if (count($vars) > 0) {
        foreach ($vars as $k => $v) {
            $str = str_replace($char . $k, $v, $str);
        }
    }

    return $str;
}

/**
 * @param $contentObj
 * @param $sets
 *
 * @return mixed
 */
function xnewsletter_setPost($contentObj, $sets)
{
    if (!is_object($contentObj)) {
        return false;
    }
    if (isset($sets)) {
        $contentObj->setVar('accounts_id', xnewsletter_CleanVars($sets, 'accounts_id', 0, 'int'));
        $contentObj->setVar('accounts_type', xnewsletter_CleanVars($sets, 'accounts_type', 1, 'int'));
        $contentObj->setVar('accounts_name', xnewsletter_CleanVars($sets, 'accounts_name', _AM_XNEWSLETTER_ACCOUNTS_TYPE_NAME, 'string', true));
        $contentObj->setVar('accounts_yourname', xnewsletter_CleanVars($sets, 'accounts_yourname', _AM_XNEWSLETTER_ACCOUNTS_YOURNAME, 'string', true));
        $contentObj->setVar('accounts_yourmail', xnewsletter_CleanVars($sets, 'accounts_yourmail', _AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL, 'email', true));
        $contentObj->setVar('accounts_username', xnewsletter_CleanVars($sets, 'accounts_username', _AM_XNEWSLETTER_ACCOUNTS_USERNAME, 'string', true));
        $contentObj->setVar('accounts_password', xnewsletter_CleanVars($sets, 'accounts_password', _AM_XNEWSLETTER_ACCOUNTS_PASSWORD, 'string', true));
        if (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP == $contentObj->getVar('accounts_type')) {
            if ($contentObj->isNew()) {
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN == @$set['accounts_server_in']) {
                    $sets['accounts_server_in'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN == @$set['accounts_port_in']) {
                    $sets['accounts_port_in'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT == @$set['accounts_server_out']) {
                    $sets['accounts_server_out'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT == @$set['accounts_port_out']) {
                    $sets['accounts_port_out'] = null;
                }
            }
            $contentObj->setVar('accounts_server_in', xnewsletter_CleanVars($sets, 'accounts_server_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN, 'string', true));
            $contentObj->setVar('accounts_port_in', xnewsletter_CleanVars($sets, 'accounts_port_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN, 'string', true));
            $contentObj->setVar('accounts_server_out', xnewsletter_CleanVars($sets, 'accounts_server_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT, 'string', true));
            $contentObj->setVar('accounts_port_out', xnewsletter_CleanVars($sets, 'accounts_port_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT, 'string', true));
            $contentObj->setVar('accounts_securetype_in', xnewsletter_CleanVars($sets, 'accounts_securetype_in', '', 'string'));
            $contentObj->setVar('accounts_securetype_out', xnewsletter_CleanVars($sets, 'accounts_securetype_out', '', 'string'));
        } elseif (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL == $contentObj->getVar('accounts_type')) {
            if ($contentObj->isNew()) {
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN == @$set['accounts_server_in']) {
                    $sets['accounts_server_in'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN == @$set['accounts_port_in']) {
                    $sets['accounts_port_in'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT == @$set['accounts_server_out']) {
                    $sets['accounts_server_out'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT == @$set['accounts_port_out']) {
                    $sets['accounts_port_out'] = null;
                }
            }
            $contentObj->setVar('accounts_server_in', xnewsletter_CleanVars($sets, 'accounts_server_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN, 'string', true));
            $contentObj->setVar('accounts_port_in', xnewsletter_CleanVars($sets, 'accounts_port_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN, 'string', true));
            $contentObj->setVar('accounts_server_out', xnewsletter_CleanVars($sets, 'accounts_server_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT, 'string', true));
            $contentObj->setVar('accounts_port_out', xnewsletter_CleanVars($sets, 'accounts_port_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT, 'string', true));
            $contentObj->setVar('accounts_securetype_in', xnewsletter_CleanVars($sets, 'accounts_securetype_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_IN, 'string'));
            $contentObj->setVar('accounts_securetype_out', xnewsletter_CleanVars($sets, 'accounts_securetype_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_OUT, 'string'));
        } else {
            if ($contentObj->isNew()) {
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN == @$set['accounts_server_in']) {
                    $sets['accounts_server_in'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN == @$set['accounts_port_in']) {
                    $sets['accounts_port_in'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT == @$set['accounts_server_out']) {
                    $sets['accounts_server_out'] = null;
                }
                if (_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT == @$set['accounts_port_out']) {
                    $sets['accounts_port_out'] = null;
                }
            }
            $contentObj->setVar('accounts_server_in', xnewsletter_CleanVars($sets, 'accounts_server_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN, 'string', true));
            $contentObj->setVar('accounts_port_in', xnewsletter_CleanVars($sets, 'accounts_port_in', _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN, 'string', true));
            $contentObj->setVar('accounts_server_out', xnewsletter_CleanVars($sets, 'accounts_server_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT, 'string', true));
            $contentObj->setVar('accounts_port_out', xnewsletter_CleanVars($sets, 'accounts_port_out', _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT, 'string', true));
            $contentObj->setVar('accounts_securetype_in', xnewsletter_CleanVars($sets, 'accounts_securetype_in', '', 'string'));
            $contentObj->setVar('accounts_securetype_out', xnewsletter_CleanVars($sets, 'accounts_securetype_out', '', 'string'));
        }
        $contentObj->setVar('accounts_use_bmh', xnewsletter_CleanVars($sets, 'accounts_use_bmh', 0, 'int'));
        $contentObj->setVar('accounts_inbox', xnewsletter_CleanVars($sets, 'accounts_inbox', _XNEWSLETTER_ACCOUNTS_TYPE_INBOX, 'string', true));
        $contentObj->setVar('accounts_hardbox', xnewsletter_CleanVars($sets, 'accounts_hardbox', _XNEWSLETTER_ACCOUNTS_TYPE_HARDBOX, 'string'));
        $contentObj->setVar('accounts_movehard', xnewsletter_CleanVars($sets, 'accounts_movehard', 0, 'int'));
        $contentObj->setVar('accounts_softbox', xnewsletter_CleanVars($sets, 'accounts_softbox', _XNEWSLETTER_ACCOUNTS_TYPE_SOFTBOX, 'string'));
        $contentObj->setVar('accounts_movesoft', xnewsletter_CleanVars($sets, 'accounts_movesoft', 0, 'int'));
        $contentObj->setVar('accounts_default', xnewsletter_CleanVars($sets, 'accounts_default', 0, 'int'));
        $contentObj->setVar('accounts_submitter', xnewsletter_CleanVars($sets, 'accounts_submitter', 0, 'int'));
        $contentObj->setVar('accounts_created', time());
    }

    return $contentObj;
}

/**
 * Check the rights of current user for this letter
 * returns the permission as array
 *
 * @param int $letter_id
 *
 * @return array
 */
function xnewsletter_getUserPermissionsByLetter($letter_id = 0)
{
    global $xoopsUser;
    $grouppermHandler = xoops_getHandler('groupperm');
    $memberHandler    = xoops_getHandler('member');
    $helper           = Xnewsletter\Helper::getInstance();

    $uid    = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : [0 => XOOPS_GROUP_ANONYMOUS];

    $permissions = [
        'read'   => false,
        'edit'   => false,
        'delete' => false,
        'create' => false,
        'send'   => false,
        'list'   => false,
    ];

    if ($uid > 0 && $xoopsUser->isAdmin()) {
        $permissions['read']   = true;
        $permissions['edit']   = true;
        $permissions['delete'] = true;
        $permissions['create'] = true;
        $permissions['send']   = true;
        $permissions['list']   = true;
    } else {
        $letterObj   = $helper->getHandler('Letter')->get($letter_id);
        $letter_cats = explode('|', $letterObj->getVar('letter_cats'));
        foreach ($letter_cats as $cat_id) {
            if ($grouppermHandler->checkRight('newsletter_admin_cat', $cat_id, $groups, $helper->getModule()->mid())) {
                $permissions['read']   = true;
                $permissions['edit']   = true;
                $permissions['delete'] = true;
                $permissions['create'] = true;
                $permissions['send']   = true;
                $permissions['list']   = true;
            } else {
                if ($grouppermHandler->checkRight('newsletter_create_cat', $cat_id, $groups, $helper->getModule()->mid())) {
                    $permissions['create'] = true;
                    $permissions['read']   = true; //creator should have perm to read all letters of this cat
                    if ($uid == $letterObj->getVar('letter_submitter')) {
                        $permissions['edit']   = true; //creator must have perm to edit own letters
                        $permissions['delete'] = true; //creator must have perm to edit own letters
                        $permissions['send']   = true; //creator must have perm to send/resend own letters
                    }
                }
                if ($grouppermHandler->checkRight('newsletter_read_cat', $cat_id, $groups, $helper->getModule()->mid())) {
                    $permissions['read'] = true;
                }
                if ($grouppermHandler->checkRight('newsletter_list_cat', $cat_id, $groups, $helper->getModule()->mid())) {
                    $permissions['list'] = true;
                }
            }
        }
    }

    return $permissions;
}

/**
 * Check the rights of current user
 * if a cat is defined, than only check for this cat, otherwise check whether there is minimum one cat with right create
 *
 * @param int $cat_id
 *
 * @return bool
 */
function xnewsletter_userAllowedCreateCat($cat_id = 0)
{
    global $xoopsUser;
    $grouppermHandler = xoops_getHandler('groupperm');
    $memberHandler    = xoops_getHandler('member');
    $helper           = Xnewsletter\Helper::getInstance();

    $allowedit = 0;
    $uid       = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
    if (0 == $uid) {
        return false;
    }

    $groups = $memberHandler->getGroupsByUser($uid);

    if ($cat_id > 0) {
        $catObj    = $helper->getHandler('Cat')->get($cat_id);
        $allowedit = $grouppermHandler->checkRight('newsletter_create_cat', $cat_id, $groups, $helper->getModule()->mid());
    } else {
        $catCriteria = new \CriteriaCompo();
        $catObjs     = $helper->getHandler('Cat')->getAll($catCriteria);
        foreach ($catObjs as $i => $catObj) {
            $cat_id    = $catObj->getVar('cat_id');
            $allowedit += $grouppermHandler->checkRight('newsletter_create_cat', $cat_id, $groups, $helper->getModule()->mid());
        }
    }

    return ($allowedit > 0);
}

/**
 * @param string $email
 *
 * @return bool
 */
function xnewsletter_pluginCheckEmail($email = '')
{
    global $xoopsDB;

    if ('' == $email) {
        return false;
    }
    $sql = "SELECT `subscr_id` FROM {$xoopsDB->prefix('xnewsletter_subscr')}";
    $sql .= " WHERE ((subscr_email)='{$email}')";
    if (!$subscriber = $xoopsDB->query($sql)) {
        die('MySQL-Error in xnewsletter_pluginCheckEmail: ' . $GLOBALS['xoopsDB']->error());
    }
    $row_result = mysqli_fetch_assoc($subscriber);
    $ret        = $row_result['subscr_id'] > 0 ? $row_result['subscr_id'] : false;
    unset($row_result);
    unset($subscriber);

    return $ret;
}

/**
 * @param boolean $subscr_id
 * @param $cat_id
 *
 * @return bool
 */
function xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id)
{
    global $xoopsDB;

    if (0 == $subscr_id || 0 == $cat_id) {
        return false;
    }
    $sql = 'SELECT `catsubscr_id`';
    $sql .= " FROM {$xoopsDB->prefix('xnewsletter_catsubscr')}";
    $sql .= " WHERE ((catsubscr_subscrid)={$subscr_id} AND (catsubscr_catid)={$cat_id})";
    if (!$subscriber = $xoopsDB->query($sql)) {
        die('MySQL-Error in xnewsletter_pluginCheckCatSubscr: ' . $GLOBALS['xoopsDB']->error());
    }
    $row_result = mysqli_fetch_assoc($subscriber);
    $ret        = $row_result['catsubscr_id'] > 0 ? $row_result['catsubscr_id'] : false;
    unset($row_result);
    unset($subscriber);

    return $ret;
}

/**
 * @param     $bytes
 * @param int $precision
 *
 * @return string
 */
function xnewsletter_bytesToSize1024($bytes, $precision = 2)
{
    // human readable format -- powers of 1024
    $unit = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB'];

    return @round($bytes / (pow(1024, $i = floor(log($bytes, 1024)))), $precision) . '' . $unit[(int)$i];
}

/**
 * Try to calculate email size (quite precise)
 *
 * @param int $letter_id
 *
 * @return int
 * @throws \Html2TextException
 */
function xnewsletter_emailSize($letter_id = 0)
{
//    require_once XNEWSLETTER_ROOT_PATH . '/class/class.xnewslettermailer.php';
    global $XoopsTpl;
    $helper = Xnewsletter\Helper::getInstance();

    if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $xoopsTpl = new \XoopsTpl();
    }
    // get template path
    $template_path = XNEWSLETTER_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
    if (!is_dir($template_path)) {
        $template_path = XNEWSLETTER_ROOT_PATH . '/language/english/templates/';
    }
    if (!is_dir($template_path)) {
        return str_replace('%p', $template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH);
    }

    $letterObj = $helper->getHandler('Letter')->get($letter_id);
    if (!is_array($letterObj) || 0 == count($letterObj)) {
        return false;
    }

    // read categories
    $letter_cats = $letterObj->getVar('letter_cats');
    if ('' == $letter_cats) {
        //no cats
        return false;
    }

    // read data of account
    $letter_account = $letterObj->getVar('letter_account');
    if ('' == $letter_account && 0 == $letter_account) {
        return false;
    }
    $accountObj             = $helper->getHandler('Accounts')->get($letter_account);
    $account_type           = $accountObj->getVar('accounts_type');
    $account_yourname       = $accountObj->getVar('accounts_yourname');
    $account_yourmail       = $accountObj->getVar('accounts_yourmail');
    $account_username       = $accountObj->getVar('accounts_username');
    $account_password       = $accountObj->getVar('accounts_password');
    $account_server_out     = $accountObj->getVar('accounts_server_out');
    $account_port_out       = $accountObj->getVar('accounts_port_out');
    $account_securetype_out = $accountObj->getVar('accounts_securetype_out');

    // create basic mail body
    $letter_title   = $letterObj->getVar('letter_title');
    $letter_content = $letterObj->getVar('letter_content', 'n');

    $letterTpl = new \XoopsTpl();
    // letter data
    $letterTpl->assign('content', $letter_content);
    $letterTpl->assign('title', $letter_title); // new from v1.3
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
        $letterTpl->append('attachments', $attachment_array);
    }
    // extra data
    $letterTpl->assign('date', time()); // new from v1.3
    $letterTpl->assign('xoops_url', XOOPS_URL); // new from v1.3
    $letterTpl->assign('xoops_langcode', _LANGCODE); // new from v1.3
    $letterTpl->assign('xoops_charset', _CHARSET); // new from v1.3
    // subscr data
    $letterTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW);
    $letterTpl->assign('salutation', _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW); // new from v1.3
    $letterTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW);
    $letterTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
    $letterTpl->assign('subscr_email', $letterObj->getVar('letter_email_test'));
    $letterTpl->assign('email', $letterObj->getVar('letter_email_test')); // new from v1.3
    $letterTpl->assign('unsubscribe_link', 'Test');
    $letterTpl->assign('unsubscribe_url', 'Test'); // new from v1.3

    preg_match('/db:([0-9]*)/', $letterObj->getVar('letter_template'), $matches);
    if (isset($matches[1]) && ($templateObj = $helper->getHandler('Template')->get((int)$matches[1]))) {
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
    //$textBody = mb_convert_encoding($textBody, 'ISO-8859-1', _CHARSET); // "text/plain; charset=us-ascii" [http://www.w3.org/Protocols/rfc1341/7_1_Text.html]

    // get letter attachments as attachment
    $attachmentAsattachmentCriteria = new \CriteriaCompo();
    $attachmentAsattachmentCriteria->add(new \Criteria('attachment_letter_id', $letter_id));
    $attachmentAsattachmentCriteria->add(new \Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT));
    $attachmentAsattachmentCriteria->setSort('attachment_id');
    $attachmentAsattachmentCriteria->setOrder('ASC');
    $attachmentObjs  = $helper->getHandler('Attachment')->getObjects($attachmentAsattachmentCriteria, true);
    $attachmentsPath = [];
    foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
        $attachmentsPath[] = XOOPS_UPLOAD_PATH . $helper->getConfig('xn_attachment_path') . $letter_id . '/' . $attachmentObj->getVar('attachment_name');
    }

    $mail           = new XnewsletterMailer();
    $mail->CharSet  = _CHARSET; //use xoops default character set
    $mail->Username = $account_username; // SMTP account username
    $mail->Password = $account_password; // SMTP account password
    if (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3 == $account_type) {
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = $account_server_out;
    }
    if (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP == $account_type || _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL == $account_type) {
        $mail->Port = $account_port_out; // set the SMTP port
        $mail->Host = $account_server_out; //sometimes necessary to repeat
    }
    if ('' != $account_securetype_out) {
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = $account_securetype_out; // sets the prefix to the server
    }
    $mail->setFrom($account_yourmail, $account_yourname);
    $mail->addReplyTo($account_yourmail, $account_yourname);
    $mail->Subject = html_entity_decode($letter_title, ENT_QUOTES);

    $mail->addAddress($letterObj->getVar('letter_email_test'), _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW . ' ' . _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
    $mail->msgHTML($htmlBody); // $mail->Body = $htmlBody;
    $mail->AltBody = $textBody;

    foreach ($attachmentsPath as $attachmentPath) {
        if (file_exists($attachmentPath)) {
            $mail->addAttachment($attachmentPath);
        }
    }

    return $mail->getSize();
    unset($mail);
}

/**
 * @param      $filePath
 * @param bool $isBinary
 * @param bool $retBytes
 *
 * @return bool|int|mixed
 */
function xnewsletter_download($filePath, $isBinary = true, $retBytes = true)
{
    // how many bytes per chunk
    //$chunkSize = 1 * (1024 * 1024);
    $chunkSize    = 8 * (1024 * 1024); //8MB (highest possible fread length)
    $buffer       = '';
    $bytesCounter = 0;

    if (true === $isBinary) {
        $handler = fopen($filePath, 'rb');
    } else {
        $handler = fopen($filePath, 'rb');
    }
    if (false === $handler) {
        return false;
    }
    while (!feof($handler)) {
        $buffer = fread($handler, $chunkSize);
        echo $buffer;
        ob_flush();
        flush();
        if ($retBytes) {
            $bytesCounter += mb_strlen($buffer);
        }
    }
    $status = fclose($handler);
    if ($retBytes && $status) {
        return $bytesCounter; // return num. bytes delivered like readfile() does.
    }

    return $status;
}

/**
 * @author     Jack Mason
 * @website    volunteer @ http://www.osipage.com, web access application and bookmarking tool.
 * @copyright  Free script, use anywhere as you like, no attribution required
 * @created    2014
 * The script is capable of downloading really large files in PHP. Files greater than 2GB may fail in 32-bit windows or similar system.
 * All incorrect headers have been removed and no nonsense code remains in this script. Should work well.
 * The best and most recommended way to download files with PHP is using xsendfile, learn
 * more here: https://tn123.org/mod_xsendfile/
 *
 * @param $filePath
 * @param $fileMimetype
 */
function xnewsletter_largeDownload($filePath, $fileMimetype)
{
    /* You may need these ini settings too */
    set_time_limit(0);
    ini_set('memory_limit', '512M');
    if (!empty($filePath)) {
        $fileInfo            = pathinfo($filePath);
        $fileName            = $fileInfo['basename'];
        $fileExtrension      = $fileInfo['extension'];
        $default_contentType = 'application/octet-stream';
        // to find and use specific content type, check out this IANA page : http://www.iana.org/assignments/media-types/media-types.xhtml
        if ($fileMimetype = !'') {
            $contentType = $fileMimetype;
        } else {
            $contentType = $default_contentType;
        }
        if (file_exists($filePath)) {
            $size   = filesize($filePath);
            $offset = 0;
            $length = $size;
            //HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
            if (\Xmf\Request::hasVar('HTTP_RANGE', 'SERVER')) {
                preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
                $offset  = (int)$matches[1];
                $length  = (int)$matches[2] - $offset;
                $fhandle = fopen($filePath, 'rb');
                fseek($fhandle, $offset); // seek to the requested offset, this is 0 if it's not a partial content request
                $data = fread($fhandle, $length);
                fclose($fhandle);
                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size);
            }//HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
            //USUAL HEADERS FOR DOWNLOAD
            header('Content-Disposition: attachment;filename=' . $fileName);
            header('Content-Type: ' . $contentType);
            header('Accept-Ranges: bytes');
            header('Pragma: public');
            header('Expires: -1');
            header('Cache-Control: no-cache');
            header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($filePath));
            $chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length)
            if ($size > $chunksize) {
                $handle = fopen($_FILES['file']['tmp_name'], 'rb');
                $buffer = '';
                while (!feof($handle) && (CONNECTION_NORMAL === connection_status())) {
                    $buffer = fread($handle, $chunksize);
                    print $buffer;
                    ob_flush();
                    flush();
                }
                if (CONNECTION_NORMAL !== connection_status()) {
                    //TODO traslation
                    echo 'Connection aborted';
                }
                fclose($handle);
            } else {
                ob_clean();
                flush();
                readfile($filePath);
            }
        } else {
            //TODO traslation
            echo 'File does not exist!';
        }
    } else {
        //TODO traslation
        echo 'There is no file to download!';
    }
}
