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
use XoopsModules\Xnewsletter;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op     = \Xmf\Request::getString('op', 'list');
$cat_id = \Xmf\Request::getInt('cat_id', 0);

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        $adminObject->displayButton('left');

        $start       = \Xmf\Request::getInt('start', 0);
        $limit       = $helper->getConfig('adminperpage');
        $catsCount   = $helper->getHandler('Cat')->getCount();
        $catCriteria = new \CriteriaCompo();
        $catCriteria->setSort('cat_id ASC, cat_name');
        $catCriteria->setOrder('ASC');
        $catCriteria->setStart($start);
        $catCriteria->setLimit($limit);
        $catObjs = $helper->getHandler('Cat')->getAll($catCriteria);
        if ($catsCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($catsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "<table class='outer width100' cellspacing='1'>";
        echo '<tr>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_ID . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_NAME . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_INFO . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_GPERMS_ADMIN . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_GPERMS_CREATE . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_GPERMS_LIST . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_CAT_GPERMS_READ . '</th>';
        if (true === $helper->getConfig('xn_use_mailinglist')) {
            echo '    <th>' . _AM_XNEWSLETTER_CAT_MAILINGLIST . '</th>';
        }
        echo '    <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>';
        echo '</tr>';

        if (count($catObjs) > 0) {
            $class            = 'odd';
            $groupNames       = $memberHandler->getGroupList();
            $grouppermHandler = xoops_getHandler('groupperm');
            foreach ($catObjs as $cat_id => $catObj) {
                echo "<tr class='{$class}'>";
                $class = ('even' === $class) ? 'odd' : 'even';
                echo "<td>{$cat_id}</td>";
                echo "<td>{$catObj->getVar('cat_name')}</td>";
                echo "<td>{$catObj->getVar('cat_info')}&nbsp;</td>";
                // cat_gperms_admin
                $cat_gperms_admin_groupids = $grouppermHandler->getGroupIds('newsletter_admin_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_admin_groupids);
                $cat_gperms_admin = '';
                foreach ($cat_gperms_admin_groupids as $groupid) {
                    $cat_gperms_admin .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_admin = mb_substr($cat_gperms_admin, 0, -3);
                echo '<td>' . $cat_gperms_admin . '</td>';

                // cat_gperms_create
                $cat_gperms_create_groupids = $grouppermHandler->getGroupIds('newsletter_create_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_create_groupids);
                $cat_gperms_create = '';
                foreach ($cat_gperms_create_groupids as $groupid) {
                    $cat_gperms_create .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_create = mb_substr($cat_gperms_create, 0, -3);
                echo '<td>' . $cat_gperms_create . '</td>';

                // cat_gperms_list
                $cat_gperms_list_groupids = $grouppermHandler->getGroupIds('newsletter_list_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_list_groupids);
                $cat_gperms_list = '';
                foreach ($cat_gperms_list_groupids as $groupid) {
                    $cat_gperms_list .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_list = mb_substr($cat_gperms_list, 0, -3);
                echo '<td>' . $cat_gperms_list . '</td>';

                // cat_gperms_read
                $cat_gperms_read_groupids = $grouppermHandler->getGroupIds('newsletter_read_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_read_groupids);
                $cat_gperms_read = '';
                foreach ($cat_gperms_read_groupids as $groupid) {
                    $cat_gperms_read .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_read = mb_substr($cat_gperms_read, 0, -3);
                echo '<td>' . $cat_gperms_read . '</td>';

                if (true === $helper->getConfig('xn_use_mailinglist')) {
                    echo '<td>' . $catObj->getVar('cat_mailinglist') . '</td>';
                }
                echo "<td class='center' nowrap='nowrap'>";
                echo "<a href='?op=edit_cat&cat_id={$cat_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>";
                echo '&nbsp;';
                echo "<a href='?op=delete_cat&cat_id={$cat_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>";
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '<br>';
        echo '<div>' . $pagenav . '</div>';
        echo '<br>';
        break;
    case 'new_cat':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $catObj = $helper->getHandler('Cat')->create();
        $form   = $catObj->getForm();
        $form->display();
        break;
    case 'save_cat':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $catObj = $helper->getHandler('Cat')->get($cat_id);
        $catObj->setVar('cat_name', \Xmf\Request::getString('cat_name', ''));
        $catObj->setVar('cat_info', $_REQUEST['cat_info']);
        $catObj->setVar('cat_mailinglist', \Xmf\Request::getInt('cat_mailinglist', 0));
        $catObj->setVar('cat_submitter', $xoopsUser->uid());
        $catObj->setVar('cat_created', time());
        $catObj->setVar('dohtml', isset($_REQUEST['dohtml']));
        $catObj->setVar('dosmiley', isset($_REQUEST['dosmiley']));
        $catObj->setVar('doxcode', isset($_REQUEST['doxcode']));
        $catObj->setVar('doimage', isset($_REQUEST['doimage']));
        $catObj->setVar('dobr', isset($_REQUEST['dobr']));

        if ($helper->getHandler('Cat')->insert($catObj)) {
            $cat_id = $catObj->getVar('cat_id');
            //
            // Form cat_gperms_read
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_read_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_read_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_read_groupids = \Xmf\Request::getArray('cat_gperms_read', []);
            foreach ($cat_gperms_read_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_read_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }
            // Form cat_gperms_admin
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_admin_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_admin_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_admin_groupids = \Xmf\Request::getArray('cat_gperms_admin', []);
            foreach ($cat_gperms_admin_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_admin_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }
            // Form cat_gperms_create
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_create_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_create_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_create_groupids = \Xmf\Request::getArray('cat_gperms_create', []);
            foreach ($cat_gperms_create_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_create_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }
            // Form cat_gperms_list
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_list_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_list_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_list_groupids = \Xmf\Request::getArray('cat_gperms_list', []);
            foreach ($cat_gperms_list_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_list_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }

            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        echo $catObj->getHtmlErrors();
        $form = $catObj->getForm();
        $form->display();
        break;
    case 'edit_cat':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $catObj = $helper->getHandler('Cat')->get($cat_id);
        $form   = $catObj->getForm();
        $form->display();
        break;
    case 'delete_cat':
        $catObj = $helper->getHandler('Cat')->get($_REQUEST['cat_id']);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Cat')->delete($catObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $catObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'cat_id' => $cat_id, 'op' => 'delete_cat'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $catObj->getVar('cat_name')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
