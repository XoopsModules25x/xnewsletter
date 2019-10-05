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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xnewsletter
 * @since           1.3
 * @author          Xoops Development Team
 */

use XoopsModules\Xnewsletter;

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");

include dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName); //$capsDirName

/** @var \XoopsDatabase $db */
/** @var \XoopsModules\Xnewsletter\Helper $helper */
/** @var \XoopsModules\Xnewsletter\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$debug   = false;
$helper  = \XoopsModules\Xnewsletter\Helper::getInstance($debug);
$utility = new \XoopsModules\Xnewsletter\Utility();

$helper->loadLanguage('common');

$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32 = \Xmf\Module\Admin::iconUrl('', 32);
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
}

// This must contain the name of the folder in which reside xnewsletter
if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define('XNEWSLETTER_DIRNAME', basename(dirname(__DIR__)));
    define('XNEWSLETTER_URL', XOOPS_URL . '/modules/' . XNEWSLETTER_DIRNAME);
    define('XNEWSLETTER_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . XNEWSLETTER_DIRNAME);
    define('XNEWSLETTER_IMAGES_URL', XNEWSLETTER_URL . '/assets/images');
    define('XNEWSLETTER_ADMIN_URL', XNEWSLETTER_URL . '/admin');
    define('XNEWSLETTER_ICONS_URL', XNEWSLETTER_URL . '/assets/images/icons');
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);
}

require_once XNEWSLETTER_ROOT_PATH . '/config/config.php'; // IN PROGRESS
require_once XNEWSLETTER_ROOT_PATH . '/include/functions.php';
require_once XNEWSLETTER_ROOT_PATH . '/include/constants.php';
require_once XNEWSLETTER_ROOT_PATH . '/config/icons.php';

xoops_load('XoopsUserUtility');
// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

$moduleImageUrl      = XNEWSLETTER_URL . '/assets/images/xnewsletter.png';
$moduleCopyrightHtml = ''; //"<br><br><a href='' title='' target='_blank'><img src='{$moduleImageUrl}' alt=''></a>";

$debug  = false;
$helper = \XoopsModules\Xnewsletter\Helper::getInstance($debug);

//This is needed or it will not work in blocks.
global $xnewsletter_isAdmin;

// Load only if module is installed
if (is_object($helper->getModule())) {
    // Find if the user is admin of the module
    $xnewsletter_isAdmin = xnewsletter_userIsAdmin();
}
$xoopsModule = $helper->getModule();

// Load Xoops handlers
$moduleHandler = xoops_getHandler('module');
$memberHandler = xoops_getHandler('member');
/** @var \XoopsNotificationHandler $notificationHandler */
$notificationHandler = xoops_getHandler('notification');
$grouppermHandler    = xoops_getHandler('groupperm');
$configHandler       = xoops_getHandler('config');

$debug = false;

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);
// Local icons path
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');

    $GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
    $GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
}
