<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xnewsletter module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xnewsletter
 * @since           1.3
 * @author          Xoops Development Team
 * @version         svn:$id$
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");

// This must contain the name of the folder in which reside xnewsletter
define("XNEWSLETTER_DIRNAME", basename(dirname(__DIR__)));
define("XNEWSLETTER_URL", XOOPS_URL . '/modules/' . XNEWSLETTER_DIRNAME);
define("XNEWSLETTER_ROOT_PATH", XOOPS_ROOT_PATH . '/modules/' . XNEWSLETTER_DIRNAME);
define("XNEWSLETTER_IMAGES_URL", XNEWSLETTER_URL . '/assets/images');
define("XNEWSLETTER_ADMIN_URL", XNEWSLETTER_URL . '/admin');
define("XNEWSLETTER_ICONS_URL", XNEWSLETTER_URL . '/assets/images/icons');

xoops_loadLanguage('common', XNEWSLETTER_DIRNAME);

include_once XNEWSLETTER_ROOT_PATH . '/class/xnewsletter.php'; // XnewsletterXnewsletter class
include_once XNEWSLETTER_ROOT_PATH . '/include/config.php'; // IN PROGRESS
include_once XNEWSLETTER_ROOT_PATH . '/include/functions.php';
include_once XNEWSLETTER_ROOT_PATH . '/include/constants.php';
include_once XNEWSLETTER_ROOT_PATH . '/class/common/session.php'; // XnewsletterSession class
include_once XNEWSLETTER_ROOT_PATH . '/class/common/breadcrumb.php'; // XnewsletterBreadcrumb class

xoops_load('XoopsUserUtility');
xoops_load('XoopsRequest');
// MyTextSanitizer object
$myts = MyTextSanitizer::getInstance();

$debug       = false;
$xnewsletter = XnewsletterXnewsletter::getInstance($debug);

//This is needed or it will not work in blocks.
global $xnewsletter_isAdmin;

// Load only if module is installed
if (is_object($xnewsletter->getModule())) {
    // Find if the user is admin of the module
    $xnewsletter_isAdmin = xnewsletter_userIsAdmin();
}
$xoopsModule = $xnewsletter->getModule();

// Load Xoops handlers
$module_handler       = xoops_gethandler('module');
$member_handler       = xoops_gethandler('member');
$notification_handler = xoops_gethandler('notification');
$gperm_handler        = xoops_gethandler('groupperm');
$config_handler       = xoops_gethandler('config');
