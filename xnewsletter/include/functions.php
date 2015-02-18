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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */

include_once __DIR__ . '/common.php';

/*
 * @param int       $amount is… how much of $what you want.
 * @param string    $what is either paras, words, bytes or lists.
 * @param int       $start is whether or not to start the result with ‘Lorem ipsum dolor sit amet…‘
 *
 * @return string
 */
function xnewsletter_randomLipsum($amount = 1, $what = 'paras', $start = 0) {
    //$ret = file_get_contents('http://loripsum.net/api')
    $ret = simplexml_load_file("http://www.lipsum.com/feed/xml?amount=$amount&what=$what&start=$start")->lipsum;
    return $ret;
}

/**
 * @param $cats
 *
 * @return string
 */
function xnewsletter_block_addCatSelect($cats)
{
    if (is_array($cats)) {
        $cat_sql = "(" . current($cats);
        array_shift($cats);
        foreach ($cats as $cat) {
            $cat_sql .= "," . $cat;
        }
        $cat_sql .= ")";
    }

    return $cat_sql;
}

/**
 * @return bool
 */
function xnewsletter_checkModuleAdmin()
{
    if (file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))) {
        include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');

        return true;
    } else {
        echo xoops_error("Error: You don't use the Frameworks \"admin module\". Please install this Frameworks");

        return false;
    }
}

/**
 * Checks if a user is admin of xnewsletter
 *
 * @return boolean
 */
