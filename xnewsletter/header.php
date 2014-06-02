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
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

include_once dirname(dirname(dirname(__FILE__))) . '/mainfile.php';
include_once dirname(__FILE__) . '/include/common.php';

include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
include_once XOOPS_ROOT_PATH . '/include/functions.php';

global $xoopsConfig;
/*

// Get Xoops instances
$gperm_handler = xoops_gethandler('groupperm');
$member_handler = xoops_gethandler('member');
$config_handler = xoops_gethandler("config");

xoops_load('XoopsUserUtility');

$myts = & MyTextSanitizer::getInstance();

// Get the module instances
$xnewsletter->getHandler('xNewsletter_accounts')    = xoops_getModuleHandler('xNewsletter_accounts', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_cat')         = xoops_getModuleHandler('xNewsletter_cat', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_subscr')      = xoops_getModuleHandler('xNewsletter_subscr', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_catsubscr')   = xoops_getModuleHandler('xNewsletter_catsubscr', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_letter')      = xoops_getModuleHandler('xNewsletter_letter', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_attachment')  = xoops_getModuleHandler('xNewsletter_attachment', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_protocol')    = xoops_getModuleHandler('xNewsletter_protocol', XNEWSLETTER_DIRNAME);
$xnewsletter->getHandler('xNewsletter_mailinglist') = xoops_getModuleHandler('xNewsletter_mailinglist', XNEWSLETTER_DIRNAME);
*/

//Load languages
xoops_loadLanguage('admin', $xnewsletter->getModule()->dirname());
xoops_loadLanguage('modinfo', $xnewsletter->getModule()->dirname());
xoops_loadLanguage('main', $xnewsletter->getModule()->dirname());
