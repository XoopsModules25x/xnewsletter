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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/header.php';

echo '<br/>start cron job<br/>';

include_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/task.inc.php';
// execute all pending tasks
$result_exec = xnewsletter_executeTasks($xnewsletter->getConfig('xn_send_in_packages'), 0);

if ($result_exec != '') {
    $newsletter->getHandler('protocol')->protocol(0, 0, 'Cron: ' . $result_exec, _XNEWSLETTER_PROTOCOL_STATUS_CRON, array('%result_exec' => $result_exec), true);
/*
    // you can enable the block for creating protocol for cron
    $protocolObj = $xnewsletter->getHandler('protocol')->create();
    $protocolObj->setVar('protocol_letter_id', 0);
    $protocolObj->setVar('protocol_subscriber_id', 0);
    $protocolObj->setVar('protocol_status', 'Cron: ' . $result_exec); // old style
    $protocolObj->setVar('protocol_status_str_id', _XNEWSLETTER_PROTOCOL_STATUS_CRON); // new from v1.3
    $protocolObj->setVar('protocol_status_vars', array('%result_exec' => $result_exec)); // new from v1.3
    $protocolObj->setVar('protocol_success', true);
    $protocolObj->setVar('protocol_submitter', 0);
    $protocolObj->setVar('protocol_created', time());

    if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
        // create protocol is ok
        // NOP
    } else {
        echo $protocolObj->getHtmlErrors();
    }
*/
}
echo "<br/>result cron: {$result_exec}";
