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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';

// We recovered the value of the argument op in the URL$
$op = XoopsRequest::getString('op', 'list_subscrs');
switch ($op) {
    case 'show_catsubscr':
        $subscr_id = XoopsRequest::getInt('subscr_id', 0);
        // render start here
        xoops_cp_header();
        // render submenu
        $subscrAdmin = new ModuleAdmin();
        echo $subscrAdmin->addNavigation($currentFile);
        //
        $prev_op = XoopsRequest::getString('prev_op', 'list_subscrs');
        $linklist = "?op=$prev_op&filter_subscr={$filter_subscr}";
        $linklist .= "&filter_subscr_firstname={$filter_subscr_firstname}";
        $linklist .= "&filter_subscr_lastname={$filter_subscr_lastname}";
        $linklist .= "&filter_subscr_email={$filter_subscr_email}";
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, $linklist, 'view_detailed');
        echo $subscrAdmin->renderButton();

        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);

        echo "<table class='outer' cellspacing='1'>
                <tr>
                    <th>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>
                    <th>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                    <th>" . _AM_XNEWSLETTER_LETTERLIST . "</th>
                </tr>";

        $class = 'odd';
        echo "<tr class='{$class}'>";
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<td>{$subscr_id}</td>";
        echo "<td>" . $subscrObj->getVar('subscr_email') . "</td>";
        echo "<td>";
        $catsubscrCriteria = new CriteriaCompo();
        $catsubscrCriteria->add(new Criteria('catsubscr_subscrid', $subscr_id));
        $catsubscrCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
        if ($catsubscrCount > 0) {
            $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
            foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                $cat_id = $catsubscrObj->getVar("catsubscr_catid");
                $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
                echo $catObj->getVar('cat_name') . "<br/>";
            }
        } else {
            echo _AM_XNEWSLETTER_SUBSCR_NO_CATSUBSCR;
        }
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'list':
    case 'list_subscrs':
    default:
        $apply_filter = XoopsRequest::getBool('apply_filter', false);
        // render start here
        xoops_cp_header();
        // render submenu
        $subscrAdmin = new ModuleAdmin();
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        if ($apply_filter == true) {
            $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, '?op=list_subscrs', 'view_detailed');
        }
        echo $subscrAdmin->renderButton();
        //
        $subsrCount = $xnewsletter->getHandler('subscr')->getCount();
        $GLOBALS['xoopsTpl']->assign('subsrCount', $subsrCount);
        if ($subsrCount > 0) {
            $subscrCriteria = new CriteriaCompo();
            // get filter parameters
            $filter_subscr_criteria = XoopsRequest::getString('filter_subscr_criteria', '=');
            $filter_subscr_firstname = XoopsRequest::getString('filter_subscr_firstname', '');
            $filter_subscr_lastname  = XoopsRequest::getString('filter_subscr_lastname', '');
            $filter_subscr_email = XoopsRequest::getString('filter_subscr_email', '');
            if ($apply_filter == true) {
                switch ($filter_subscr_criteria) {
                    case 'CONTAINS':
                        $pre = '%';
                        $post = '%';
                        $function = 'LIKE';
                        break;
                    case 'MATCHES':
                        $pre = '';
                        $post = '';
                        $function = '=';
                        break;
                    case 'STARTSWITH':
                        $pre = '';
                        $post = '%';
                        $function = 'LIKE';
                        break;
                    case 'ENDSWITH':
                        $pre = '%';
                        $post = '';
                        $function = 'LIKE';
                        break;
                }
                if ($filter_subscr_firstname == '' && $filter_subscr_lastname == '' && $filter_subscr_email == '') {
                    $op = 'list_subscrs';
                }
                // apply filter
                if ($filter_subscr_firstname != '') {
                    $subscrCriteria->add(new Criteria('subscr_firstname', $pre . $filter_subscr_firstname . $post, $function));
                }
                if ($filter_subscr_lastname != '') {
                    $subscrCriteria->add(new Criteria('subscr_lastname', $pre . $filter_subscr_lastname . $post, $function));
                }
                if ($filter_subscr_email != '') {
                    $subscrCriteria->add(new Criteria('subscr_email', $pre . $filter_subscr_email . $post, $function));
                }
            }
            $GLOBALS['xoopsTpl']->assign('apply_filter', $apply_filter);
            $subsrFilterCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
            $GLOBALS['xoopsTpl']->assign('subsrFilterCount', $subsrFilterCount);
            //
            $subscrCriteria->setSort('subscr_id');
            $subscrCriteria->setOrder('DESC');
            //
            $start = XoopsRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $subscrCriteria->setStart($start);
            $subscrCriteria->setLimit($limit);
            //
            $subscrObjs = $xnewsletter->getHandler('subscr')->getAll($subscrCriteria);
            $subscrs = $xnewsletter->getHandler('subscr')->getObjects($subscrCriteria, true, false); // as array
            if ($subsrFilterCount > $limit) {
                xoops_load('xoopspagenav');
                $linklist = "op={$op}";
                $linklist .= "&filter_subscr_criteria={$filter_subscr_criteria}";
                $linklist .= "&filter_subscr_firstname={$filter_subscr_firstname}";
                $linklist .= "&filter_subscr_lastname={$filter_subscr_lastname}";
                $linklist .= "&filter_subscr_email={$filter_subscr_email}";
                $pagenav = new XoopsPageNav($subsrFilterCount, $limit, $start, 'start', $linklist);
                $pagenav = $pagenav->renderNav(4);
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('subscrs_pagenav', $pagenav);
            //
            xoops_load('XoopsFormLoader');
            $filter_subscr_criteria_select = new XoopsFormSelect(_AM_XNEWSLETTER_LETTER_TITLE, 'filter_subscr_criteria', $filter_subscr_criteria, 1, false);
            $filter_subscr_criteria_select->addOption('CONTAINS', _CONTAINS);
            $filter_subscr_criteria_select->addOption('MATCHES', _MATCHES);
            $filter_subscr_criteria_select->addOption('STARTSWITH', _STARTSWITH);
            $filter_subscr_criteria_select->addOption('ENDSWITH', _ENDSWITH);
            $GLOBALS['xoopsTpl']->assign('filter_subscr_criteria_select', $filter_subscr_criteria_select->render());
            //
            $GLOBALS['xoopsTpl']->assign('filter_subscr_criteria', $filter_subscr_criteria);
            $GLOBALS['xoopsTpl']->assign('filter_subscr_firstname', $filter_subscr_firstname);
            $GLOBALS['xoopsTpl']->assign('filter_subscr_lastname', $filter_subscr_lastname);
            $GLOBALS['xoopsTpl']->assign('filter_subscr_email', $filter_subscr_email);
            //
            $GLOBALS['xoopsTpl']->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
            // fill subscrs array
            foreach ($subscrs as $subscr_id => $subscr) {
                $subscr['subscr_uname'] = XoopsUser::getUnameFromId($subscr['subscr_uid'], 'S');
                $subscr['subscr_created_formatted'] = formatTimestamp($subscr['subscr_created'], $xnewsletter->getConfig('dateformat'));
                $GLOBALS['xoopsTpl']->append('subscrs', $subscr);
            }
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_subscrs_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOSUBSCRS;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'apply_actions':
        $action         = XoopsRequest::getString('actions_action');
        $subscr_ids     = XoopsRequest::getArray('subscr_ids', unserialize(XoopsRequest::getString('serialize_subscr_ids')));
        $subscrCriteria = new Criteria('subscr_id', '(' . implode(',', $subscr_ids) . ')', 'IN');
        switch ($action) {
            case 'delete':
                if (XoopsRequest::getBool('ok', false, 'POST') == true) {
                    // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
                    if ($xnewsletter->getHandler('subscr')->deleteAll($subscrCriteria, true, true)) {
                        redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
                    } else {
                        echo $subscrObj->getHtmlErrors();
                    }
                } else {
                    $subscr_emails = array();
                    foreach ($xnewsletter->getHandler('subscr')->getObjects($subscrCriteria) as $subscrObj) {
                        $subscr_emails[] = $subscrObj->getVar('subscr_email');
                    }
                    // render start here
                    xoops_cp_header();
                    // render confirm form
                    xoops_confirm(
                        array('ok' => true, 'op' => 'apply_actions', 'actions_action' => $action, 'serialize_subscr_ids' => serialize($subscr_ids)),
                        $_SERVER['REQUEST_URI'],
                        sprintf(_AM_XNEWSLETTER_FORMSUREDEL, implode(', ', $subscr_emails))
                    );
                    include_once __DIR__ . '/admin_footer.php';
                }
                break;
            case 'activate':
                // activate subscriber (subscr)
                if ($xnewsletter->getHandler('subscr')->updateAll('subscr_activated', true, $subscrCriteria, true)) {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMACTIVATEOK);
                } else {
                    echo $subscrObj->getHtmlErrors();
                }
                break;
            case 'unactivate':
                // unactivate subscriber (subscr)
                if ($xnewsletter->getHandler('subscr')->updateAll('subscr_activated', false, $subscrCriteria, true)) {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMUNACTIVATEOK);
                } else {
                    echo $subscrObj->getHtmlErrors();
                }
                break;
            default:
                // NOP
                break;
        }
        break;

    case 'new_subscr':
        // render start here
        xoops_cp_header();
        // render submenu
        $subscrAdmin = new ModuleAdmin();
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list_subscrs', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->create();
        $form  = $subscrObj->getFormAdmin();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'save_subscr':
        $subscr_id = XoopsRequest::getInt('subscr_id', 0);
        if (!$GLOBALS["xoopsSecurity"]->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $subscrObj->setVar('subscr_email', $_REQUEST['subscr_email']);
        $subscrObj->setVar('subscr_firstname', $_REQUEST['subscr_firstname']);
        $subscrObj->setVar('subscr_lastname', $_REQUEST['subscr_lastname']);
        $subscrObj->setVar('subscr_uid', $_REQUEST['subscr_uid']);
        $subscrObj->setVar('subscr_sex', $_REQUEST['subscr_sex']);
        $subscrObj->setVar('subscr_submitter', $_REQUEST['subscr_submitter']);
        $subscrObj->setVar('subscr_created', $_REQUEST['subscr_created']);
        $subscrObj->setVar('subscr_ip', $_REQUEST['subscr_ip']);
        $subscrObj->setVar('subscr_actkey', $_REQUEST['subscr_actkey']);
        $subscrObj->setVar('subscr_activated', XoopsRequest::getInt('subscr_activated', 0));
        //
        if (!$xnewsletter->getHandler('subscr')->insert($subscrObj)) {
            // render start here
            xoops_cp_header();
            // render form
            echo $subscrObj->getHtmlErrors();
            $form = $subscrObj->getFormAdmin();
            $form->display();
            include_once __DIR__ . '/admin_footer.php';
            exit();
        }
        //
        $cat_ids = XoopsRequest::getArray('cat_ids', array());
        $subscr_id = $subscrObj->getVar('subscr_id');
        $catObjs = $xnewsletter->getHandler('cat')->getAll();
        foreach ($catObjs as $cat_id => $catObj) {
            $catsubscrCriteria = new CriteriaCompo();
            $catsubscrCriteria->add(new Criteria('catsubscr_catid', $cat_id));
            $catsubscrCriteria->add(new Criteria('catsubscr_subscrid', $subscr_id));
            $catsubscrCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
            if (in_array($cat_id, $cat_ids)) {
                // checked
                switch($catsubscrCount) {
                    case 0:
                        // create catsubscr
                        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
                        $catsubscrObj->setVar('catsubscr_catid', $cat_id);
                        $catsubscrObj->setVar('catsubscr_subscrid', $subscr_id);
                        $catsubscrObj->setVar('catsubscr_submitter', $_REQUEST['subscr_uid']);
                        $catsubscrObj->setVar('catsubscr_created', $_REQUEST['subscr_created']);
                        $xnewsletter->getHandler('catsubscr')->insert($catsubscrObj);
                        break;
                    case 1:
                        // NOP
                        break;
                    default:
                        // delete all catsubscrs
                        $xnewsletter->getHandler('catsubscr')->deleteAll($catsubscrCriteria);
                        // create catsubscr
                        $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
                        $catsubscrObj->setVar('catsubscr_catid', $cat_id);
                        $catsubscrObj->setVar('catsubscr_subscrid', $subscr_id);
                        $catsubscrObj->setVar('catsubscr_submitter', $_REQUEST['subscr_uid']);
                        $catsubscrObj->setVar('catsubscr_created', $_REQUEST['subscr_created']);
                        $xnewsletter->getHandler('catsubscr')->insert($catsubscrObj);
                        break;
                }
            } else {
                // not checked
                switch($catsubscrCount) {
                    case 0:
                        // NOP
                        break;
                    case 1:
                        // delete catsubscr
                        $xnewsletter->getHandler('catsubscr')->deleteAll($catsubscrCriteria);
                        break;
                    default:
                        // delete all catsubscrs
                        $xnewsletter->getHandler('catsubscr')->deleteAll($catsubscrCriteria);
                        break;
                }
            }
        }
        redirect_header('?op=list_subscrs', 3, _AM_XNEWSLETTER_FORMOK);
        break;

    case 'edit_subscr':
        $subscr_id = XoopsRequest::getInt('subscr_id', 0);
        // render start here
        xoops_cp_header();
        // render submenu
        $subscrAdmin = new ModuleAdmin();
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list_subscrs', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $form      = $subscrObj->getFormAdmin();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_subscr':
        $subscr_id = XoopsRequest::getInt('subscr_id', 0);
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
            if ($xnewsletter->getHandler('subscr')->delete($subscrObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $subscrObj->getHtmlErrors();
            }
        } else {
            // render start here
            xoops_cp_header();
            // render confirm form
            xoops_confirm(
                array('ok' => true, 'subscr_id' => $_REQUEST['subscr_id'], 'op' => 'delete_subscr'),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $subscrObj->getVar('subscr_email'))
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;
}
