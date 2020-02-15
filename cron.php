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

$currentFile = basename(__FILE__);
require_once __DIR__ . '/header.php';

echo '<br>start cron.php';

require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/task.inc.php';
// protocol_level
// 0 = no protocol items will be created
// 1 = protocol will be created when newsletter sent or an error occurs (recommended)
// 2 = protocol will be created for all events (only for testing)
$protocol_level = $helper->getConfig('xn_cron_protocol');

// execute all pending tasks
$result_exec = xnewsletter_executeTasks($helper->getConfig('xn_send_in_packages'), 0, 1);

if ($protocol_level > 0) {
    echo '<br>protocol_level:' . $protocol_level;
    echo '<br>is_object(helper):'.is_object($helper);
    echo '<br>xn_send_in_packages:'.$helper->getConfig('xn_send_in_packages');
    if (_AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID === $result_exec) {
        $status = 'cron no task';
        if (2 == $protocol_level) {
            echo '<br>no letters for sending available';
            $protocolObj = $helper->getHandler('Protocol')->create();
            echo '<br>is_object(protocolObj):'.is_object($protocolObj);
            $protocolObj->setVar('protocol_letter_id', 0);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_status', 'Cron job: ' . _AM_XNEWSLETTER_TASK_NO_DATA);
            $protocolObj->setVar('protocol_status_str_id', 1);
            $protocolObj->setVar('protocol_status_vars', []);
            $protocolObj->setVar('protocol_success', true);
            $protocolObj->setVar('protocol_submitter', 0);
            $protocolObj->setVar('protocol_created', time());

            if ($helper->getHandler('Protocol')->insert($protocolObj)) {
                echo '<br>protocol successfully created';
            } else {
                echo $protocolObj->getHtmlErrors();
                echo '<br>errors when creating protocol';
            }
        }
    } else {
        $status = 'cron task available';
        echo "<br>result cron: {$result_exec}";
    }
    echo '<br>status: ' . $status;
}
echo '<br>finished cron.php';
