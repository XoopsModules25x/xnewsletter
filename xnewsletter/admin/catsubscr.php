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
$op = XoopsRequest::getString('op', 'list');

switch ($op) {
    case 'list':
    default:
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit       = $xnewsletter->getConfig('adminperpage');
        $catCriteria = new CriteriaCompo();
        $catCriteria->setSort('cat_id ASC, cat_name');
        $catCriteria->setOrder('ASC');
        $catCount = $xnewsletter->getHandler('cat')->getCount();
        $start    = XoopsRequest::getInt('start', 0);
        $catCriteria->setStart($start);
        $catCriteria->setLimit($limit);
        $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);
        if ($catCount > $limit) {
            xoops_load('xoopspagenav');
            $pagenav = new XoopsPageNav($catCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }
        // View Table
        echo "<table class='outer' cellspacing='1'>";
        echo "<tr>";
        echo "    <th>" . _AM_XNEWSLETTER_CAT_ID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CAT_NAME . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CAT_INFO . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_SUBSCRID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_FORMACTION . "</th>";
        echo "</tr>";
        if ($catCount > 0) {
            $class = 'odd';
            foreach ($catObjs as $cat_id => $catObj) {
                echo "<tr class='{$class}'>";
                $class = ($class == 'even') ? 'odd' : 'even';
                echo "<td>{$cat_id}</td>";
                echo "<td><a href='?op=list_cat&cat_id={$cat_id}'>{$catObj->getVar('cat_name')}</a></td>";
                echo "<td>{$catObj->getVar('cat_info')}</td>";
                $catCount = $xnewsletter->getHandler('catsubscr')->getCount(new Criteria('catsubscr_catid', $cat_id));
                echo "<td>{$catCount}</td>";
                echo "<td class='center'><a href='?op=list_cat&cat_id={$cat_id}'><img src='" . XNEWSLETTER_ICONS_URL . "/xn_details.png' alt='" . _AM_XNEWSLETTER_DETAILS . "' title='"
                    . _AM_XNEWSLETTER_DETAILS . "'></a></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<div class='center'>{$pagenav}</div>";
        echo "<br />";
        break;

    case 'list_cat':
        $cat_id = XoopsRequest::getInt('cat_id', 0);

        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATLIST, '?op=list', 'list');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit             = $xnewsletter->getConfig('adminperpage');
        $catsubscrCriteria = new CriteriaCompo();
        $catsubscrCriteria->add(new Criteria('catsubscr_catid', $cat_id));
        $catsubscrCriteria->setSort('catsubscr_id ASC, catsubscr_catid');
        $catsubscrCriteria->setOrder('ASC');
        $catCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
        $start    = XoopsRequest::getInt('start', 0);
        $catsubscrCriteria->setStart($start);
        $catsubscrCriteria->setLimit($limit);
        $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
        if ($catCount > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($catCount, $limit, $start, 'start', 'op=list_cat&cat_id=' . $cat_id);
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "<table class='outer' cellspacing='1'>";
        echo "<tr>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_ID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_CATID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_SUBSCRID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_QUITED . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_SUBMITTER . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_CATSUBSCR_CREATED . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_FORMACTION . "</th>";
        echo "</tr>";
        if ($catCount > 0) {
            $class = 'odd';
            foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                echo "<tr class='{$class}'>";
                $class = ($class == 'even') ? 'odd' : 'even';
                echo "<td>{$catsubscr_id}</td>";

                $cat      = $xnewsletter->getHandler('cat')->get($cat_id);
                $cat_name = $cat->getVar('cat_name');
                echo "<td>" . $cat_name . "</td>";
                $subscr_id    = $catsubscrObj->getVar('catsubscr_subscrid');
                $subscr       = $xnewsletter->getHandler('subscr')->get($subscr_id);
                $subscr_email = ($subscr) ? $subscr->getVar("subscr_email") : "";
                echo "<td>" . $subscr_email . "</td>";
                if ($catsubscrObj->getVar('catsubscr_quited') > 0) {
                    $catsubscr_quited = _YES . ' (' . formatTimeStamp($catsubscrObj->getVar('catsubscr_quited'), 'M') . ')';
                } else {
                    $catsubscr_quited = _NO;
                }
                echo "<td>" . $catsubscr_quited . "</td>";
                echo "<td>" . XoopsUser::getUnameFromId($catsubscrObj->getVar('catsubscr_submitter'), 'S') . "</td>";
                echo "<td>" . formatTimeStamp($catsubscrObj->getVar('catsubscr_created'), 'S') . "</td>";

                echo "<td class='center' nowrap='nowrap'>
                    <a href='?op=edit_catsubscr&catsubscr_id={$catsubscr_id}&cat_id={$cat_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>
                    &nbsp;<a href='?op=delete_catsubscr&catsubscr_id={$catsubscr_id}&cat_id={$cat_id}&cat_name={$cat_name}&subscr_email={$subscr_email}&subscr_id={$subscr_id}'><img src="
                    . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>
                    </td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<div>" . $pagenav . "</div>";
        echo "<br />";
        break;

    case 'new_catsubscr':
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
        $form         = $catsubscrObj->getForm();
        $form->display();
        break;

    case 'save_catsubscr':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (isset($_REQUEST['catsubscr_id'])) {
            $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($_REQUEST['catsubscr_id']);
        } else {
            $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
        }

        $catsubscrObj->setVar('catsubscr_catid', $_REQUEST['catsubscr_catid']);
        $catsubscr_subscrid = $_REQUEST['catsubscr_subscrid'];
        $catsubscrObj->setVar('catsubscr_subscrid', $catsubscr_subscrid);
        $catsubscr_quit_now = XoopsRequest::getInt('catsubscr_quit_now', _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NONE);
        if ($catsubscr_quit_now == _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NOW) {
            $catsubscrObj->setVar('catsubscr_quited', time());
        } elseif ($catsubscr_quit_now == _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_REMOVE) {
            $catsubscrObj->setVar('catsubscr_quited', 0);
        }
        $catsubscrObj->setVar('catsubscr_submitter', $_REQUEST['catsubscr_submitter']);
        $catsubscrObj->setVar('catsubscr_created', $_REQUEST['catsubscr_created']);

        if ($xnewsletter->getHandler('catsubscr')->insert($catsubscrObj)) {
            //add subscriber to mailinglist
            $catsubscrObj_cat = $xnewsletter->getHandler('cat')->get($_REQUEST['catsubscr_catid']);
            if ($catsubscrObj_cat->getVar('cat_mailinglist') > 0) {
                require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                subscribingMLHandler(1, $catsubscr_subscrid, $catsubscrObj_cat->getVar('cat_mailinglist'));
            }
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        echo $catsubscrObj->getHtmlErrors();
        $form = $catsubscrObj->getForm();
        $form->display();
        break;

    case 'edit_catsubscr':
        $cat_id = XoopsRequest::getInt('cat_id', 0);

        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_CATSUBSCRLIST, "?op=list_cat&cat_id={$cat_id}", 'list');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWCATSUBSCR, '?op=new_catsubscr', 'add');
        echo $indexAdmin->renderButton();
        //
        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($_REQUEST['catsubscr_id']);
        $form         = $catsubscrObj->getForm();
        $form->display();
        break;

    case 'delete_catsubscr':
        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($_REQUEST['catsubscr_id']);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('catsubscr.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('catsubscr')->delete($catsubscrObj)) {
                // remove subscriber from mailinglist
                $subscr_id        = $_REQUEST['subscr_id'];
                $catsubscrObj_cat = $xnewsletter->getHandler('cat')->get($_REQUEST['cat_id']);
                if ($catsubscrObj_cat->getVar('cat_mailinglist') > 0) {
                    require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                    subscribingMLHandler(0, $subscr_id, $catsubscrObj_cat->getVar('cat_mailinglist'));
                }
                redirect_header('catsubscr.php', 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $catsubscrObj->getHtmlErrors();
            }
        } else {
            $confirmtext = str_replace("%c", $_REQUEST['cat_name'], _AM_XNEWSLETTER_CATSUBSCR_SUREDELETE);
            $confirmtext = str_replace("%s", $_REQUEST['subscr_email'], $confirmtext);
            $confirmtext = str_replace('"', ' ', $confirmtext);
            xoops_confirm(array('ok' => true, 'catsubscr_id' => $_REQUEST['catsubscr_id'], 'op' => 'delete_catsubscr'), $_SERVER['REQUEST_URI'], sprintf($confirmtext));
        }
        break;
}
include_once __DIR__ . '/admin_footer.php';
