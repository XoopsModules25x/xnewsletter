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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__FILE__) . '/common.php';

/**
 * @param $cats
 *
 * @return string
 */
function xnewsletter_block_addCatSelect($cats) {
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
function xnewsletter_checkModuleAdmin() {
    if ( file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))) {
        include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');

        return true;
    } else {
        echo xoops_error("Error: You don't use the Frameworks \"admin module\". Please install this Frameworks");

        return false;
    }
}

/**
 * Checks if a user is admin of Wfdownloads
 *
 * @return boolean
 */
function xnewsletter_userIsAdmin()
{
    global $xoopsUser;
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    static $xnewsletter_isAdmin;

    if (isset($xnewsletter_isAdmin)) {
        return $xnewsletter_isAdmin;
    }

    if (!$xoopsUser) {
        $xnewsletter_isAdmin = false;
    } else {
        $xnewsletter_isAdmin = $xoopsUser->isAdmin($xnewsletter->getModule()->mid());
    }

    return $xnewsletter_isAdmin;
}

/**
 * @param      $email
 * @param bool $antispam
 *
 * @return bool|mixed
 */
function xnewsletter_checkEmail($email, $antispam = false) {
    include_once XOOPS_ROOT_PATH . '/include/functions.php';

    return checkEmail($email, $antispam);
}

/**
 * @param $html
 *
 * @return the
 * @throws Html2TextException
 */
