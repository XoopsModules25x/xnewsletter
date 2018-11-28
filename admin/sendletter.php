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

include_once __DIR__ . '/admin_header.php';
include XOOPS_ROOT_PATH . '/modules/xnewsletter/include/task.inc.php';

$op        = XoopsRequest::getString('op', 'list');
$letter_id = XoopsRequest::getInt('letter_id', 0);

$xn_send_in_packages = $xnewsletter->getConfig('xn_send_in_packages');

if ($xn_send_in_packages > 0 && $op !== 'send_test') {
    $xn_send_in_packages_time = $xnewsletter->getConfig('xn_send_in_packages_time');
} else {
    $xn_send_in_packages_time = 0;
}

$result = xnewsletter_createTasks($op, $letter_id, $xn_send_in_packages, $xn_send_in_packages_time);

$result_exec = xnewsletter_executeTasks($xn_send_in_packages, $letter_id);

redirect_header('letter.php', 3, $result_exec);

include_once __DIR__ . '/admin_footer.php';
