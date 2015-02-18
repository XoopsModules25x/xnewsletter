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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op = XoopsRequest::getString('op', 'list');

switch ($op) {
    case 'list':
    default :
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, '?op=new_mailinglist', 'add');
        echo $indexAdmin->renderButton();
        $limit = $xnewsletter->getConfig('adminperpage');
        $mailinglistCriteria = new CriteriaCompo();
        $mailinglistCriteria->setSort('mailinglist_id ASC, mailinglist_email');
        $mailinglistCriteria->setOrder('ASC');
        $mailinglistCount = $xnewsletter->getHandler('mailinglist')->getCount();
        $start            = XoopsRequest::getInt('start', 0);
        $mailinglistCriteria->setStart($start);
        $mailinglistCriteria->setLimit($limit);
        $mailinglistObjs = $xnewsletter->getHandler('mailinglist')->getAll($mailinglistCriteria);
        if ($mailinglistCount > $limit) {
            xoops_load('xoopspagenav');
            $pagenav = new XoopsPageNav($mailinglistCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "
        <table class='outer width100' cellspacing='1'>
            <tr>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_ID . "</th>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_NAME . "</th>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_EMAIL . "</th>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_LISTNAME . "</th>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE . "</th>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE . "</th>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_CREATED . "</th>
                <th>" . _AM_XNEWSLETTER_FORMACTION . "</th>
            </tr>
            ";
        if ($mailinglistCount > 0) {
            $class = 'odd';
            foreach ($mailinglistObjs as $mailinglist_id => $mailinglistObj) {
                echo "<tr class='{$class}'>";
                $class = ($class == 'even') ? 'odd' : 'even';
                echo "<td>" . $mailinglist_id . "</td>";
                echo "<td>" . $mailinglistObj->getVar("mailinglist_name") . "</td>";
                echo "<td>" . $mailinglistObj->getVar("mailinglist_email") . "</td>";
                echo "<td>" . $mailinglistObj->getVar("mailinglist_listname") . "</td>";
                echo "<td>" . $mailinglistObj->getVar("mailinglist_subscribe") . "</td>";
                echo "<td>" . $mailinglistObj->getVar("mailinglist_unsubscribe") . "</td>";
                echo "<td>" . formatTimeStamp($mailinglistObj->getVar("mailinglist_created"), "S") . "</td>";
                echo "<td>";
                echo "    <a href='?op=edit_mailinglist&mailinglist_id=" . $mailinglist_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo "    <a href='?op=delete_mailinglist&mailinglist_id=" . $mailinglist_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE
                    . "' /></a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<div>{$pagenav}</div>";
        echo "<br />";
        break;

    case 'new_mailinglist':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $mailinglistObj = $xnewsletter->getHandler('mailinglist')->create();
        $form           = $mailinglistObj->getForm();
        $form->display();
        break;

    case 'save_mailinglist' :
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (isset($_REQUEST['mailinglist_id'])) {
            $mailinglistObj = $xnewsletter->getHandler('mailinglist')->get($_REQUEST["mailinglist_id"]);
        } else {
            $mailinglistObj = $xnewsletter->getHandler('mailinglist')->create();
        }
        $mailinglistObj->setVar("mailinglist_name", $_REQUEST['mailinglist_name']);
        $mailinglistObj->setVar("mailinglist_email", $_REQUEST['mailinglist_email']);
        $mailinglistObj->setVar("mailinglist_listname", $_REQUEST['mailinglist_listname']);
        $mailinglistObj->setVar("mailinglist_subscribe", $_REQUEST['mailinglist_subscribe']);
        $mailinglistObj->setVar("mailinglist_unsubscribe", $_REQUEST['mailinglist_unsubscribe']);
        $mailinglistObj->setVar("mailinglist_submitter", $_REQUEST['mailinglist_submitter']);
        $mailinglistObj->setVar("mailinglist_created", $_REQUEST['mailinglist_created']);
        //
        if ($xnewsletter->getHandler('mailinglist')->insert($mailinglistObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }
        //
        echo $mailinglistObj->getHtmlErrors();
        $form = $mailinglistObj->getForm();
        $form->display();
        break;

    case 'edit_mailinglist':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, '?op=new_mailinglist', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $mailinglistObj = $xnewsletter->getHandler('mailinglist')->get($_REQUEST['mailinglist_id']);
        $form           = $mailinglistObj->getForm();
        $form->display();
        break;

    case 'delete_mailinglist':
        $mailinglistObj = $xnewsletter->getHandler('mailinglist')->get($_REQUEST['mailinglist_id']);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('mailinglist')->delete($mailinglistObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $mailinglistObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                array('ok' => true, 'mailinglist_id' => $_REQUEST['mailinglist_id'], 'op' => 'delete_mailinglist'),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $mailinglistObj->getVar('mailinglist_email'))
            );
        }
        break;
}
include_once __DIR__ . '/admin_footer.php';
