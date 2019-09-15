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

use XoopsModules\Xnewsletter;

require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
//require_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');

// require_once  dirname(__DIR__) . '/class/Utility.php';
require_once dirname(__DIR__) . '/include/common.php';

$moduleDirName = basename(dirname(__DIR__));

$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32 = \Xmf\Module\Admin::iconUrl('', 32);

//Load languages
xoops_loadLanguage('admin', $helper->getModule()->dirname());
xoops_loadLanguage('modinfo', $helper->getModule()->dirname());
xoops_loadLanguage('main', $helper->getModule()->dirname());

define('XNEWSLETTER_IMG_OK', "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_ok.png' alt='" . _AM_XNEWSLETTER_OK . "' title='" . _AM_XNEWSLETTER_OK . "'>&nbsp;&nbsp;");
define('XNEWSLETTER_IMG_FAILED', "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_failed.png' alt='" . _AM_XNEWSLETTER_FAILED . "' title='" . _AM_XNEWSLETTER_FAILED . "'>&nbsp;&nbsp;");

if (!xnewsletter_checkModuleAdmin()) {
    xoops_cp_header();
    echo xoops_error(_AM_XNEWSLETTER_NOFRAMEWORKS);
    xoops_cp_footer();
    exit();
}

//$pathIcon = XOOPS_URL . "/modules/" . $dirname . "/assets/images/icons";
$adminObject = \Xmf\Module\Admin::getInstance();

$myts = \MyTextSanitizer::getInstance();

if ($xoopsUser) {
    $grouppermHandler = xoops_getHandler('groupperm');
    if (!$grouppermHandler->checkRight('module_admin', $helper->getModule()->mid(), $xoopsUser->getGroups())) {
        redirect_header(XOOPS_URL, 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new \XoopsTpl();
}
