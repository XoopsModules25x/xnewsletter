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

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op     = XoopsRequest::getString('op', 'list');
$cat_id = XoopsRequest::getInt('cat_id', 0);

switch ($op) {
    case 'list' :
    default:
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $catCriteria = new CriteriaCompo();
        $catCriteria->setSort('cat_id ASC, cat_name');
        $catCriteria->setOrder('ASC');
        $catsCount = $xnewsletter->getHandler('cat')->getCount();
        $start = XoopsRequest::getInt('start', 0);
        $catCriteria->setStart($start);
        $catCriteria->setLimit($limit);
        $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);
        if ($catsCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new XoopsPageNav($catsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($catsCount > 0) {
            echo "<table class='outer width100' cellspacing='1'>";
            echo '<tr>';
            echo "    <th class='center width2'>"._AM_XNEWSLETTER_CAT_ID . '</th>';
            echo "    <th class='center'>" . _AM_XNEWSLETTER_CAT_NAME . '</th>';
            echo "    <th class='center'>" . _AM_XNEWSLETTER_CAT_INFO . '</th>';
            echo "    <th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_ADMIN . '</th>';
            echo "    <th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_CREATE . '</th>';
            echo "    <th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_LIST . '</th>';
            echo "    <th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_READ . '</th>';
            if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
                echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_MAILINGLIST . '</th>';
            }
            echo "<th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . '</th>';
            echo '</tr>';

            $class = 'odd';

            $member_handler = xoops_getHandler('member');
            $grouplist = $member_handler->getGroupList();

            $gperm_handler = xoops_getHandler('groupperm');

            foreach ($catObjs as $cat_id => $catObj) {
                echo "<tr class='" . $class . "'>";
                $class = ($class === 'even') ? 'odd' : 'even';
                echo "<td class='center'>" . $cat_id . '</td>';
                echo "<td class='center'>" . $catObj->getVar('cat_name') . '</td>';
                echo '<td>' . $catObj->getVar('cat_info') . '&nbsp;</td>';

                // cat_gperms_admin;
                $arr_cat_gperms_admin = '';
                $cat_gperms_admin = '';
                $arr_cat_gperms_admin = $gperm_handler->getGroupIds('newsletter_admin_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort ( $arr_cat_gperms_admin );
                foreach ($arr_cat_gperms_admin as $groupid_admin) {
                    $cat_gperms_admin .= $grouplist[$groupid_admin] . ' | ';
                }
                $cat_gperms_admin = substr($cat_gperms_admin, 0, -3);
                echo "<td class='center'>" . $cat_gperms_admin . '</td>';

                // cat_gperms_create
                $arr_cat_gperms_create = '';
                $cat_gperms_create     = '';
                $arr_cat_gperms_create = $gperm_handler->getGroupIds('newsletter_create_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort ( $arr_cat_gperms_create );
                foreach ($arr_cat_gperms_create as $groupid_create) {
                    $cat_gperms_create .= $grouplist[$groupid_create] . ' | ';
                }
                $cat_gperms_create = substr($cat_gperms_create, 0, -3);
                echo "<td class='center'>" . $cat_gperms_create . '</td>';

                // cat_gperms_list
                $cat_gperms_list = '';
                $arr_cat_gperms_list = '';
                $arr_cat_gperms_list = $gperm_handler->getGroupIds('newsletter_list_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort ( $arr_cat_gperms_list );
                foreach ($arr_cat_gperms_list as $groupid_list) {
                    $cat_gperms_list .= $grouplist[$groupid_list] . ' | ';
                }
                $cat_gperms_list = substr($cat_gperms_list, 0, -3);
                echo "<td class='center'>" . $cat_gperms_list . '</td>';

                // cat_gperms_read
                $cat_gperms_read = '';
                $arr_cat_groupperms = '';
                $arr_cat_groupperms = $gperm_handler->getGroupIds('newsletter_read_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort ( $arr_cat_groupperms );
                foreach ($arr_cat_groupperms as $groupid_read) {
                    $cat_gperms_read .= $grouplist[$groupid_read] . ' | ';
                }
                $cat_gperms_read = substr($cat_gperms_read, 0, -3);
                echo "<td class='center'>". $cat_gperms_read . '</td>';

                if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
                    echo "<td class='center'>" . $catObj->getVar('cat_mailinglist') . '</td>';
                }
                echo "<td class='center width5' nowrap='nowrap'>";
                echo "<a href='?op=edit_cat&cat_id=" . $cat_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo '&nbsp;';
                echo "<a href='?op=delete_cat&cat_id=" . $cat_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                echo '</td>';
                echo '</tr>';
            }
            echo '</table><br /><br />';
            echo "<br /><div class='center'>" . $pagenav . '</div><br />';
        } else {
            echo "<table class='outer width100' cellspacing='1'>";
            echo '<tr>';
            echo "<th class='center width2'>" . _AM_XNEWSLETTER_CAT_ID . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_NAME . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_INFO . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_ADMIN . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_CREATE . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_READ . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_GPERMS_LIST . '</th>';
            echo "<th class='center'>" . _AM_XNEWSLETTER_CAT_MAILINGLIST . '</th>';
            echo "<th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . '</th>';
            echo '</tr>';
            echo '</table><br /><br />';
        }
        break;

    case 'new_cat' :
        echo $indexAdmin->addNavigation($currentFile);
        $catsCount = $xnewsletter->getHandler('cat')->getCount();
        if (!empty($catsCount)) {
            $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
            echo $indexAdmin->renderButton();
        }
        //
        $catObj = $xnewsletter->getHandler('cat')->create();
        $form = $catObj->getForm();
        $form->display();
        break;

    case 'save_cat':
        if ( !$GLOBALS['xoopsSecurity']->check() ) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
        $catObj->setVar('cat_name', $_POST['cat_name'] );
        $catObj->setVar('cat_info', $_POST['cat_info'] );

        global $xoopsDB;

        $gperm_handler = xoops_getHandler('groupperm');

        // Form cat_mailinglist
        $cat_mailinglist = !empty($_REQUEST['cat_mailinglist']) ? (int)$_REQUEST['cat_mailinglist'] : 0;
        $catObj->setVar('cat_mailinglist', $cat_mailinglist);

        // Form cat_submitter
        $catObj->setVar('cat_submitter', $xoopsUser->uid());
        // Form cat_created
        $catObj->setVar('cat_created', time());

        if ($xnewsletter->getHandler('cat')->insert($catObj)) {
            $cat_id = $catObj->getVar('cat_id');

            //Form cat_gperms_admin
            $arr_cat_gperms_create = $_POST['cat_gperms_admin'];
            if ($cat_id > 0) {
                $sql = 'DELETE FROM `' . $xoopsDB->prefix('group_permission') . '`';
                $sql.= " WHERE `gperm_name`='newsletter_admin_cat' AND `gperm_itemid`={$cat_id};";
                $xoopsDB->query($sql);
            }
            //admin
            $gperm = $gperm_handler->create();
            $gperm->setVar('gperm_groupid', XOOPS_GROUP_ADMIN);
            $gperm->setVar('gperm_name', 'newsletter_admin_cat');
            $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
            $gperm->setVar('gperm_itemid', $cat_id);
            $gperm_handler->insert($gperm);
            unset($gperm);
            foreach ($arr_cat_gperms_create as $key => $cat_groupperm) {
                $gperm = $gperm_handler->create();
                $gperm->setVar('gperm_groupid', $cat_groupperm);
                $gperm->setVar('gperm_name', 'newsletter_admin_cat');
                $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
                $gperm->setVar('gperm_itemid', $cat_id);
                $gperm_handler->insert($gperm);
                unset($gperm);
            }

            // Form cat_gperms_create
            $arr_cat_gperms_create = $_POST['cat_gperms_create'];
            if ($cat_id > 0) {
                $sql = 'DELETE FROM `' . $xoopsDB->prefix('group_permission') . '`';
                $sql.= " WHERE `gperm_name`='newsletter_create_cat' AND `gperm_itemid`={$cat_id};";
                $xoopsDB->query($sql);
            }
            //admin
            $gperm = $gperm_handler->create();
            $gperm->setVar('gperm_groupid', XOOPS_GROUP_ADMIN);
            $gperm->setVar('gperm_name', 'newsletter_create_cat');
            $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
            $gperm->setVar('gperm_itemid', $cat_id);
            $gperm_handler->insert($gperm);
            unset($gperm);
            foreach ($arr_cat_gperms_create as $key => $cat_groupperm) {
                $gperm = $gperm_handler->create();
                $gperm->setVar('gperm_groupid', $cat_groupperm);
                $gperm->setVar('gperm_name', 'newsletter_create_cat');
                $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
                $gperm->setVar('gperm_itemid', $cat_id);
                $gperm_handler->insert($gperm);
                unset($gperm);
            }

            // Form cat_gperms_read
            $arr_cat_gperms_read = $_POST['cat_gperms_read'];
            if ($cat_id > 0) {
                $sql = 'DELETE FROM `' . $xoopsDB->prefix('group_permission') . '`';
                $sql.= " WHERE `gperm_name`='newsletter_read_cat' AND `gperm_itemid`={$cat_id};";
                $xoopsDB->query($sql);
            }
            //admin
            $gperm = $gperm_handler->create();
            $gperm->setVar('gperm_groupid', XOOPS_GROUP_ADMIN);
            $gperm->setVar('gperm_name', 'newsletter_read_cat');
            $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
            $gperm->setVar('gperm_itemid', $cat_id);
            $gperm_handler->insert($gperm);
            unset($gperm);
            foreach ($arr_cat_gperms_read as $key => $cat_groupperm) {
                $gperm = $gperm_handler->create();
                $gperm->setVar('gperm_groupid', $cat_groupperm);
                $gperm->setVar('gperm_name', 'newsletter_read_cat');
                $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
                $gperm->setVar('gperm_itemid', $cat_id);
                $gperm_handler->insert($gperm);
                unset($gperm);
            }

            // Form cat_gperms_list
            $arr_cat_gperms_list = $_POST['cat_gperms_list'];
            if ($cat_id > 0) {
                $sql = 'DELETE FROM `' . $xoopsDB->prefix('group_permission') . '`';
                $sql.= " WHERE `gperm_name`='newsletter_list_cat' AND `gperm_itemid`={$cat_id};";
                $xoopsDB->query($sql);
            }
            //admin
            $gperm = $gperm_handler->create();
            $gperm->setVar('gperm_groupid', XOOPS_GROUP_ADMIN);
            $gperm->setVar('gperm_name', 'newsletter_list_cat');
            $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
            $gperm->setVar('gperm_itemid', $cat_id);
            $gperm_handler->insert($gperm);
            unset($gperm);
            foreach ($arr_cat_gperms_list as $key => $cat_groupperm) {
                $gperm = $gperm_handler->create();
                $gperm->setVar('gperm_groupid', $cat_groupperm);
                $gperm->setVar('gperm_name', 'newsletter_list_cat');
                $gperm->setVar('gperm_modid', $xnewsletter->getModule()->mid());
                $gperm->setVar('gperm_itemid', $cat_id);
                $gperm_handler->insert($gperm);
                unset($gperm);
            }

            redirect_header('?op=list', 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $catObj->getHtmlErrors();
        $form = $catObj->getForm();
        $form->display();
        break;

    case 'edit_cat' :
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
        $form = $catObj->getForm();
        $form->display();
        break;

    case 'delete_cat' :
        $catObj = $xnewsletter->getHandler('cat')->get($_REQUEST['cat_id']);
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if ( !$GLOBALS['xoopsSecurity']->check() ) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('cat')->delete($catObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $catObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => 1, 'cat_id' => $cat_id, 'op' => 'delete_cat'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $catObj->getVar('cat_name')));
        }
        break;
}
include_once __DIR__ . '/admin_footer.php';
