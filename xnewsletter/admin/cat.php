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
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op     = XoopsRequest::getString('op', 'list');
$cat_id = XoopsRequest::getInt('cat_id', 0);

switch ($op) {
    case 'list':
    case 'list_cats':
    default:
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        echo $indexAdmin->renderButton();
        //
        $catCount = $xnewsletter->getHandler('cat')->getCount();
        $GLOBALS['xoopsTpl']->assign('catCount', $catCount);
        if ($catCount > 0) {
            $catCriteria = new CriteriaCompo();
            //
            $catCriteria->setSort('cat_id ASC, cat_name');
            $catCriteria->setOrder('ASC');
            //
            $start = XoopsRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $catCriteria->setStart($start);
            $catCriteria->setLimit($limit);
            //
            if ($catCount > $limit) {
                xoops_load('xoopspagenav');
                $pagenav = new XoopsPageNav($catCount, $limit, $start, 'start', 'op=list');
                $pagenav = $pagenav->renderNav(4);
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('cats_pagenav', $pagenav);
            //
            $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);
            $cats = $xnewsletter->getHandler('cat')->getObjects($catCriteria, true, false); // as array
            $groupNames    = $member_handler->getGroupList();
            $gperm_handler = xoops_gethandler('groupperm');
            foreach ($cats as $cat_id => $cat) {
                // cat_gperms_admin
                $cat_gperms_admin_groupids = $gperm_handler->getGroupIds('newsletter_admin_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort($cat_gperms_admin_groupids);
                $cat_gperms_admin = array();
                foreach ($cat_gperms_admin_groupids as $groupid) {
                    $cat_gperms_admin[$groupid] = array(
                        'group_id' => $groupid,
                        'group_name' => $groupNames[$groupid]);
                }
                $cat['cat_gperms_admin_groups'] = $cat_gperms_admin;
                // cat_gperms_create
                $cat_gperms_create_groupids = $gperm_handler->getGroupIds('newsletter_create_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort($cat_gperms_create_groupids);
                $cat_gperms_create = array();
                foreach ($cat_gperms_create_groupids as $groupid) {
                    $cat_gperms_create[$groupid] = array(
                        'group_id' => $groupid,
                        'group_name' => $groupNames[$groupid]);
                }
                $cat['cat_gperms_create_groups'] = $cat_gperms_create;
                // cat_gperms_list
                $cat_gperms_list_groupids = $gperm_handler->getGroupIds('newsletter_list_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort($cat_gperms_list_groupids);
                $cat_gperms_list = array();
                foreach ($cat_gperms_list_groupids as $groupid) {
                    $cat_gperms_list[$groupid] = array(
                        'group_id' => $groupid,
                        'group_name' => $groupNames[$groupid]);
                }
                $cat['cat_gperms_list_groups'] = $cat_gperms_list;
                // cat_gperms_read
                $cat_gperms_read_groupids = $gperm_handler->getGroupIds('newsletter_read_cat', $cat_id, $xnewsletter->getModule()->mid());
                sort($cat_gperms_read_groupids);
                $cat_gperms_read = array();
                foreach ($cat_gperms_read_groupids as $groupid) {
                    $cat_gperms_read[$groupid] = array(
                        'group_id' => $groupid,
                        'group_name' => $groupNames[$groupid]);
                }
                $cat['cat_gperms_read_groups'] = $cat_gperms_read;
                //
                $GLOBALS['xoopsTpl']->append('cats', $cat);
            }
            // config
            $GLOBALS['xoopsTpl']->assign('xn_use_mailinglist', $xnewsletter->getConfig('xn_use_mailinglist'));
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_cats_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOCATS;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'new_cat':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $catObj = $xnewsletter->getHandler('cat')->create();
        $form   = $catObj->getForm();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'save_cat':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
        $catObj->setVar('cat_name', XoopsRequest::getString('cat_name', ''));
        $catObj->setVar('cat_info', $_REQUEST['cat_info']);
        $catObj->setVar('cat_mailinglist', XoopsRequest::getInt('cat_mailinglist', 0));
        $catObj->setVar('cat_submitter', $GLOBALS['xoopsUser']->uid());
        $catObj->setVar('cat_created', time());
        $catObj->setVar('dohtml', isset($_REQUEST['dohtml']));
        $catObj->setVar('dosmiley', isset($_REQUEST['dosmiley']));
        $catObj->setVar('doxcode', isset($_REQUEST['doxcode']));
        $catObj->setVar('doimage', isset($_REQUEST['doimage']));
        $catObj->setVar('dobr', isset($_REQUEST['dobr']));
        //
        if ($xnewsletter->getHandler('cat')->insert($catObj)) {
            $cat_id = $catObj->getVar('cat_id');
            //
            // Form cat_gperms_read
            $gperm_handler->deleteByModule($xnewsletter->getModule()->mid(), 'newsletter_read_cat', $cat_id);
            $gperm_handler->addRight('newsletter_read_cat', $cat_id, XOOPS_GROUP_ADMIN, $xnewsletter->getModule()->mid());
            $cat_gperms_read_groupids = XoopsRequest::getArray('cat_gperms_read', array());
            foreach ($cat_gperms_read_groupids as $groupid) {
                $gperm_handler->addRight('newsletter_read_cat', $cat_id, $groupid, $xnewsletter->getModule()->mid());
            }
            // Form cat_gperms_admin
            $gperm_handler->deleteByModule($xnewsletter->getModule()->mid(), 'newsletter_admin_cat', $cat_id);
            $gperm_handler->addRight('newsletter_admin_cat', $cat_id, XOOPS_GROUP_ADMIN, $xnewsletter->getModule()->mid());
            $cat_gperms_admin_groupids = XoopsRequest::getArray('cat_gperms_admin', array());
            foreach ($cat_gperms_admin_groupids as $groupid) {
                $gperm_handler->addRight('newsletter_admin_cat', $cat_id, $groupid, $xnewsletter->getModule()->mid());
            }
            // Form cat_gperms_create
            $gperm_handler->deleteByModule($xnewsletter->getModule()->mid(), 'newsletter_create_cat', $cat_id);
            $gperm_handler->addRight('newsletter_create_cat', $cat_id, XOOPS_GROUP_ADMIN, $xnewsletter->getModule()->mid());
            $cat_gperms_create_groupids = XoopsRequest::getArray('cat_gperms_create', array());
            foreach ($cat_gperms_create_groupids as $groupid) {
                $gperm_handler->addRight('newsletter_create_cat', $cat_id, $groupid, $xnewsletter->getModule()->mid());
            }
            // Form cat_gperms_list
            $gperm_handler->deleteByModule($xnewsletter->getModule()->mid(), 'newsletter_list_cat', $cat_id);
            $gperm_handler->addRight('newsletter_list_cat', $cat_id, XOOPS_GROUP_ADMIN, $xnewsletter->getModule()->mid());
            $cat_gperms_list_groupids = XoopsRequest::getArray('cat_gperms_list', array());
            foreach ($cat_gperms_list_groupids as $groupid) {
                $gperm_handler->addRight('newsletter_list_cat', $cat_id, $groupid, $xnewsletter->getModule()->mid());
            }
            //
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        echo $catObj->getHtmlErrors();
        $form = $catObj->getForm();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'edit_cat':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
        $form   = $catObj->getForm();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_cat':
        $catObj = $xnewsletter->getHandler('cat')->get($_REQUEST['cat_id']);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('cat')->delete($catObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $catObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array('ok' => true, 'cat_id' => $cat_id, 'op' => 'delete_cat'), $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $catObj->getVar('cat_name')));
        }
        include_once __DIR__ . '/admin_footer.php';
        break;
}
