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
// Module info/menu
$moduleInfo = $helper->getModule()->getInfo();
//$xoopsTpl->assign('xnewsletterModuleInfo', $moduleInfo); // huge array but useful?
$xoopsTpl->assign('xnewsletterModuleInfoSub', $moduleInfo['sub']);
// Module admin
$xoopsTpl->assign('isAdmin', xnewsletter_userIsAdmin());
// Extra info
// copyright
$xoopsTpl->assign('copyright', $moduleCopyrightHtml); // this definition is not removed for backward compatibility issues
$xoopsTpl->assign('copyright_code', $moduleCopyrightHtml); // config/config.php
// advertise
$xoopsTpl->assign('adv', $helper->getConfig('advertise')); // this definition is not removed for backward compatibility issues
$xoopsTpl->assign('advertise_code', $helper->getConfig('advertise'));
// social
if (true === $helper->getConfig('social_active')) {
    $xoopsTpl->assign('social_active', true);
    $xoopsTpl->assign('social_code', $helper->getConfig('social_code'));
}

require_once XOOPS_ROOT_PATH . '/footer.php';
