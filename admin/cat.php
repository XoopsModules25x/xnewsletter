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

// set template
$templateMain = 'xnewsletter_admin_categories.tpl';

// We recovered the value of the argument op in the URL$
$op     = Request::getString('op', 'list');
$cat_id = Request::getInt('cat_id', 0);

$GLOBALS['xoopsTpl']->assign('use_mailinglist', $helper->getConfig('xn_use_mailinglist'));
$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $start       = Request::getInt('start', 0);
        $limit       = $helper->getConfig('adminperpage');
        $catsCount   = $helper->getHandler('Cat')->getCount();
        $catCriteria = new \CriteriaCompo();
        $catCriteria->setSort('cat_id ASC, cat_name');
        $catCriteria->setOrder('ASC');
        $catCriteria->setStart($start);
        $catCriteria->setLimit($limit);
        $catAll = $helper->getHandler('Cat')->getAll($catCriteria);
        if ($catsCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($catsCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }

        if ($catsCount > 0) {
            $GLOBALS['xoopsTpl']->assign('categories_count', $catsCount);
            $groupNames = $memberHandler->getGroupList();
            /** @var \XoopsGroupPermHandler $grouppermHandler */
            $grouppermHandler = xoops_getHandler('groupperm');
            foreach (array_keys($catAll) as $i) {
                $cat    = $catAll[$i]->getValuesCat();
                $cat_id = $cat['id'];
                
                // cat_gperms_admin
                $cat_gperms_admin_groupids = $grouppermHandler->getGroupIds('newsletter_admin_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_admin_groupids);
                $cat_gperms_admin = '';
                foreach ($cat_gperms_admin_groupids as $groupid) {
                    $cat_gperms_admin .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_admin = mb_substr($cat_gperms_admin, 0, -3);
                $cat['gperms_admin'] = $cat_gperms_admin;
                // cat_gperms_create
                $cat_gperms_create_groupids = $grouppermHandler->getGroupIds('newsletter_create_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_create_groupids);
                $cat_gperms_create = '';
                foreach ($cat_gperms_create_groupids as $groupid) {
                    $cat_gperms_create .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_create = mb_substr($cat_gperms_create, 0, -3);
                $cat['gperms_create'] = $cat_gperms_create;
                // cat_gperms_list
                $cat_gperms_list_groupids = $grouppermHandler->getGroupIds('newsletter_list_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_list_groupids);
                $cat_gperms_list = '';
                foreach ($cat_gperms_list_groupids as $groupid) {
                    $cat_gperms_list .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_list = mb_substr($cat_gperms_list, 0, -3);
                $cat['gperms_list'] = $cat_gperms_list;

                // cat_gperms_read
                $cat_gperms_read_groupids = $grouppermHandler->getGroupIds('newsletter_read_cat', $cat_id, $helper->getModule()->mid());
                sort($cat_gperms_read_groupids);
                $cat_gperms_read = '';
                foreach ($cat_gperms_read_groupids as $groupid) {
                    $cat_gperms_read .= $groupNames[$groupid] . ' | ';
                }
                $cat_gperms_read = mb_substr($cat_gperms_read, 0, -3);
                $cat['gperms_read'] = $cat_gperms_read;

                $GLOBALS['xoopsTpl']->append('categories_list', $cat);
                unset($cat);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_CAT);
        }
        break;
    case 'new_cat':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $catObj = $helper->getHandler('Cat')->create();
        $form   = $catObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_cat':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $catObj = $helper->getHandler('Cat')->get($cat_id);
        $catObj->setVar('cat_name',        Request::getString('cat_name', ''));
        $catObj->setVar('cat_info',        Request::getString('cat_info', ''));
        $catObj->setVar('cat_mailinglist', Request::getInt('cat_mailinglist', 0));
        $catObj->setVar('cat_submitter',   $xoopsUser->uid());
        $catObj->setVar('cat_created',     time());
        $catObj->setVar('dohtml',          Request::getInt('dohtml', 0));
        $catObj->setVar('dosmiley',        Request::getInt('dosmiley', 0));
        $catObj->setVar('doxcode',         Request::getInt('doxcode', 0));
        $catObj->setVar('doimage',         Request::getInt('doimage', 0));
        $catObj->setVar('dobr',            Request::getInt('dobr', 0));

        if ($helper->getHandler('Cat')->insert($catObj)) {
            $cat_id = $catObj->getVar('cat_id');
            //
            // Form cat_gperms_read
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_read_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_read_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_read_groupids = Request::getArray('cat_gperms_read', []);
            foreach ($cat_gperms_read_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_read_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }
            // Form cat_gperms_admin
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_admin_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_admin_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_admin_groupids = Request::getArray('cat_gperms_admin', []);
            foreach ($cat_gperms_admin_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_admin_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }
            // Form cat_gperms_create
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_create_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_create_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_create_groupids = Request::getArray('cat_gperms_create', []);
            foreach ($cat_gperms_create_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_create_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }
            // Form cat_gperms_list
            $grouppermHandler->deleteByModule($helper->getModule()->mid(), 'newsletter_list_cat', $cat_id);
            $grouppermHandler->addRight('newsletter_list_cat', $cat_id, XOOPS_GROUP_ADMIN, $helper->getModule()->mid());
            $cat_gperms_list_groupids = Request::getArray('cat_gperms_list', []);
            foreach ($cat_gperms_list_groupids as $groupid) {
                $grouppermHandler->addRight('newsletter_list_cat', $cat_id, $groupid, $helper->getModule()->mid());
            }

            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        $GLOBALS['xoopsTpl']->assign('error', $catObj->getHtmlErrors());
        $form = $catObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_cat':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCAT, '?op=new_cat', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $catObj = $helper->getHandler('Cat')->get($cat_id);
        $form   = $catObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_cat':
        $catObj = $helper->getHandler('Cat')->get($_REQUEST['cat_id']);
        // check whether there are existing sbuscription to this cat
        if ($helper->getHandler('Catsubscr')->getCount(new \Criteria('catsubscr_catid', $cat_id)) > 0) {
            redirect_header($currentFile, 5, _AM_XNEWSLETTER_CAT_DELETE_ERROR);
        }
        
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Cat')->delete($catObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $catObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'cat_id' => $cat_id, 'op' => 'delete_cat'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $catObj->getVar('cat_name')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
