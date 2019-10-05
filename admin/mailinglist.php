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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op = \Xmf\Request::getString('op', 'list');

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, '?op=new_mailinglist', 'add');
        $adminObject->displayButton('left');
        $limit               = $helper->getConfig('adminperpage');
        $mailinglistCriteria = new \CriteriaCompo();
        $mailinglistCriteria->setSort('mailinglist_id ASC, mailinglist_email');
        $mailinglistCriteria->setOrder('ASC');
        $mailinglistCount = $helper->getHandler('Mailinglist')->getCount();
        $start            = \Xmf\Request::getInt('start', 0);
        $mailinglistCriteria->setStart($start);
        $mailinglistCriteria->setLimit($limit);
        $mailinglistObjs = $helper->getHandler('Mailinglist')->getAll($mailinglistCriteria);
        if ($mailinglistCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($mailinglistCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "
        <table class='outer width100' cellspacing='1'>
            <tr>
                <th>" . _AM_XNEWSLETTER_MAILINGLIST_ID . '</th>
                <th>' . _AM_XNEWSLETTER_MAILINGLIST_NAME . '</th>
                <th>' . _AM_XNEWSLETTER_MAILINGLIST_EMAIL . '</th>
                <th>' . _AM_XNEWSLETTER_MAILINGLIST_LISTNAME . '</th>
                <th>' . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE . '</th>
                <th>' . _AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE . '</th>
                <th>' . _AM_XNEWSLETTER_MAILINGLIST_CREATED . '</th>
                <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>
            </tr>
            ';
        if ($mailinglistCount > 0) {
            $class = 'odd';
            foreach ($mailinglistObjs as $mailinglist_id => $mailinglistObj) {
                echo "<tr class='{$class}'>";
                $class = ('even' === $class) ? 'odd' : 'even';
                echo '<td>' . $mailinglist_id . '</td>';
                echo '<td>' . $mailinglistObj->getVar('mailinglist_name') . '</td>';
                echo '<td>' . $mailinglistObj->getVar('mailinglist_email') . '</td>';
                echo '<td>' . $mailinglistObj->getVar('mailinglist_listname') . '</td>';
                echo '<td>' . $mailinglistObj->getVar('mailinglist_subscribe') . '</td>';
                echo '<td>' . $mailinglistObj->getVar('mailinglist_unsubscribe') . '</td>';
                echo '<td>' . formatTimestamp($mailinglistObj->getVar('mailinglist_created'), 'S') . '</td>';
                echo '<td>';
                echo "    <a href='?op=edit_mailinglist&mailinglist_id=" . $mailinglist_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>";
                echo "    <a href='?op=delete_mailinglist&mailinglist_id=" . $mailinglist_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>";
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '<br>';
        echo "<div>{$pagenav}</div>";
        echo '<br>';
        break;
    case 'new_mailinglist':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $mailinglistObj = $helper->getHandler('Mailinglist')->create();
        $form           = $mailinglistObj->getForm();
        $form->display();
        break;
    case 'save_mailinglist':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (\Xmf\Request::hasVar('mailinglist_id', 'REQUEST')) {
            $mailinglistObj = $helper->getHandler('Mailinglist')->get($_REQUEST['mailinglist_id']);
        } else {
            $mailinglistObj = $helper->getHandler('Mailinglist')->create();
        }
        $mailinglistObj->setVar('mailinglist_name', $_REQUEST['mailinglist_name']);
        $mailinglistObj->setVar('mailinglist_email', $_REQUEST['mailinglist_email']);
        $mailinglistObj->setVar('mailinglist_listname', $_REQUEST['mailinglist_listname']);
        $mailinglistObj->setVar('mailinglist_subscribe', $_REQUEST['mailinglist_subscribe']);
        $mailinglistObj->setVar('mailinglist_unsubscribe', $_REQUEST['mailinglist_unsubscribe']);
        $mailinglistObj->setVar('mailinglist_submitter', $_REQUEST['mailinglist_submitter']);
        $mailinglistObj->setVar('mailinglist_created', $_REQUEST['mailinglist_created']);

        if ($helper->getHandler('Mailinglist')->insert($mailinglistObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        echo $mailinglistObj->getHtmlErrors();
        $form = $mailinglistObj->getForm();
        $form->display();
        break;
    case 'edit_mailinglist':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, '?op=new_mailinglist', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $mailinglistObj = $helper->getHandler('Mailinglist')->get($_REQUEST['mailinglist_id']);
        $form           = $mailinglistObj->getForm();
        $form->display();
        break;
    case 'delete_mailinglist':
        $mailinglistObj = $helper->getHandler('Mailinglist')->get($_REQUEST['mailinglist_id']);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Mailinglist')->delete($mailinglistObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $mailinglistObj->getHtmlErrors();
            }
        } else {
            xoops_confirm([
                              'ok'             => true,
                              'mailinglist_id' => $_REQUEST['mailinglist_id'],
                              'op'             => 'delete_mailinglist',
                          ], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $mailinglistObj->getVar('mailinglist_email')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
