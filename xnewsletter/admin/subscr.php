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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include 'admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op                      = xnewsletterRequest::getString('op', 'list');
$subscr_id               = xnewsletterRequest::getInt('subscr_id', 0);

$filter_subscr           = xnewsletterRequest::getString('filter_subscr', '=');
$filter_subscr_firstname = xnewsletterRequest::getString('filter_subscr_firstname', '');
$filter_subscr_lastname  = xnewsletterRequest::getString('filter_subscr_lastname', '');
$filter_subscr_email     = xnewsletterRequest::getString('filter_subscr_email', '');

if ($op == 'apply_filter') {
    if ($filter_subscr == 'LIKE' && !$filter_subscr_firstname=='') $filter_subscr_firstname = "%{$filter_subscr_firstname}%";
    if ($filter_subscr == 'LIKE' && !$filter_subscr_lastname=='') $filter_subscr_lastname = "%{$filter_subscr_lastname}%";
    if ($filter_subscr == 'LIKE' && !$filter_subscr_email=='') $filter_subscr_email = "%{$filter_subscr_email}%";
    if ($filter_subscr_firstname == '' && $filter_subscr_lastname == '' && $filter_subscr_email == '') $op = 'list';
}

$subscrAdmin = new ModuleAdmin();
switch ($op) {
    case 'show_catsubscr':
        echo $subscrAdmin->addNavigation($currentFile);
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
        break;



    case 'list':
    case 'apply_filter':
    default:
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        if ($op == 'apply_filter') $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, '?op=list', 'view_detailed');
        echo $subscrAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $subscrCriteria = new CriteriaCompo();

        if ($op == 'apply_filter') {
            if ($filter_subscr_firstname != '')
                $subscrCriteria->add(new Criteria('subscr_firstname', $filter_subscr_firstname,$filter_subscr));
            if ($filter_subscr_lastname != '')
                $subscrCriteria->add(new Criteria('subscr_lastname', $filter_subscr_lastname,$filter_subscr));
            if ($filter_subscr_email != '')
                $subscrCriteria->add(new Criteria('subscr_email', $filter_subscr_email,$filter_subscr));
        }
        $subscrCriteria->setSort('subscr_id');
        $subscrCriteria->setOrder('DESC');
        $subscrCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
        $start = xnewsletterRequest::getInt('start', 0);
        $subscrCriteria->setStart($start);
        $subscrCriteria->setLimit($limit);
        $subscrObjs = $xnewsletter->getHandler('subscr')->getAll($subscrCriteria);
        if ($subscrCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $linklist = "op={$op}";
            $linklist .= "&filter_subscr={$filter_subscr}";
            $linklist .= "&filter_subscr_firstname={$filter_subscr_firstname}";
            $linklist .= "&filter_subscr_lastname={$filter_subscr_lastname}";
            $linklist .= "&filter_subscr_email={$filter_subscr_email}";
            $pagenav = new XoopsPageNav($subscrCount, $limit, $start, 'start', $linklist);
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }
        if ($filter_subscr == 'LIKE') {
            //clean up var for refill form
            $filter_subscr_firstname = str_replace('%', '', $filter_subscr_firstname);
            $filter_subscr_lastname = str_replace('%', '', $filter_subscr_lastname);
            $filter_subscr_email = str_replace('%', '', $filter_subscr_email);
        }

        // View Table
        echo "<table class='outer width100' cellspacing='1'>";
        echo "<tr>";
        echo "    <th class='center'><input type='checkbox' title='" . _ALL . "'onClick='toggle(this);'></th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_ID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_SEX . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_FIRSTNAME . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_LASTNAME . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_UID . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_SUBSCR_CREATED . "</th>";
        echo "    <th>" . _AM_XNEWSLETTER_FORMACTION . "</th>";
        echo "</tr>";

        if ($subscrCount > 0) {
            $class = 'odd';
            echo "<form id='form_filter' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_filter'>";
            $inputstyle = '';//style='border: 1px solid #000000;";
            echo "<tr class='{$class}'>";
            $class = ($class == 'even') ? 'odd' : 'even';
            echo "    <td class='center'>&nbsp;</td>";
            echo "    <td colspan='2'>" . _SEARCH . ":&nbsp;&nbsp;";
            echo "    <select id='filter_subscr' title='" . _SEARCH . "' name='filter_subscr' size='1'>";
            echo "        <option value='='" . (($filter_subscr == "=") ? " selected='selected'" : "") . ">" . _AM_XNEWSLETTER_SEARCH_EQUAL . "</option>";
            echo "        <option value='LIKE'" . (($filter_subscr == "LIKE") ? " selected='selected'" : "") . ">" . _AM_XNEWSLETTER_SEARCH_CONTAINS . "</option>";
            echo "    </select>";
            echo "    </td>";
            echo "    <td><input {$inputstyle} id='filter_subscr_firstname' type='text' value='{$filter_subscr_firstname}' maxlength='50' size='15' title='' name='filter_subscr_firstname'></td>";
            echo "    <td><input {$inputstyle} id='filter_subscr_lastname' type='text' value='{$filter_subscr_lastname}' maxlength='50' size='15' title='' name='filter_subscr_lastname'></td>";
            echo "    <td><input {$inputstyle} id='filter_subscr_email' type='text' value='{$filter_subscr_email}' maxlength='255' size='40' title='' name='filter_subscr_email'></td>";
            echo "    <td>&nbsp;</td>";
            echo "    <td>&nbsp;</td>";
            echo "    <td><input id='filter_submit' class='formButton' type='submit' title='" . _SEARCH . "' value='" . _SEARCH . "' name='filter_submit'></td>";
            echo "</tr>";
            echo "<input id='filter_op' type='hidden' value='apply_filter' name='op'>";
            echo "</form>";

            echo "<script language='JavaScript'>
            function toggle(source) {
                checkboxes = document.getElementsByName('subscr_ids[]');
                for(var i=0, n=checkboxes.length;i<n;i++) {
                    checkboxes[i].checked = source.checked;
                }
            }
            </script>";
            echo "<script language='JavaScript'>
            function check(source) {
                checkboxes = document.getElementsByName('subscr_ids[]');
                for(var i=0, n=checkboxes.length;i<n;i++) {
                    if (checkboxes[i].checked) return true;
                }
                return false;
            }
            </script>";

            echo "<form id='form_action' onsubmit='return check(this);' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_action'>";
            foreach ($subscrObjs as $subscr_id => $subscrObj) {
                echo "<tr class='{$class}'>";
                $class = ($class == 'even') ? 'odd' : 'even';
                echo "    <td class='center'><input type='checkbox' name='subscr_ids[]' value='{$subscr_id}'></td>";
                echo "    <td>" . $subscr_id . "</td>";
                echo "    <td>" . $subscrObj->getVar('subscr_sex') . "&nbsp;</td>";
                echo "    <td>" . $subscrObj->getVar('subscr_firstname') . "&nbsp;</td>";
                echo "    <td>" . $subscrObj->getVar('subscr_lastname') . "&nbsp;</td>";
                echo "    <td>" . $subscrObj->getVar('subscr_email') . "&nbsp;</td>";
                echo "    <td>";
                if ($subscrObj->getVar('subscr_uid') > 0) {
                    echo XoopsUser::getUnameFromId($subscrObj->getVar('subscr_uid'), 'S');
                } else {
                    echo "-";
                }
                echo "    </td>";
                echo "    <td>";
                if ($subscrObj->getVar('subscr_activated') == 0) {
                    echo '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_failed.png" alt="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" title="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" /> ';
                } else {
                    echo '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_ok.png" alt="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" title="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" /> ';
                }
                echo formatTimestamp($subscrObj->getVar("subscr_created"), $xnewsletter->getConfig('dateformat')) . " [" . $subscrObj->getVar('subscr_ip') . "]";
                echo "    </td>";

                echo "    <td nowrap='nowrap'>";
                echo "    <a href='?op=edit_subscr&subscr_id={$subscr_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>";
                echo "    &nbsp;";
                echo "    <a href='?op=delete_subscr&subscr_id={$subscr_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>";
                echo "    &nbsp;";
                echo "    <a href='?op=show_catsubscr&subscr_id={$subscr_id}&filter_subscr={$filter_subscr}&filter_subscr_firstname={$filter_subscr_firstname}&filter_subscr_lastname={$filter_subscr_lastname}&filter_subscr_email={$filter_subscr_email}&apply_filter={$op}'>";
                echo "    <img src=" . XNEWSLETTER_ICONS_URL . "/xn_details.png alt='" . _AM_XNEWSLETTER_DETAILS . "' title='" . _AM_XNEWSLETTER_DETAILS . "' />";
                echo "    </a>";
                echo "    </td>";
                echo "</tr>";

//                $filter_subscr           = xnewsletterRequest::getString('filter_subscr', '=');
//                $filter_subscr_firstname = xnewsletterRequest::getString('filter_subscr_firstname', '');
//                $filter_subscr_lastname  = xnewsletterRequest::getString('filter_subscr_lastname', '');
//                $filter_subscr_email     = xnewsletterRequest::getString('filter_subscr_email', '');
            }
            echo "<tr>";
            echo "    <td colspan='9'>";
            echo "        <select id='actions_action' name='actions_action' size='1'>";
            echo "            <option value='delete'>" . _DELETE . "</option>";
            echo "            <option value='activate'>" . _AM_XNEWSLETTER_ACTIONS_ACTIVATE . "</option>";
            echo "            <option value='unactivate'>" . _AM_XNEWSLETTER_ACTIONS_UNACTIVATE . "</option>";
            echo "        </select>";
            echo "        <input id='actions_submit' class='formButton' type='submit' title='" . _AM_XNEWSLETTER_ACTIONS_EXEC . "' value='" . _AM_XNEWSLETTER_ACTIONS_EXEC . "' name='actions_submit'>";
            echo "    </td>";
            echo "</tr>";
            echo "<input id='actions_op' type='hidden' value='apply_actions' name='op'>";
            echo "</form>";
        }
        echo "</table>";
        echo "<br />";
        echo "<div>" . $pagenav . "</div>";
        echo "<br />";
        break;



    case 'apply_actions':
        $action = xnewsletterRequest::getString('actions_action');
        $subscr_ids = xnewsletterRequest::getArray('subscr_ids', unserialize(xnewsletterRequest::getString('serialize_subscr_ids')));
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
                    foreach ($xnewsletter->getHandler('subscr')->getObjects($subscrCriteria) as $subscrObj)
                        $subscr_emails[] = $subscrObj->getVar('subscr_email');
                    xoops_confirm(array('ok' => true, 'op' => 'apply_actions', 'actions_action' => $action, 'serialize_subscr_ids' => serialize($subscr_ids)), $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, implode(', ', $subscr_emails)));
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
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->create();
        $form = $subscrObj->getFormAdmin();
        $form->display();
        break;



    case 'save_subscr':
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
        //
        echo $subscrObj->getHtmlErrors();
        $form = $subscrObj->getFormAdmin();
        $form->display();
        break;



    case 'edit_subscr':
        echo $subscrAdmin->addNavigation($currentFile);
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        $subscrAdmin->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        echo $subscrAdmin->renderButton();
        //
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $form = $subscrObj->getFormAdmin();
        $form->display();
        break;



    case 'delete_subscr':
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        if (xnewsletterRequest::getBool('ok', false, 'POST') == true) {
            if ( !$GLOBALS['xoopsSecurity']->check() ) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
            if ($xnewsletter->getHandler('subscr')->delete($subscrObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $subscrObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array('ok' => true, 'subscr_id' => $_REQUEST['subscr_id'], 'op' => 'delete_subscr'), $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $subscrObj->getVar('subscr_email')));
        }
        break;
}
include 'admin_footer.php';
