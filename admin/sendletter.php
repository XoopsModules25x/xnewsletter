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

use Xmf\Request;

require_once __DIR__ . '/admin_header.php';

$op                  = \Xmf\Request::getString('op', 'list');
$letter_id           = \Xmf\Request::getInt('letter_id', 0);
$xn_send_in_packages = $helper->getConfig('xn_send_in_packages');
if ($xn_send_in_packages > 0 && 'send_test' !== $op) {
    $xn_send_in_packages_time = $helper->getConfig('xn_send_in_packages_time');
} else {
    $xn_send_in_packages_time = 0;
}

require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/task.inc.php';
// create tasks
$result_create = xnewsletter_createTasks($op, $letter_id, $xn_send_in_packages, $xn_send_in_packages_time);
// execute tasks
$result_exec = xnewsletter_executeTasks($xn_send_in_packages, $letter_id);
redirect_header('letter.php', 3, $result_exec);

require_once __DIR__ . '/admin_footer.php';
