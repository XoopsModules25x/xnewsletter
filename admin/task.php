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
 * xnewsletter module for xoops
 *
 * @copyright       The TXMod XOOPS Project http://sourceforge.net/projects/thmod/
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GPL 2.0 or later
 * @package         xnewsletter
 * @since           2.5.x
 * @author          XOOPS Development Team ( name@site.com ) - ( https://xoops.org )
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_tasks.tpl';

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

//It recovered the value of argument op in URL$
$op = \Xmf\Request::getString('op', 'list');

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);

        $taskCriteria = new \CriteriaCompo();
//        $taskCriteria->add(new \Criteria('task_pid', 0));
        $taskCriteria->setSort('task_id');
        $taskCriteria->setOrder('ASC');
        $taskCounts = $helper->getHandler('Task')->getCount();
        $tasksAll   = $helper->getHandler('Task')->getAll($taskCriteria);

        if ($taskCounts > 0) {
            $GLOBALS['xoopsTpl']->assign('taskCounts', $taskCounts);
            foreach ($tasksAll as $task_id => $taskObj) {
                $task = $taskObj->getValuesTask();
                $task = $taskObj->getValuesTask();

                if (0 == $taskObj->getVar('task_subscr_id')) {
                    //send_test
                    $letterObj    = $helper->getHandler('Letter')->get($taskObj->getVar('task_letter_id'));
                    $task['subscr_email'] = $letterObj->getVar('letter_email_test') . '<br>(send_test)';
                } else {
                    $subscr = $helper->getHandler('Subscr')->get($taskObj->getVar('task_subscr_id'));
                    if (is_object($subscr)) {
                        $task['subscr_email'] = $subscr->getVar('subscr_email');
                    } else {
                        $task['subscr_email'] = _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                    }
                }
                $GLOBALS['xoopsTpl']->append('tasks_list', $task);
                unset($task);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_TASK_NO_DATA);
        }
        break;
    case 'delete_task':
        $taskObj = $helper->getHandler('Task')->get($_REQUEST['task_id']);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Task')->delete($taskObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $taskObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'task_id' => $_REQUEST['task_id'], 'op' => 'delete_task'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $taskObj->getVar('task')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