function xnewsletter_html2text($html) {
    include_once XNEWSLETTER_ROOT_PATH . '/include/html2text/html2text.php';

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
function xnewsletter_CleanVars(&$global, $key, $default = '', $type = 'int', $notset=false) {
    include_once XOOPS_ROOT_PATH . '/include/functions.php';
    switch ($type) {
        case 'string':
            $ret = (isset($global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_MAGIC_QUOTES) : $default;
            if ($notset) {
                if (trim($ret) == '') $ret = $default;
            }
            break;
        case 'date':
            $ret = (isset($global[$key])) ? strtotime($global[$key]) : $default;
            break;
        case 'email':
            $ret = (isset( $global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_EMAIL) : $default;
            $ret = checkEmail($ret);
            break;
        case 'array':
            if (isset( $global[$key])) {
                //ToDo!!
                $ret = $global[$key];
            }
            break;
        case 'int':
        default:
            $ret = (isset( $global[$key])) ? filter_var($global[$key], FILTER_SANITIZE_NUMBER_INT) : $default;
            break;
    }
    if ($ret === false) {
        return $default;
    }

    return $ret;
}

/**
 * @param $content
 */
function xnewsletter_meta_keywords($content) {
    global $xoopsTpl, $xoTheme;
    $myts = MyTextSanitizer::getInstance();
    $content = $myts->undoHtmlSpecialChars($myts->displayTbox($content));
    if (isset($xoTheme) && is_object($xoTheme)) {
        $xoTheme->addMeta( 'meta', 'keywords', strip_tags($content));
    } else {
        // Compatibility for old Xoops versions
        $xoopsTpl->assign('xoops_meta_keywords', strip_tags($content));
    }
}

/**
 * @param $content
 */
function xnewsletter_meta_description($content) {
    global $xoopsTpl, $xoTheme;
    $myts =& MyTextSanitizer::getInstance();
    $content= $myts->undoHtmlSpecialChars($myts->displayTarea($content));
    if (isset($xoTheme) && is_object($xoTheme)) {
        $xoTheme->addMeta( 'meta', 'description', strip_tags($content));
    } else {
        // Compatibility for old Xoops versions
        $xoopsTpl->assign('xoops_meta_description', strip_tags($content));
    }
}

/**
 * @param $content
 * @param $sets
 *
 * @return mixed
 */
function xnewsletter_setPost($content, $sets) {
    if (!is_object($content)) return false;
    if (isset($sets)) {
        $content->setVar("accounts_id",             xnewsletter_CleanVars($sets, "accounts_id", 0, 'int'));
        $content->setVar("accounts_type",           xnewsletter_CleanVars($sets, "accounts_type", 1, 'int'));
        $content->setVar("accounts_name",           xnewsletter_CleanVars($sets, "accounts_name", _AM_XNEWSLETTER_ACCOUNTS_TYPE_NAME, 'string', true));
        $content->setVar("accounts_yourname",       xnewsletter_CleanVars($sets, "accounts_yourname", _AM_XNEWSLETTER_ACCOUNTS_YOURNAME, 'string', true));
        $content->setVar("accounts_yourmail",       xnewsletter_CleanVars($sets, "accounts_yourmail", _AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL, 'email', true));
        $content->setVar("accounts_username",       xnewsletter_CleanVars($sets, "accounts_username", _AM_XNEWSLETTER_ACCOUNTS_USERNAME, 'string', true));
        $content->setVar("accounts_password",       xnewsletter_CleanVars($sets, "accounts_password", _AM_XNEWSLETTER_ACCOUNTS_PASSWORD, 'string', true));

        if ($content->getVar("accounts_type") == _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP) {
            if ($content->isNew()) {
                if (@$set['accounts_server_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN) $sets['accounts_server_in'] = null;
                if (@$set['accounts_port_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN) $sets['accounts_port_in'] = null;
                if (@$set['accounts_server_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT) $sets['accounts_server_out'] = null;
                if (@$set['accounts_port_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT) $sets['accounts_port_out'] = null;
            }
            $content->setVar("accounts_server_in",      xnewsletter_CleanVars( $sets, "accounts_server_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN, 'string', true));
            $content->setVar("accounts_port_in",        xnewsletter_CleanVars( $sets, "accounts_port_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN, 'string', true));
            $content->setVar("accounts_server_out",     xnewsletter_CleanVars( $sets, "accounts_server_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT, 'string', true));
            $content->setVar("accounts_port_out",       xnewsletter_CleanVars( $sets, "accounts_port_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT, 'string', true));
            $content->setVar("accounts_securetype_in",  xnewsletter_CleanVars( $sets, "accounts_securetype_in", '', 'string'));
            $content->setVar("accounts_securetype_out", xnewsletter_CleanVars( $sets, "accounts_securetype_out", '', 'string'));

        } elseif ($content->getVar("accounts_type") == _AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL) {
            if ($content->isNew()) {
                if (@$set['accounts_server_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN) $sets['accounts_server_in'] = null;
                if (@$set['accounts_port_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN) $sets['accounts_port_in'] = null;
                if (@$set['accounts_server_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT) $sets['accounts_server_out'] = null;
                if (@$set['accounts_port_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT ) $sets['accounts_port_out'] = null;
            }
            $content->setVar("accounts_server_in",      xnewsletter_CleanVars( $sets, "accounts_server_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN, 'string', true));
            $content->setVar("accounts_port_in",        xnewsletter_CleanVars( $sets, "accounts_port_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN, 'string', true));
            $content->setVar("accounts_server_out",     xnewsletter_CleanVars( $sets, "accounts_server_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT, 'string', true));
            $content->setVar("accounts_port_out",       xnewsletter_CleanVars( $sets, "accounts_port_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT, 'string', true));
            $content->setVar("accounts_securetype_in",  xnewsletter_CleanVars( $sets, "accounts_securetype_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_IN, 'string'));
            $content->setVar("accounts_securetype_out", xnewsletter_CleanVars( $sets, "accounts_securetype_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_OUT, 'string'));
        } else {
            if ($content->isNew()) {
                if (@$set['accounts_server_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN) $sets['accounts_server_in'] = null;
                if (@$set['accounts_port_in'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN) $sets['accounts_port_in'] = null;
                if (@$set['accounts_server_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT) $sets['accounts_server_out'] = null;
                if (@$set['accounts_port_out'] == _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT) $sets['accounts_port_out'] = null;
            }
            $content->setVar("accounts_server_in",      xnewsletter_CleanVars( $sets, "accounts_server_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN, 'string', true));
            $content->setVar("accounts_port_in",        xnewsletter_CleanVars( $sets, "accounts_port_in", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN, 'string', true));
            $content->setVar("accounts_server_out",     xnewsletter_CleanVars( $sets, "accounts_server_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT, 'string', true));
            $content->setVar("accounts_port_out",       xnewsletter_CleanVars( $sets, "accounts_port_out", _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT, 'string', true));
            $content->setVar("accounts_securetype_in",  xnewsletter_CleanVars( $sets, "accounts_securetype_in", '', 'string'));
            $content->setVar("accounts_securetype_out", xnewsletter_CleanVars( $sets, "accounts_securetype_out", '', 'string'));
        }
        $content->setVar("accounts_use_bmh",        xnewsletter_CleanVars( $sets, "accounts_use_bmh", 0, 'int'));
        $content->setVar("accounts_inbox",          xnewsletter_CleanVars( $sets, "accounts_inbox", _AM_XNEWSLETTER_ACCOUNTS_TYPE_INBOX, 'string', true));
        $content->setVar("accounts_hardbox",        xnewsletter_CleanVars( $sets, "accounts_hardbox", _AM_XNEWSLETTER_ACCOUNTS_TYPE_HARDBOX, 'string'));
        $content->setVar("accounts_movehard",       xnewsletter_CleanVars( $sets, "accounts_movehard", 0, 'int'));
        $content->setVar("accounts_softbox",        xnewsletter_CleanVars( $sets, "accounts_softbox", _AM_XNEWSLETTER_ACCOUNTS_TYPE_SOFTBOX, 'string'));
        $content->setVar("accounts_movesoft",       xnewsletter_CleanVars( $sets, "accounts_movesoft", 0, 'int'));
        $content->setVar("accounts_default",        xnewsletter_CleanVars( $sets, "accounts_default", 0, 'int'));
        $content->setVar("accounts_submitter",      xnewsletter_CleanVars( $sets, "accounts_submitter", 0, 'int'));
        $content->setVar("accounts_created",        time());
    }

    return $content;
}

/**
 * Convert StringToTime Date
 *
 * @param mixed $date
 *
 * @return int|mixed
 */
function xnewsletter_convertDate($date) {
    $GLOBALS['xoopsLogger']->addDeprecated(__FUNCTION__ . ' is deprecated');

    return ($date);

    if (strpos(_SHORTDATESTRING, "/")) {
       $date=str_replace("/", "-", $date);
    }

    return strtotime($date);
}

/**
 * @param int $letter_id
 *
 * @return array
 */
function xnewsletter_getUserPermissionsByLetter($letter_id = 0) {
    //check the rights of current user for this letter
    // returns the permission as array
    global $xoopsUser;
    $gperm_handler = xoops_gethandler('groupperm');
    $member_handler = xoops_gethandler('member');
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    $perm = array(
        "read" => false,
        "edit" => false,
        "delete" => false,
        "create" => false,
        "send" => false
        );
    $letter_cats = array();
    $currentuid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;

    // perm read
    if ($currentuid > 0 && $xoopsUser->isAdmin()) {
        $perm["read"] = true;
        $perm["edit"] = true;
        $perm["delete"] = true;
        $perm["create"] = true;
        $perm["send"] = true;
    } else {
        $obj_letter = $xnewsletter->getHandler('xnewsletter_letter')->get($letter_id);
        $letter_cats = explode("|", $obj_letter->getVar("letter_cats"));
        $submitter = $obj_letter->getVar("letter_submitter");
        $my_group_ids = $member_handler->getGroupsByUser( $currentuid ) ;

        foreach ($letter_cats as $cat_id) {
            if ($gperm_handler->checkRight('newsletter_admin_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid())) {
                $perm["create"] = true;
                $perm["read"] = true;
                $perm["edit"] = true;
                $perm["delete"] = true;
                $perm["send"] = true;
                $perm["list"] = true;
            } else {
                if ($gperm_handler->checkRight('newsletter_create_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid())) {
                    $perm["create"] = true;
                    $perm["read"] = true; //creator should have perm to read all letters of this cat
                    if ($currentuid == $submitter) {
                        $perm["edit"] = true; //creator must have perm to edit own letters
                        $perm["delete"] = true; //creator must have perm to edit own letters
                        $perm["send"] = true; //creator must have perm to send/resend own letters
                    }
                }
                if ($gperm_handler->checkRight( 'newsletter_read_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid()))
                    $perm["read"] = true;
                if ($gperm_handler->checkRight( 'newsletter_list_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid()))
                    $perm["list"] = true;
            }
        }
    }

    return $perm;
}

/**
 * @param int $cat_id
 *
 * @return bool
 */
function xnewsletter_userAllowedCreateCat($cat_id = 0) {
    //check the rights of current user
    //if a cat is defined, than only check for this cat, otherwise check whether there is minimum one cat with right create

    global $xoopsUser;
    $gperm_handler =& xoops_gethandler('groupperm');
    $member_handler =& xoops_gethandler('member');
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    $allowedit = 0;
    $currentuid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
    if ($currentuid == 0) return false;

    $my_group_ids = $member_handler->getGroupsByUser($currentuid);

    if ($cat_id > 0) {
        $cat_arr = $xnewsletter->getHandler('xnewsletter_cat')->get($cat_id);
        $allowedit = $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid());
    } else {
        $crit_cat = new CriteriaCompo();
        $cat_arr = $xnewsletter->getHandler('xnewsletter_cat')->getall($crit_cat);
        foreach (array_keys($cat_arr) as $i) {
            $cat_id = $cat_arr[$i]->getVar('cat_id');
            $allowedit += $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid());
        }
    }

    return ($allowedit > 0);
}

/**
 * @param string $email
 *
 * @return bool
 */
function xnewsletter_pluginCheckEmail($email = '') {
    global $xoopsDB;

    if ($email == '') {
        return false;
    }
    $sql = "SELECT `subscr_id` FROM {$xoopsDB->prefix("xnewsletter_subscr")}";
    $sql .= " WHERE ((subscr_email)='{$email}')";
    $subscriber = mysql_query($sql) || die ("MySQL-Error in xnewsletter_pluginCheckEmail: " . mysql_error());
    $row_result = mysql_fetch_assoc($subscriber);
    $ret = $row_result['subscr_id'] > 0 ? $row_result['subscr_id'] : false;
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
function xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id) {
    global $xoopsDB;

    if ($subscr_id == 0 || $cat_id == 0) return false;
    $sql = "SELECT `catsubscr_id`";
    $sql .= " FROM {$xoopsDB->prefix("xnewsletter_catsubscr")}";
    $sql .= " WHERE ((catsubscr_subscrid)={$subscr_id} AND (catsubscr_catid)={$cat_id})";
    $subscriber = mysql_query($sql) || die ("MySQL-Error in xnewsletter_pluginCheckCatSubscr: " . mysql_error());
    $row_result = mysql_fetch_assoc($subscriber);
    $ret = $row_result['catsubscr_id'] > 0 ? $row_result['catsubscr_id'] : false;
    unset($row_result);
    unset($subscriber);

    return $ret;
}
