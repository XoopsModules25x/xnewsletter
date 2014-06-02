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
 * xNewsletter module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xNewsletter
 * @since           1.3
 * @author          Xoops Development Team
 * @version         svn:$id$
 */
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

// This must contain the name of the folder in which reside xNewsletter
define("XNEWSLETTER_DIRNAME", basename(dirname(dirname(__FILE__))));
define("XNEWSLETTER_URL", XOOPS_URL . '/modules/' . XNEWSLETTER_DIRNAME);
define("XNEWSLETTER_ROOT_PATH", XOOPS_ROOT_PATH . '/modules/' . XNEWSLETTER_DIRNAME);
define("XNEWSLETTER_IMAGES_URL", XNEWSLETTER_URL . '/assets/images');
define("XNEWSLETTER_ADMIN_URL", XNEWSLETTER_URL . '/admin');
define("XNEWSLETTER_ICONS_URL", XNEWSLETTER_URL . '/assets/images/icons');

xoops_loadLanguage('common', XNEWSLETTER_DIRNAME);

//include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
//include_once XOOPS_ROOT_PATH . '/class/tree.php';
//include_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    include_once XNEWSLETTER_ROOT_PATH . '/include/config.php'; // IN PROGRESS
include_once XNEWSLETTER_ROOT_PATH . '/include/functions.php';
include_once XNEWSLETTER_ROOT_PATH . '/include/constants.php';
include_once XNEWSLETTER_ROOT_PATH . '/class/session.php'; // xNewsletterSession class
include_once XNEWSLETTER_ROOT_PATH . '/class/xnewsletter.php'; // xNewsletterxNewsletter class
include_once XNEWSLETTER_ROOT_PATH . '/class/request.php'; // xNewsletterRequest class
include_once XNEWSLETTER_ROOT_PATH . '/class/breadcrumb.php'; // xNewsletterBreadcrumb class

xoops_load('XoopsUserUtility');
// MyTextSanitizer object
$myts = MyTextSanitizer::getInstance();

$debug = false;
$xnewsletter = xNewsletterxNewsletter::getInstance($debug);

//This is needed or it will not work in blocks.
global $xnewsletter_isAdmin;

// Load only if module is installed
if (is_object($xnewsletter->getModule())) {
    // Find if the user is admin of the module
    $xnewsletter_isAdmin = xnewsletter_userIsAdmin();
}

// Load Xoops handlers
$module_handler = xoops_gethandler('module');
$member_handler = xoops_gethandler('member');
$notification_handler = &xoops_gethandler('notification');
$gperm_handler = xoops_gethandler('groupperm');
$config_handler = xoops_gethandler('config');

$pathIcon16 = XOOPS_URL . '/' . $xnewsletter->getModule()->getInfo('icons16');
$pathIcon32 = XOOPS_URL . '/' . $xnewsletter->getModule()->getInfo('icons32');
$pathModuleAdmin = XOOPS_ROOT_PATH . '/' . $xnewsletter->getModule()->getInfo('dirmoduleadmin');
require_once $pathModuleAdmin . '/moduleadmin/moduleadmin.php';
