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
 *  Version : $Id $
 * ****************************************************************************
 */

include_once dirname(dirname(__FILE__)) . '/include/common.php';
$xnewsletter = xnewsletterxnewsletter::getInstance();
$pathImageAdmin = 'assets/images/icons';

$adminmenu = array();
$i = 1;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU1;
$adminmenu[$i]["link"] = "admin/index.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/home.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU2;
$adminmenu[$i]["link"] = "admin/accounts.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_accounts.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU3;
$adminmenu[$i]["link"] = "admin/cat.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_category.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU4;
$adminmenu[$i]["link"] = "admin/subscr.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_subscribers.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU5;
$adminmenu[$i]["link"] = "admin/catsubscr.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_category_subscr.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU6;
$adminmenu[$i]["link"] = "admin/letter.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_newsletter.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU7;
$adminmenu[$i]["link"] = "admin/attachment.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_attachment.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU8;
$adminmenu[$i]["link"] = "admin/protocol.php";
$adminmenu[$i]["icon"] = $pathImageAdmin."/xn_protocol.png";
if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
    ++$i;
    $adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU9;
    $adminmenu[$i]["link"]  = "admin/mailinglist.php";
    $adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_mailinglist.png";
}
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    ++$i;
    $adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU13;
    $adminmenu[$i]["link"] = "admin/task.php";
    $adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_task.png";
}
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU10;
$adminmenu[$i]["link"] = "admin/bmh.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_bmh.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU11;
$adminmenu[$i]["link"] = "admin/maintenance.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_maintenance.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU12;
$adminmenu[$i]["link"] = "admin/import.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/xn_import.png";
++$i;
$adminmenu[$i]["title"] = _MI_XNEWSLETTER_ADMENU99;
$adminmenu[$i]["link"]  = "admin/about.php";
$adminmenu[$i]["icon"] = $pathImageAdmin . "/about.png";
unset($i);
