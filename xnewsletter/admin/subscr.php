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
$op        = xnewsletterRequest::getString('op', 'list');

$subscrAdmin = new ModuleAdmin();
switch ($op) {
    case 'show_catsubscr':
        $subscr_id = xnewsletterRequest::getInt('subscr_id', 0);
        // render start here
        xoops_cp_header();
        // render submenu
        echo $subscrAdmin->addNavigation($currentFile);
        //
        $apply_filter = xnewsletterRequest::getString('apply_filter', 'list');
        $linklist = "?op=$apply_filter&filter_subscr={$filter_subscr}";
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
    case 'apply_filter':
    default:
        // render start here
        xoops_cp_header();
        // render submenu
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        if ($op == 'apply_filter') {
            $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, '?op=list', 'view_detailed');
        }
        echo $subscrAdmin->renderButton();
        //
        $subsrCount = $xnewsletter->getHandler('subscr')->getCount();
        if ($subsrCount > 0) {
            // get filter parameters
            $filter_subscr           = xnewsletterRequest::getString('filter_subscr', '=');
            $filter_subscr_firstname = xnewsletterRequest::getString('filter_subscr_firstname', '');
            $filter_subscr_lastname  = xnewsletterRequest::getString('filter_subscr_lastname', '');
            $filter_subscr_email     = xnewsletterRequest::getString('filter_subscr_email', '');
            if ($op == 'apply_filter') {
                if ($filter_subscr == 'LIKE' && !$filter_subscr_firstname == '') {
                    $filter_subscr_firstname = "%{$filter_subscr_firstname}%";
                }
                if ($filter_subscr == 'LIKE' && !$filter_subscr_lastname == '') {
                    $filter_subscr_lastname = "%{$filter_subscr_lastname}%";
                }
                if ($filter_subscr == 'LIKE' && !$filter_subscr_email == '') {
                    $filter_subscr_email = "%{$filter_subscr_email}%";
                }
                if ($filter_subscr_firstname == '' && $filter_subscr_lastname == '' && $filter_subscr_email == '') {
                    $op = 'list';
                }
            }
            // get filtered subscrs criteria
            $subscrCriteria = new CriteriaCompo();
            if ($op == 'apply_filter') {
                if ($filter_subscr_firstname != '') {
                    $subscrCriteria->add(new Criteria('subscr_firstname', $filter_subscr_firstname, $filter_subscr));
                }
                if ($filter_subscr_lastname != '') {
                    $subscrCriteria->add(new Criteria('subscr_lastname', $filter_subscr_lastname, $filter_subscr));
                }
                if ($filter_subscr_email != '') {
                    $subscrCriteria->add(new Criteria('subscr_email', $filter_subscr_email, $filter_subscr));
                }
            }
            $subscrCriteria->setSort('subscr_id');
            $subscrCriteria->setOrder('DESC');
            $subsrFilterCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
            //
            $start = xnewsletterRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $subscrCriteria->setStart($start);
            $subscrCriteria->setLimit($limit);
            //
            $subscrObjs = $xnewsletter->getHandler('subscr')->getAll($subscrCriteria);
            $subscrs = $xnewsletter->getHandler('subscr')->getObjects($subscrCriteria, true, false); // as array
            if ($subsrFilterCount > $limit) {
                include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
                $linklist = "op={$op}";
                $linklist .= "&filter_subscr={$filter_subscr}";
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
            if ($filter_subscr == 'LIKE') {
                //clean up var for refill form
                $filter_subscr_firstname = str_replace('%', '', $filter_subscr_firstname);
                $filter_subscr_lastname  = str_replace('%', '', $filter_subscr_lastname);
                $filter_subscr_email     = str_replace('%', '', $filter_subscr_email);
            }
            $GLOBALS['xoopsTpl']->assign('filter_subscr', $filter_subscr);
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
        $action         = xnewsletterRequest::getString('actions_action');
        $subscr_ids     = xnewsletterRequest::getArray('subscr_ids', unserialize(xnewsletterRequest::getString('serialize_subscr_ids')));
        $subscrCriteria = new Criteria('subscr_id', '(' . implode(',', $subscr_ids) . ')', 'IN');
        switch ($action) {
            case 'delete':
                if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
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
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->create();
        $form      = $subscrObj->getFormAdmin();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'save_subscr':
        $subscr_id = xnewsletterRequest::getInt('subscr_id', 0);
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
        $subscrObj->setVar('subscr_activated', xnewsletterRequest::getInt('subscr_activated', 0));
        //
        if ($xnewsletter->getHandler('subscr')->insert($subscrObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }
        // render start here
        xoops_cp_header();
        // render form
        echo $subscrObj->getHtmlErrors();
        $form = $subscrObj->getFormAdmin();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'edit_subscr':
        $subscr_id = xnewsletterRequest::getInt('subscr_id', 0);
        // render start here
        xoops_cp_header();
        // render submenu
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $form      = $subscrObj->getFormAdmin();
        $form->display();
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'delete_subscr':
        $subscr_id = xnewsletterRequest::getInt('subscr_id', 0);
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
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