function xnewsletter_userIsAdmin()
{
    $xnewsletter = XnewsletterXnewsletter::getInstance();
    static $xnewsletter_isAdmin;
    //
    if (isset($xnewsletter_isAdmin)) {
        return $xnewsletter_isAdmin;
    }
    if (!$GLOBALS['xoopsUser']) {
        $xnewsletter_isAdmin = false;
    } else {
        $xnewsletter_isAdmin = $GLOBALS['xoopsUser']->isAdmin($xnewsletter->getModule()->mid());
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
    include_once XOOPS_ROOT_PATH . '/include/functions.php';
    //
    return checkEmail($email, $antispam);
}

/**
 * @param $html
 *
 * @return the
 * @throws Html2TextException
 */
function xnewsletter_html2text($html)
{
    include_once XNEWSLETTER_ROOT_PATH . '/include/html2text/html2text.php';
    //
    return convert_html_to_text($html);
}

/**
 * @param        $global
 * @param        $key
 * @param string $default
 * @param string $type
 * @param bool   $notset
 *
 * @return bool|int|mixed|string
 */
function xnewsletter_CleanVars(&$global, $key, $default = '', $type = 'int', $notset = false)
{
    include_once XOOPS_ROOT_PATH . '/include/functions.php';
    //
    switch ($type) {
        case 'string':
            $ret = (isset($global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_MAGIC_QUOTES) : $default;
            if ($notset) {
                if (trim($ret) == '') {
                    $ret = $default;
                }
            }
            break;
        case 'date':
            $ret = (isset($global[$key])) ? strtotime($global[$key]) : $default;
            break;
        case 'email':
            $ret = (isset($global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_EMAIL) : $default;
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
            $ret = (isset($global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_NUMBER_INT) : $default;
            break;
    }
    if ($ret === false) {
        return $default;
    }

    return $ret;
}

/**
 * @param string $str
 * @param array  $vars associative array
 *
 * @return string
 */
function xnewsletter_sprintf($str = '', $vars = array(), $char = '')
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
        $contentObj->setVar("accounts_id", xnewsletter_CleanVars($sets, "accounts_id", 0, 'int'));
        $contentObj->setVar("accounts_type", xnewsletter_CleanVars($sets, "accounts_type", 1, 'int'));
        $contentObj->setVar("accounts_name", xnewsletter_CleanVars($sets, "accounts_name", _AM_XNEWSLETTER_ACCOUNTS_TYPE_NAME, 'string', true));
        $contentObj->setVar("accounts_yourname", xnewsletter_CleanVars($sets, "accounts_yourname", _AM_XNEWSLETTER_ACCOUNTS_YOURNAME, 'string', true));
        $contentObj->setVar("accounts_yourmail", xnewsletter_CleanVars($sets, "accounts_yourmail", _AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL, 'email', true));
        $contentObj->setVar("accounts_username", xnewsletter_CleanVars($sets, "accounts_username", _AM_XNEWSLETTER_ACCOUNTS_USERNAME, 'string', true));
        $contentObj->setVar("accounts_password", xnewsletter_CleanVars($sets, "accounts_password", _AM_XNEWSLETTER_ACCOUNTS_PASSWORD, 'string', true));
        if ($contentObj->getVar("accounts_type") == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP) {
            if ($contentObj->isNew()) {
                if (@$set['accounts_server_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN) {
                    $sets['accounts_server_in'] = null;
                }
                if (@$set['accounts_port_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN) {
                    $sets['accounts_port_in'] = null;
                }
                if (@$set['accounts_server_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT) {
                    $sets['accounts_server_out'] = null;
                }
                if (@$set['accounts_port_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT) {
                    $sets['accounts_port_out'] = null;
                }
            }
            $contentObj->setVar("accounts_server_in", xnewsletter_CleanVars($sets, "accounts_server_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN, 'string', true));
            $contentObj->setVar("accounts_port_in", xnewsletter_CleanVars($sets, "accounts_port_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN, 'string', true));
            $contentObj->setVar("accounts_server_out", xnewsletter_CleanVars($sets, "accounts_server_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT, 'string', true));
            $contentObj->setVar("accounts_port_out", xnewsletter_CleanVars($sets, "accounts_port_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT, 'string', true));
            $contentObj->setVar("accounts_securetype_in", xnewsletter_CleanVars($sets, "accounts_securetype_in", '', 'string'));
            $contentObj->setVar("accounts_securetype_out", xnewsletter_CleanVars($sets, "accounts_securetype_out", '', 'string'));
        } elseif ($contentObj->getVar("accounts_type") == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL) {
            if ($contentObj->isNew()) {
                if (@$set['accounts_server_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN) {
                    $sets['accounts_server_in'] = null;
                }
                if (@$set['accounts_port_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN) {
                    $sets['accounts_port_in'] = null;
                }
                if (@$set['accounts_server_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT) {
                    $sets['accounts_server_out'] = null;
                }
                if (@$set['accounts_port_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT) {
                    $sets['accounts_port_out'] = null;
                }
            }
            $contentObj->setVar("accounts_server_in", xnewsletter_CleanVars($sets, "accounts_server_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN, 'string', true));
            $contentObj->setVar("accounts_port_in", xnewsletter_CleanVars($sets, "accounts_port_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN, 'string', true));
            $contentObj->setVar("accounts_server_out", xnewsletter_CleanVars($sets, "accounts_server_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT, 'string', true));
            $contentObj->setVar("accounts_port_out", xnewsletter_CleanVars($sets, "accounts_port_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT, 'string', true));
            $contentObj->setVar("accounts_securetype_in", xnewsletter_CleanVars($sets, "accounts_securetype_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_IN, 'string'));
            $contentObj->setVar("accounts_securetype_out", xnewsletter_CleanVars($sets, "accounts_securetype_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_OUT, 'string'));
        } else {
            if ($contentObj->isNew()) {
                if (@$set['accounts_server_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN) {
                    $sets['accounts_server_in'] = null;
                }
                if (@$set['accounts_port_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN) {
                    $sets['accounts_port_in'] = null;
                }
                if (@$set['accounts_server_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT) {
                    $sets['accounts_server_out'] = null;
                }
                if (@$set['accounts_port_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT) {
                    $sets['accounts_port_out'] = null;
                }
            }
            $contentObj->setVar("accounts_server_in", xnewsletter_CleanVars($sets, "accounts_server_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN, 'string', true));
            $contentObj->setVar("accounts_port_in", xnewsletter_CleanVars($sets, "accounts_port_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN, 'string', true));
            $contentObj->setVar("accounts_server_out", xnewsletter_CleanVars($sets, "accounts_server_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT, 'string', true));
            $contentObj->setVar("accounts_port_out", xnewsletter_CleanVars($sets, "accounts_port_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT, 'string', true));
            $contentObj->setVar("accounts_securetype_in", xnewsletter_CleanVars($sets, "accounts_securetype_in", '', 'string'));
            $contentObj->setVar("accounts_securetype_out", xnewsletter_CleanVars($sets, "accounts_securetype_out", '', 'string'));
        }
        $contentObj->setVar("accounts_use_bmh", xnewsletter_CleanVars($sets, "accounts_use_bmh", 0, 'int'));
        $contentObj->setVar("accounts_inbox", xnewsletter_CleanVars($sets, "accounts_inbox", _XNEWSLETTER_ACCOUNTS_TYPE_INBOX, 'string', true));
        $contentObj->setVar("accounts_hardbox", xnewsletter_CleanVars($sets, "accounts_hardbox", _XNEWSLETTER_ACCOUNTS_TYPE_HARDBOX, 'string'));
        $contentObj->setVar("accounts_movehard", xnewsletter_CleanVars($sets, "accounts_movehard", 0, 'int'));
        $contentObj->setVar("accounts_softbox", xnewsletter_CleanVars($sets, "accounts_softbox", _XNEWSLETTER_ACCOUNTS_TYPE_SOFTBOX, 'string'));
        $contentObj->setVar("accounts_movesoft", xnewsletter_CleanVars($sets, "accounts_movesoft", 0, 'int'));
        $contentObj->setVar("accounts_default", xnewsletter_CleanVars($sets, "accounts_default", 0, 'int'));
        $contentObj->setVar("accounts_submitter", xnewsletter_CleanVars($sets, "accounts_submitter", 0, 'int'));
        $contentObj->setVar("accounts_created", time());
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
    $gperm_handler  = xoops_gethandler('groupperm');
    $member_handler = xoops_gethandler('member');
    $xnewsletter    = XnewsletterXnewsletter::getInstance();

    $uid    = (is_object($GLOBALS['xoopsUser']) && isset($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->uid() : 0;
    $groups = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);

    $permissions = array(
        'read'   => false,
        'edit'   => false,
        'delete' => false,
        'create' => false,
        'send'   => false,
        'list'   => false
    );

    if ($uid > 0 && $GLOBALS['xoopsUser']->isAdmin()) {
        $permissions['read']   = true;
        $permissions['edit']   = true;
        $permissions['delete'] = true;
        $permissions['create'] = true;
        $permissions['send']   = true;
        $permissions['list']   = true;
    } else {
        $letterObj   = $xnewsletter->getHandler('letter')->get($letter_id);
        $letter_cats = explode('|', $letterObj->getVar('letter_cats'));
        foreach ($letter_cats as $cat_id) {
            if ($gperm_handler->checkRight('newsletter_admin_cat', $cat_id, $groups, $xnewsletter->getModule()->mid())) {
                $permissions['read']   = true;
                $permissions['edit']   = true;
                $permissions['delete'] = true;
                $permissions['create'] = true;
                $permissions['send']   = true;
                $permissions['list']   = true;
            } else {
                if ($gperm_handler->checkRight('newsletter_create_cat', $cat_id, $groups, $xnewsletter->getModule()->mid())) {
                    $permissions['create'] = true;
                    $permissions['read']   = true; //creator should have perm to read all letters of this cat
                    if ($uid == $letterObj->getVar('letter_submitter')) {
                        $permissions['edit']   = true; //creator must have perm to edit own letters
                        $permissions['delete'] = true; //creator must have perm to edit own letters
                        $permissions['send']   = true; //creator must have perm to send/resend own letters
                    }
                }
                if ($gperm_handler->checkRight('newsletter_read_cat', $cat_id, $groups, $xnewsletter->getModule()->mid())) {
                    $permissions['read'] = true;
                }
                if ($gperm_handler->checkRight('newsletter_list_cat', $cat_id, $groups, $xnewsletter->getModule()->mid())) {
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
    $gperm_handler  = xoops_gethandler('groupperm');
    $member_handler = xoops_gethandler('member');
    $xnewsletter    = XnewsletterXnewsletter::getInstance();

    $allowedit = 0;
    $uid       = (is_object($GLOBALS['xoopsUser']) && isset($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->uid() : 0;
    if ($uid == 0) {
        return false;
    }

    $groups = $member_handler->getGroupsByUser($uid);

    if ($cat_id > 0) {
        $catObj    = $xnewsletter->getHandler('cat')->get($cat_id);
        $allowedit = $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $groups, $xnewsletter->getModule()->mid());
    } else {
        $catCriteria = new CriteriaCompo();
        $catObjs     = $xnewsletter->getHandler('cat')->getAll($catCriteria);
        foreach ($catObjs as $i => $catObj) {
            $cat_id = $catObj->getVar('cat_id');
            $allowedit += $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $groups, $xnewsletter->getModule()->mid());
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
    if ($email == '') {
        return false;
    }
    $sql = "SELECT `subscr_id` FROM {$GLOBALS['xoopsDB']->prefix("xnewsletter_subscr")}";
    $sql .= " WHERE ((subscr_email)='{$email}')";
    if (!$subscriber = mysql_query($sql)) {
        die ("MySQL-Error in xnewsletter_pluginCheckEmail: " . mysql_error());
    }
    $row_result = mysql_fetch_assoc($subscriber);
    $ret        = $row_result['subscr_id'] > 0 ? $row_result['subscr_id'] : false;
    unset($row_result);
    unset($subscriber);

    return $ret;
}

/**
 * @param $subscr_id
 * @param $cat_id
 *
 * @return bool
 */
function xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id)
{
    if ($subscr_id == 0 || $cat_id == 0) {
        return false;
    }
    $sql = "SELECT `catsubscr_id`";
    $sql .= " FROM {$GLOBALS['xoopsDB']->prefix("xnewsletter_catsubscr")}";
    $sql .= " WHERE ((catsubscr_subscrid)={$subscr_id} AND (catsubscr_catid)={$cat_id})";
    if (!$subscriber = mysql_query($sql)) {
        die ("MySQL-Error in xnewsletter_pluginCheckCatSubscr: " . mysql_error());
    }
    $row_result = mysql_fetch_assoc($subscriber);
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
    $unit = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB');

    return @round(
            $bytes / pow(1024, ($i = floor(log($bytes, 1024)))),
            $precision
        ) . '' . $unit[(int)$i];
}

/**
 * Try to calculate email size (quite precise)
 *
 * @param int $letter_id
 *
 * @return int
 */
function xnewsletter_emailSize($letter_id = 0)
{
    $xnewsletter = XnewsletterXnewsletter::getInstance();
    include_once XNEWSLETTER_ROOT_PATH . '/class/class.xnewslettermailer.php';
    require_once $GLOBALS['xoops']->path('/class/template.php');
    //
    // get template path
    $template_path = XNEWSLETTER_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
    if (!is_dir($template_path)) {
        $template_path = XNEWSLETTER_ROOT_PATH . '/language/english/templates/';
    }
    if (!is_dir($template_path)) {
        return str_replace("%p", $template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH);
    }
    // get letter
    $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
    if (count($letterObj) == 0) {
        return false;
    }
    // read categories
    $letter_cats = $letterObj->getVar('letter_cats');
    if ($letter_cats == '') {
        //no cats
        return false;
    }
    // read data of account
    $letter_account = $letterObj->getVar('letter_account');
    if ($letter_account == '' && $letter_account == 0) {
        return false;
    }
    $accountObj             = $xnewsletter->getHandler('accounts')->get($letter_account);
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

    $letterTpl = new XoopsTpl();
    // letter data
    $letterTpl->assign('letter_id', $letter_id); // new from v1.3
    $letterTpl->assign('content', $letter_content);
    $letterTpl->assign('title', $letter_title); // new from v1.3
    // letter attachments as link
    $attachmentAslinkCriteria = new CriteriaCompo();
    $attachmentAslinkCriteria->add(new Criteria('attachment_letter_id', $letter_id));
    $attachmentAslinkCriteria->add(new Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASLINK));
    $attachmentAslinkCriteria->setSort('attachment_id');
    $attachmentAslinkCriteria->setOrder('ASC');
    $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentAslinkCriteria, true);
    $letterTpl->assign('attachments', array());
    foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
        $attachment = $attachmentObj->toArray();
        $attachment['attachment_url']  = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
        $attachment['attachment_link'] = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
        $letterTpl->append('attachments', $attachment);
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
    $textBody = mb_convert_encoding($textBody, 'ISO-8859-1', _CHARSET); // "text/plain; charset=us-ascii" [http://www.w3.org/Protocols/rfc1341/7_1_Text.html]

    // get letter attachments as attachment
    $attachmentAsAttachmentCriteria = new CriteriaCompo();
    $attachmentAsAttachmentCriteria->add(new Criteria('attachment_letter_id', $letter_id));
    $attachmentAsAttachmentCriteria->add(new Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT));
    $attachmentAsAttachmentCriteria->setSort('attachment_id');
    $attachmentAsAttachmentCriteria->setOrder('ASC');
    $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentAsAttachmentCriteria, true);
    $attachmentsPath = array();
    foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
        $attachmentsPath[] = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . '/' . $attachmentObj->getVar('attachment_name');
    }

    $mail           = new XnewsletterMailer();
    $mail->CharSet  = _CHARSET; //use xoops default character set
    $mail->Username = $account_username; // SMTP account username
    $mail->Password = $account_password; // SMTP account password
    if ($account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3) {
        $mail->IsSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = $account_server_out;
    }
    if ($account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP || $account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL) {
        $mail->Port = $account_port_out; // set the SMTP port
        $mail->Host = $account_server_out; //sometimes necessary to repeat
    }
    if ($account_securetype_out != '') {
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = $account_securetype_out; // sets the prefix to the server
    }
    $mail->SetFrom($account_yourmail, $account_yourname);
    $mail->AddReplyTo($account_yourmail, $account_yourname);
    $mail->Subject = html_entity_decode($letter_title, ENT_QUOTES);

    $mail->AddAddress($letterObj->getVar('letter_email_test'), _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW . " " . _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW);
    $mail->Body = $htmlBody;
    $mail->AltBody = $textBody;

    foreach ($attachmentsPath as $attachmentPath) {
        if (file_exists($attachmentPath)) {
            $mail->AddAttachment($attachmentPath);
        }
    }

    return $mail->GetSize();
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

    if ($isBinary == true) {
        $handler = fopen($filePath, 'rb');
    } else {
        $handler = fopen($filePath, 'r');
    }
    if ($handler === false) {
        return false;
    }
    while (!feof($handler)) {
        $buffer = fread($handler, $chunkSize);
        echo $buffer;
        ob_flush();
        flush();
        if ($retBytes) {
            $bytesCounter += strlen($buffer);
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
        $default_contentType = "application/octet-stream";
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
            if (isset($_SERVER['HTTP_RANGE'])) {
                preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
                $offset  = intval($matches[1]);
                $length  = intval($matches[2]) - $offset;
                $fhandle = fopen($filePath, 'r');
                fseek($fhandle, $offset); // seek to the requested offset, this is 0 if it's not a partial content request
                $data = fread($fhandle, $length);
                fclose($fhandle);
                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size);
            }//HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
            //USUAL HEADERS FOR DOWNLOAD
            header("Content-Disposition: attachment;filename=" . $fileName);
            header('Content-Type: ' . $contentType);
            header("Accept-Ranges: bytes");
            header("Pragma: public");
            header("Expires: -1");
            header("Cache-Control: no-cache");
            header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
            header("Content-Length: " . filesize($filePath));
            $chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length)
            if ($size > $chunksize) {
                $handle = fopen($_FILES['file']['tmp_name'], 'rb');
                $buffer = '';
                while (!feof($handle) && (connection_status() === CONNECTION_NORMAL)) {
                    $buffer = fread($handle, $chunksize);
                    print $buffer;
                    ob_flush();
                    flush();
                }
                if (connection_status() !== CONNECTION_NORMAL) {
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
