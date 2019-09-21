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

include dirname(__DIR__) . '/preloads/autoloader.php';

/** @var \XoopsModules\Xnewsletter\Helper $helper */
$helper = \XoopsModules\Xnewsletter\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
if (is_object($helper->getModule())) {
    $pathModIcon32 = $helper->url($helper->getModule()->getInfo('modicons32'));
}

//$pathImageAdmin = 'assets/images/icons';

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU1,
    'link'  => 'admin/index.php',
    'icon'  => $pathModIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU3,
    'link'  => 'admin/cat.php',
    'icon'  => $pathModIcon32 . '/xn_category.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU4,
    'link'  => 'admin/subscr.php',
    'icon'  => $pathModIcon32 . '/xn_subscribers.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU5,
    'link'  => 'admin/catsubscr.php',
    'icon'  => $pathModIcon32 . '/xn_category_subscr.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU6,
    'link'  => 'admin/letter.php',
    'icon'  => $pathModIcon32 . '/xn_newsletter.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU_TEMPLATES,
    'link'  => 'admin/template.php',
    'icon'  => $pathModIcon32 . '/tpls.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU7,
    'link'  => 'admin/attachment.php',
    'icon'  => $pathModIcon32 . '/xn_attachment.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU8,
    'link'  => 'admin/protocol.php',
    'icon'  => $pathModIcon32 . '/xn_protocol.png',
];

if (1 == $helper->getConfig('xn_use_mailinglist')) {
    $adminmenu[] = [
        'title' => _MI_XNEWSLETTER_ADMENU9,
        'link'  => 'admin/mailinglist.php',
        'icon'  => $pathModIcon32 . '/xn_mailinglist.png',
    ];
}

if ($helper->getConfig('xn_send_in_packages') > 0) {
    $adminmenu[] = [
        'title' => _MI_XNEWSLETTER_ADMENU13,
        'link'  => 'admin/task.php',
        'icon'  => $pathModIcon32 . '/xn_task.png',
    ];
}

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU10,
    'link'  => 'admin/bmh.php',
    'icon'  => $pathModIcon32 . '/xn_bmh.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU2,
    'link'  => 'admin/accounts.php',
    'icon'  => $pathModIcon32 . '/xn_accounts.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU11,
    'link'  => 'admin/maintenance.php',
    'icon'  => $pathModIcon32 . '/xn_maintenance.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU12,
    'link'  => 'admin/import.php',
    'icon'  => $pathModIcon32 . '/xn_import.png',
];

$adminmenu[] = [
    'title' => _MI_XNEWSLETTER_ADMENU99,
    'link'  => 'admin/about.php',
    'icon'  => $pathModIcon32 . '/about.png',
];
