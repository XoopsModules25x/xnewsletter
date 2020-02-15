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

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_catsubscribers.tpl';

// We recovered the value of the argument op in the URL$
$op     = Request::getString('op', 'list');
$cat_id = Request::getInt('cat_id', 0);

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr&cat_id=' . $cat_id, 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $limit       = $helper->getConfig('adminperpage');
        $catCriteria = new \CriteriaCompo();
        $catCriteria->setSort('cat_id ASC, cat_name');
        $catCriteria->setOrder('ASC');
        $catCount = $helper->getHandler('Cat')->getCount();
        $start    = Request::getInt('start', 0);
        $catCriteria->setStart($start);
        $catCriteria->setLimit($limit);
        $catAll = $helper->getHandler('Cat')->getAll($catCriteria);
        if ($catCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($catCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }
        
        if ($catCount > 0) {
            $GLOBALS['xoopsTpl']->assign('categories_count', $catCount);
            foreach ($catAll as $cat_id => $catObj) {
                $cat = $catObj->getValuesCat();
                $cat['subscrCount'] = $helper->getHandler('Catsubscr')->getCount(new \Criteria('catsubscr_catid', $cat_id));
                $GLOBALS['xoopsTpl']->append('categories_list', $cat);
                unset($cat);
            }
        }
        break;
    case 'list_cat':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr&cat_id=' . $cat_id, 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $limit             = $helper->getConfig('adminperpage');
        $catsubscrCriteria = new \CriteriaCompo();
        $catsubscrCriteria->add(new \Criteria('catsubscr_catid', $cat_id));
        $catsubscrCriteria->setSort('catsubscr_id ASC, catsubscr_catid');
        $catsubscrCriteria->setOrder('ASC');
        $catsubscrCount = $helper->getHandler('Catsubscr')->getCount($catsubscrCriteria);
        $start    = Request::getInt('start', 0);
        $catsubscrCriteria->setStart($start);
        $catsubscrCriteria->setLimit($limit);
        $catsubscrAll = $helper->getHandler('Catsubscr')->getAll($catsubscrCriteria);
        if ($catsubscrCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($catsubscrCount, $limit, $start, 'start', 'op=list_cat&cat_id=' . $cat_id);
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }
        $cat      = $helper->getHandler('Cat')->get($cat_id);
        $cat_name = $cat->getVar('cat_name');

        if ($catsubscrCount > 0) {
            $GLOBALS['xoopsTpl']->assign('catsubscr_count', $catsubscrCount);
            $class = 'odd';
            foreach ($catsubscrAll as $catsubscr_id => $catsubscrObj) {
                $catsubscr = $catsubscrObj->getValuesCatsubscr();
                $catsubscr['cat_name'] = $cat_name;
                $subscr_id    = $catsubscrObj->getVar('catsubscr_subscrid');
                $subscr       = $helper->getHandler('Subscr')->get($subscr_id);
                $subscr_email = $subscr ? $subscr->getVar('subscr_email') : '';
                $catsubscr['subscr_email'] = $subscr_email;
                if ($catsubscrObj->getVar('catsubscr_quited') > 0) {
                    $catsubscr_quited = _YES . ' (' . formatTimestamp($catsubscrObj->getVar('catsubscr_quited'), 'M') . ')';
                } else {
                    $catsubscr_quited = _NO;
                }
                $catsubscr['quited_text'] = $catsubscr_quited;
                $GLOBALS['xoopsTpl']->append('catsubscribers_list', $catsubscr);
                unset($cat);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_CATSUBSCR);
        }
        break;
    case 'new_catsubscr':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $catsubscrObj = $helper->getHandler('Catsubscr')->create();
        if ($cat_id > 0) {
            $catsubscrObj->setVar('catsubscr_catid', $cat_id);
        }
        $form         = $catsubscrObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_catsubscr':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (Request::hasVar('catsubscr_id', 'REQUEST')) {
            $catsubscrObj = $helper->getHandler('Catsubscr')->get($_REQUEST['catsubscr_id']);
        } else {
            $catsubscrObj = $helper->getHandler('Catsubscr')->create();
        }

        $catsubscr_catid = Request::getInt('catsubscr_catid', 0);
        $catsubscrObj->setVar('catsubscr_catid', $catsubscr_catid);
        $catsubscr_subscrid = Request::getInt('catsubscr_subscrid', 0);
        $catsubscrObj->setVar('catsubscr_subscrid', $catsubscr_subscrid);
        $catsubscr_quit_now = Request::getInt('catsubscr_quit_now', _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NONE);
        if (_XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NOW == $catsubscr_quit_now) {
            $catsubscrObj->setVar('catsubscr_quited', time());
        } elseif (_XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_REMOVE == $catsubscr_quit_now) {
            $catsubscrObj->setVar('catsubscr_quited', 0);
        }
        $catsubscrObj->setVar('catsubscr_submitter', Request::getInt('catsubscr_submitter', 0));
        $catsubscrObj->setVar('catsubscr_created', Request::getInt('catsubscr_created', 0));

        if ($helper->getHandler('Catsubscr')->insert($catsubscrObj)) {
            //add subscriber to mailinglist
            $catsubscrObj_cat = $helper->getHandler('Cat')->get($catsubscr_catid);
            if ($catsubscrObj_cat->getVar('cat_mailinglist') > 0) {
                require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                subscribingMLHandler(_XNEWSLETTER_MAILINGLIST_SUBSCRIBE, $catsubscr_subscrid, $catsubscrObj_cat->getVar('cat_mailinglist'));
            }
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        $GLOBALS['xoopsTpl']->assign('error', $catsubscrObj->getHtmlErrors());
        $form = $catsubscrObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_catsubscr':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, "?op=list_cat&cat_id={$cat_id}", 'list');
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $catsubscrObj = $helper->getHandler('Catsubscr')->get($_REQUEST['catsubscr_id']);
        $form         = $catsubscrObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_catsubscr':
        $catsubscrObj = $helper->getHandler('Catsubscr')->get($_REQUEST['catsubscr_id']);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('catsubscr.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Catsubscr')->delete($catsubscrObj)) {
                // remove subscriber from mailinglist
                $subscr_id        = $_REQUEST['subscr_id'];
                $catsubscrObj_cat = $helper->getHandler('Cat')->get($_REQUEST['cat_id']);
                if ($catsubscrObj_cat->getVar('cat_mailinglist') > 0) {
                    require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                    subscribingMLHandler(_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE, $subscr_id, $catsubscrObj_cat->getVar('cat_mailinglist'));
                }
                redirect_header('catsubscr.php', 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $catsubscrObj->getHtmlErrors());
            }
        } else {
            $confirmtext = str_replace('%c', $_REQUEST['cat_name'], _AM_XNEWSLETTER_CATSUBSCR_SUREDELETE);
            $confirmtext = str_replace('%s', $_REQUEST['subscr_email'], $confirmtext);
            $confirmtext = str_replace('"', ' ', $confirmtext);
            xoops_confirm(['ok' => true, 'catsubscr_id' => $_REQUEST['catsubscr_id'], 'op' => 'delete_catsubscr'], $_SERVER['REQUEST_URI'], sprintf($confirmtext));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
