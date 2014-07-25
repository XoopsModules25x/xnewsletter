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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GPL 2.0 or later
 * @package         xnewsletter
 * @since           2.5.x
 * @author          XOOPS Development Team ( name@site.com ) - ( http://xoops.org )
 * @version         $Id: task.php 12491 2014-04-25 13:21:55Z beckmi $
 */

$currentFile = basename(__FILE__);
include_once "admin_header.php";
xoops_cp_header();

//It recovered the value of argument op in URL$
$op = xnewsletterRequest::getString('op', 'list');
switch ($op) {
    case "list":
    default:
        echo $indexAdmin->addNavigation($currentFile);
        //
        $taskCriteria = new CriteriaCompo();
        $taskCriteria->setSort("task_id");
        $taskCriteria->setOrder("ASC");
        $taskCounts = $xnewsletter->getHandler('task')->getCount();
        $taskObjs = $xnewsletter->getHandler('task')->getAll($taskCriteria);

        //Affichage du tableau
        echo "
        <table width='100%' cellspacing='1' class='outer'>
            <tr>
                <th align=\"center\">" . _AM_XNEWSLETTER_TASK_LETTER_ID . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_TASK_SUBSCR_ID . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_TASK_STARTTIME . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_TASK_SUBMITTER . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_TASK_CREATED . "</th>
                <th align='center' width='10%'>" . _AM_XNEWSLETTER_FORMACTION . "</th>
            </tr>";
        if ($taskCounts > 0) {
            $class = "odd";
            foreach ($taskObjs as $task_id => $taskObj) {
                if ($taskObj->getVar("task_pid") == 0) {
                    echo "<tr class='" . $class . "'>";
                    $class = ($class == "even") ? "odd" : "even";

                    $letterObj = $xnewsletter->getHandler('letter')->get($taskObj->getVar("task_letter_id"));
                    $title_letter = $letterObj->getVar("letter_title");
                    echo "<td align=\"center\">" . $title_letter . "</td>";
                    if ($taskObj->getVar("task_subscr_id") == 0) {
                        //send_test
                        $title_subscr = $letterObj->getVar("letter_email_test") . "<br/>(send_test)";
                    } else {
                        $subscr = $xnewsletter->getHandler('subscr')->get($taskObj->getVar("task_subscr_id"));
                        if (is_object($subscr)) {
                            $title_subscr = $subscr->getVar("subscr_email");
                        } else {
                            $title_subscr = _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                        }
                    }
                    echo "<td align=\"center\">" . $title_subscr . "</td>";
                    echo "<td align=\"center\">" . formatTimeStamp($taskObj->getVar("task_starttime"), "mysql") . "</td>";
                    echo "<td class='center'>" . XoopsUser::getUnameFromId($taskObj->getVar("task_submitter"), "S") . "</td>";
                    echo "<td class='center'>" . formatTimeStamp($taskObj->getVar("task_created"), "mysql") . "</td>";
                    echo "<td align='center' width='10%'>";
                    echo "
                    <a href='?op=delete_task&task_id=" . $taskObj->getVar("task_id") . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='". _DELETE . "' title='" . _DELETE . "'></a>
                    </td>";
                    echo "</tr>";
                }
            }
        } else {
            echo "<tr><td colspan='7'>" . _AM_XNEWSLETTER_TASK_NO_DATA . "</td></tr>";
        }
        echo "</table><br /><br />";
        break;

    case "delete_task":
        $taskObj = $xnewsletter->getHandler('task')->get($_REQUEST["task_id"]);
        if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS["xoopsSecurity"]->check()) {
                redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('task')->delete($taskObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $taskObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => true, "task_id" => $_REQUEST["task_id"], "op" => "delete_task"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $taskObj->getVar("task")));
        }
        break;
}
include_once "admin_footer.php";
