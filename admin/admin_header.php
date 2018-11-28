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
 *  @copyright  Goffy ( wedega.com )
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once dirname(__DIR__) . '/include/common.php';

// Include xoops admin header
include_once XOOPS_ROOT_PATH . '/include/cp_functions.php';

$pathIcon16 = XOOPS_URL . '/' . $xnewsletter->getModule()->getInfo('icons16');
$pathIcon32 = XOOPS_URL . '/' . $xnewsletter->getModule()->getInfo('icons32');
$pathModuleAdmin = XOOPS_ROOT_PATH . '/' . $xnewsletter->getModule()->getInfo('dirmoduleadmin');
require_once $pathModuleAdmin . '/moduleadmin/moduleadmin.php';

//Load languages
xoops_loadLanguage('admin', $xnewsletter->getModule()->dirname());
xoops_loadLanguage('modinfo', $xnewsletter->getModule()->dirname());
xoops_loadLanguage('main', $xnewsletter->getModule()->dirname());

define('XNEWSLETTER_IMG_OK', "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_ok.png' alt='" . _AM_XNEWSLETTER_OK . "' title='"._AM_XNEWSLETTER_OK . "' />&nbsp;&nbsp;");
define('XNEWSLETTER_IMG_FAILED', "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_failed.png' alt='" . _AM_XNEWSLETTER_FAILED . "' title='" . _AM_XNEWSLETTER_FAILED . "' />&nbsp;&nbsp;");

if (!xnewsletter_checkModuleAdmin()) {
    xoops_cp_header();
    echo xoops_error(_AM_XNEWSLETTER_NOFRAMEWORKS);
    xoops_cp_footer();
    exit();
}

//$pathIcon = XOOPS_URL . "/modules/" . $dirname . "/assets/images/icons";
$indexAdmin = new ModuleAdmin();

$myts = MyTextSanitizer::getInstance();

if ($xoopsUser) {
    $moduleperm_handler = xoops_getHandler('groupperm');
    if (!$moduleperm_handler->checkRight('module_admin', $xnewsletter->getModule()->mid(), $xoopsUser->getGroups())) {
        redirect_header(XOOPS_URL, 1, _NOPERM);
        exit();
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
    exit();
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new XoopsTpl();
}
