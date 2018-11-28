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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/header.php';

$xoopsOption['template_main'] = 'xnewsletter_letter.tpl';
include_once XOOPS_ROOT_PATH . '/header.php';

$xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
$xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
$xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

// Breadcrumb
$breadcrumb = new XnewsletterBreadcrumb();
$breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
$xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

include XOOPS_ROOT_PATH . '/modules/xnewsletter/include/task.inc.php';

if (!$xoopsUser) {
    //Guest no Access !!!
    redirect_header(XOOPS_URL . '/modules/' . $xnewsletter->getModule()->dirname() . '/index.php', 3, _NOPERM);
}

$op        = XoopsRequest::getString('op', 'list');
$letter_id = XoopsRequest::getInt('letter_id', 0);

if ($letter_id < 1) {
    redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID);
}

$sendletter_perm = xnewsletter_getUserPermissionsByLetter($letter_id);

if (!$sendletter_perm['send']) {
    redirect_header(XOOPS_URL . '/modules/' . $xnewsletter->getModule()->dirname() . '/index.php', 3, _NOPERM);
}

$start_sending = false;
$protocolCriteria = new CriteriaCompo();
$protocolCriteria->add(new Criteria('protocol_letter_id', $letter_id));
$protocolCriteria->add(new Criteria('protocol_subscriber_id', 0, '>'));
$protocolCriteria->setLimit(1);
$protocolCount = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);
if ($protocolCount > 0) {
    if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == true) {
        $start_sending = true;
    } else {
        xoops_confirm(['ok' => true, 'op' => $op, 'letter_id' => $letter_id], $_SERVER['REQUEST_URI'], _AM_XNEWSLETTER_SEND_SURE_SENT );
    }
} else {
    $start_sending = true;
}

if ($start_sending == true) {
    $xn_send_in_packages = $xnewsletter->getConfig('xn_send_in_packages');
    if ($xn_send_in_packages > 0) {
        $xn_send_in_packages_time = $xnewsletter->getConfig('xn_send_in_packages_time');
    } else {
        $xn_send_in_packages_time = 0;
    }
    $result_create = xnewsletter_createTasks($op, $letter_id, $xn_send_in_packages, $xn_send_in_packages_time);
    $result_exec = xnewsletter_executeTasks($xn_send_in_packages, $letter_id);
    redirect_header('letter.php', 3, $result_exec);
}

include __DIR__ . '/footer.php';
