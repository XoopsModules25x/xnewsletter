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
 * xNewsletter module for xoops
 *
 * @copyright       The TXMod XOOPS Project http://sourceforge.net/projects/thmod/
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GPL 2.0 or later
 * @package         xNewsletter
 * @since           2.5.x
 * @author          XOOPS Development Team ( name@site.com ) - ( http://xoops.org )
 * @version         $Id: task.php 12491 2014-04-25 13:21:55Z beckmi $
 */

include_once "admin_header.php";
xoops_cp_header();

//It recovered the value of argument op in URL$
$op = xNewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');
switch ($op) {
    case "list":
    default:
        echo $indexAdmin->addNavigation('task.php');

        $criteria = new CriteriaCompo();
        $criteria->setSort("task_id");
        $criteria->setOrder("ASC");
        $numrows = $xnewsletter->getHandler('xNewsletter_task')->getCount();
        $task_arr = $xnewsletter->getHandler('xNewsletter_task')->getall($criteria);

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
        if ($numrows > 0) {
            $class = "odd";
            foreach (array_keys($task_arr) as $i) {
                if ( $task_arr[$i]->getVar("task_pid") == 0) {
                    echo "<tr class='" . $class . "'>";
                    $class = ($class == "even") ? "odd" : "even";

                    $obj_letter =& $xnewsletter->getHandler('xNewsletter_letter')->get($task_arr[$i]->getVar("task_letter_id"));
                    $title_letter = $obj_letter->getVar("letter_title");
                    echo "<td align=\"center\">" . $title_letter . "</td>";
                    if ($task_arr[$i]->getVar("task_subscr_id") == 0) {
                        //send_test
                        $title_subscr = $obj_letter->getVar("letter_email_test") . "<br/>(send_test)";
                    } else {
                        $subscr =& $xnewsletter->getHandler('xNewsletter_subscr')->get($task_arr[$i]->getVar("task_subscr_id"));
                        if (is_object($subscr)) {
                            $title_subscr = $subscr->getVar("subscr_email");
                        } else {
                            $title_subscr = _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                        }
                    }
                    echo "<td align=\"center\">" . $title_subscr . "</td>";
                    echo "<td align=\"center\">" . formatTimeStamp($task_arr[$i]->getVar("task_starttime"), "mysql") . "</td>";
                    echo "<td class='center'>" . XoopsUser::getUnameFromId($task_arr[$i]->getVar("task_submitter"), "S") . "</td>";
                    echo "<td class='center'>" . formatTimeStamp($task_arr[$i]->getVar("task_created"), "mysql") . "</td>";
                    echo "<td align='center' width='10%'>";
                    echo "
                    <a href='task.php?op=delete_task&task_id=" . $task_arr[$i]->getVar("task_id") . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='". _DELETE . "' title='" . _DELETE . "'></a>
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
        $obj =& $xnewsletter->getHandler('xNewsletter_task')->get($_REQUEST["task_id"]);
        if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
            if (!$GLOBALS["xoopsSecurity"]->check()) {
                redirect_header("task.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($xnewsletter->getHandler('xNewsletter_task')->delete($obj)) {
                redirect_header("task.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "task_id" => $_REQUEST["task_id"], "op" => "delete_task"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $obj->getVar("task")));
        }
        break;
}
include_once "admin_footer.php";
