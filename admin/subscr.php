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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_subscribers.tpl';

// We recovered the value of the argument op in the URL$
$op       = Request::getString('op', 'list');
$subscrId = Request::getInt('subscr_id', 0);

$filter_subscr           = Request::getString('filter_subscr', '=');
$filter_subscr_firstname = Request::getString('filter_subscr_firstname', '');
$filter_subscr_lastname  = Request::getString('filter_subscr_lastname', '');
$filter_subscr_email     = Request::getString('filter_subscr_email', '');

if ('apply_filter' === $op) {
    if ('LIKE' === $filter_subscr && '' == !$filter_subscr_firstname) {
        $filter_subscr_firstname = "%{$filter_subscr_firstname}%";
    }
    if ('LIKE' === $filter_subscr && '' == !$filter_subscr_lastname) {
        $filter_subscr_lastname = "%{$filter_subscr_lastname}%";
    }
    if ('LIKE' === $filter_subscr && '' == !$filter_subscr_email) {
        $filter_subscr_email = "%{$filter_subscr_email}%";
    }
    if ('' == $filter_subscr_firstname && '' == $filter_subscr_lastname && '' == $filter_subscr_email) {
        $op = 'list';
    }
}

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);
$GLOBALS['xoopsTpl']->assign('op', $op);
$GLOBALS['xoopsTpl']->assign('filter_subscr', $filter_subscr);
$GLOBALS['xoopsTpl']->assign('filter_subscr_firstname', $filter_subscr_firstname);
$GLOBALS['xoopsTpl']->assign('filter_subscr_lastname', $filter_subscr_lastname);
$GLOBALS['xoopsTpl']->assign('filter_subscr_email', $filter_subscr_email);


switch ($op) {
    case 'show_catsubscr':
        $adminObject->displayNavigation($currentFile);
        $apply_filter = Request::getString('apply_filter', 'list');
        $linklist     = "?op=$apply_filter&filter_subscr={$filter_subscr}";
        $linklist     .= "&filter_subscr_firstname={$filter_subscr_firstname}";
        $linklist     .= "&filter_subscr_lastname={$filter_subscr_lastname}";
        $linklist     .= "&filter_subscr_email={$filter_subscr_email}";
        $adminObject->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, $linklist, 'view_detailed');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $subscrObj = $helper->getHandler('Subscr')->get($subscrId);
        $show_catsubscr['id'] = $subscrObj->getVar('subscr_id');
        $show_catsubscr['email'] = $subscrObj->getVar('subscr_email');
        
        $cats = '';
        $catsubscrCriteria = new \CriteriaCompo();
        $catsubscrCriteria->add(new \Criteria('catsubscr_subscrid', $subscrId));
        $catsubscrCount = $helper->getHandler('Catsubscr')->getCount($catsubscrCriteria);
        if ($catsubscrCount > 0) {
            $catsubscrObjs = $helper->getHandler('Catsubscr')->getAll($catsubscrCriteria);
            foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                $cat_id = $catsubscrObj->getVar('catsubscr_catid');
                $catObj = $helper->getHandler('Cat')->get($cat_id);
                if (is_object($catObj)) {
                    $cats .= $catObj->getVar('cat_name') . '<br>';
                } else {
                    $cats .= "INVALID CATEGORY - ID: $cat_id<br>";
                }
            }
        } else {
            $cats .= _AM_XNEWSLETTER_SUBSCR_NO_CATSUBSCR;
        }
        $show_catsubscr['cats'] = $cats;
        
        $GLOBALS['xoopsTpl']->assign('show_catsubscr', $show_catsubscr);
        break;
    case 'list':
    case 'apply_filter':
    default:
        $start = Request::getInt('start', 0);
        $limit = $helper->getConfig('adminperpage');
        $GLOBALS['xoopsTpl']->assign('start', $start);
        $GLOBALS['xoopsTpl']->assign('limit', $limit);
        
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        if ('apply_filter' === $op) {
            $adminObject->addItemButton(_AM_XNEWSLETTER_SUBSCR_SHOW_ALL, '?op=list', 'view_detailed');
        }
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $subscrCriteria = new \CriteriaCompo();
        if ('apply_filter' === $op) {
            if ('' != $filter_subscr_firstname) {
                $subscrCriteria->add(new \Criteria('subscr_firstname', $filter_subscr_firstname, $filter_subscr));
            }
            if ('' != $filter_subscr_lastname) {
                $subscrCriteria->add(new \Criteria('subscr_lastname', $filter_subscr_lastname, $filter_subscr));
            }
            if ('' != $filter_subscr_email) {
                $subscrCriteria->add(new \Criteria('subscr_email', $filter_subscr_email, $filter_subscr));
            }
        }
        $subscrCriteria->setSort('subscr_id');
        $subscrCriteria->setOrder('DESC');
        $subscrCount = $helper->getHandler('Subscr')->getCount($subscrCriteria);
        
        $subscrCriteria->setStart($start);
        $subscrCriteria->setLimit($limit);
        $subscrAll = $helper->getHandler('Subscr')->getAll($subscrCriteria);
        if ($subscrCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $linklist = "op={$op}";
            $linklist .= "&filter_subscr={$filter_subscr}";
            $linklist .= "&filter_subscr_firstname={$filter_subscr_firstname}";
            $linklist .= "&filter_subscr_lastname={$filter_subscr_lastname}";
            $linklist .= "&filter_subscr_email={$filter_subscr_email}";
            $pagenav  = new \XoopsPageNav($subscrCount, $limit, $start, 'start', $linklist);
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }
        if ('LIKE' === $filter_subscr) {
            //clean up var for refill form
            $filter_subscr_firstname = str_replace('%', '', $filter_subscr_firstname);
            $filter_subscr_lastname  = str_replace('%', '', $filter_subscr_lastname);
            $filter_subscr_email     = str_replace('%', '', $filter_subscr_email);
        }

        if ($subscrCount > 0) {
            $GLOBALS['xoopsTpl']->assign('subscribers_count', $subscrCount);
            
            $form_filter = "<form id='form_filter' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_filter'>";
            $form_filter .= "<tr class='odd'>";
            $form_filter .= "    <td class='center'>&nbsp;</td>";
            $form_filter .= "    <td colspan='2'>" . _SEARCH . ':&nbsp;&nbsp;';
            $form_filter .= "    <select id='filter_subscr' title='" . _SEARCH . "' name='filter_subscr' size='1'>";
            $form_filter .= "        <option value='='" . (('=' === $filter_subscr) ? ' selected' : '') . '>' . _AM_XNEWSLETTER_SEARCH_EQUAL . '</option>';
            $form_filter .= "        <option value='LIKE'" . (('LIKE' === $filter_subscr) ? ' selected' : '') . '>' . _AM_XNEWSLETTER_SEARCH_CONTAINS . '</option>';
            $form_filter .= '    </select>';
            $form_filter .= '    </td>';
            $form_filter .= "    <td><input id='filter_subscr_firstname' type='text' value='{$filter_subscr_firstname}' maxlength='50' size='15' title='' name='filter_subscr_firstname'></td>";
            $form_filter .= "    <td><input id='filter_subscr_lastname' type='text' value='{$filter_subscr_lastname}' maxlength='50' size='15' title='' name='filter_subscr_lastname'></td>";
            $form_filter .= "    <td><input id='filter_subscr_email' type='text' value='{$filter_subscr_email}' maxlength='255' size='40' title='' name='filter_subscr_email'></td>";
            $form_filter .= '    <td>&nbsp;</td>';
            $form_filter .= '    <td>&nbsp;</td>';
            $form_filter .= "    <td class='center'><input id='filter_submit' class='formButton' type='submit' title='" . _SEARCH . "' value='" . _SEARCH . "' name='filter_submit'></td>";
            $form_filter .= '</tr>';
            $form_filter .= "<input id='filter_op' type='hidden' value='apply_filter' name='op'>";
            $form_filter .= '</form>';
            $GLOBALS['xoopsTpl']->assign('form_filter', $form_filter);
            
            foreach ($subscrAll as $subscr_id => $subscrObj) {
                $subscr = $subscrObj->getValuesSubscr();
                $subscr['username'] = '-';
                if ($subscrObj->getVar('subscr_uid') > 0) {
                    $subscr['username'] = \XoopsUser::getUnameFromId($subscrObj->getVar('subscr_uid'), 'S');
                }
                if (0 == $subscrObj->getVar('subscr_activated')) {
                    $subscr['activated_img'] = '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_failed.png" alt="' . _AM_XNEWSLETTER_SUBSCRWAIT . '" title="' . _AM_XNEWSLETTER_SUBSCRWAIT . '"> ';
                } else {
                    $subscr['activated_img'] = '<img src="' . XNEWSLETTER_ICONS_URL . '/xn_ok.png" alt="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '" title="' . _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED . '"> ';
                }
                $subscr['created_ip'] = formatTimestamp($subscrObj->getVar('subscr_created'), $helper->getConfig('dateformat')) . ' [' . $subscrObj->getVar('subscr_ip') . ']';

                $GLOBALS['xoopsTpl']->append('subscribers_list', $subscr);
                unset($subscr);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_SUBSCR);
        }
        break;
    case 'apply_actions':
        $action         = Request::getString('actions_action');
        $subscr_ids     = Request::getArray('subscr_ids', unserialize(Request::getString('serialize_subscr_ids')));
        $subscrCriteria = new \Criteria('subscr_id', '(' . implode(',', $subscr_ids) . ')', 'IN');
        switch ($action) {
            case 'delete':
                if (true === Request::getBool('ok', false, 'POST')) {
                    // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
                    if ($helper->getHandler('Subscr')->deleteAll($subscrCriteria)) {
                        redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
                    } else {
                        $GLOBALS['xoopsTpl']->assign('error', $subscrObj->getHtmlErrors());
                    }
                } else {
                    $subscr_emails = [];
                    foreach ($helper->getHandler('Subscr')->getObjects($subscrCriteria) as $subscrObj) {
                        $subscr_emails[] = $subscrObj->getVar('subscr_email');
                    }
                    xoops_confirm([
                                      'ok'                   => true,
                                      'op'                   => 'apply_actions',
                                      'actions_action'       => $action,
                                      'serialize_subscr_ids' => serialize($subscr_ids),
                                  ], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, implode(', ', $subscr_emails)));
                }
                break;
            case 'activate':
                // activate subscriber (subscr)
                if ($helper->getHandler('Subscr')->updateAll('subscr_activated', true, $subscrCriteria, true)) {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMACTIVATEOK);
                } else {
                    $GLOBALS['xoopsTpl']->assign('error', $subscrObj->getHtmlErrors());
                }
                break;
            case 'unactivate':
                // unactivate subscriber (subscr)
                if ($helper->getHandler('Subscr')->updateAll('subscr_activated', 0, $subscrCriteria, true)) {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMUNACTIVATEOK);
                } else {
                    $GLOBALS['xoopsTpl']->assign('error', $subscrObj->getHtmlErrors());
                }
                break;
            default:
                // NOP
                break;
        }
        break;
    case 'new_subscr':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $subscrObj = $helper->getHandler('Subscr')->create();
        $form      = $subscrObj->getFormAdmin();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_subscr':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $start = Request::getInt('start', 0);
        
        $subscrObj = $helper->getHandler('Subscr')->get($subscrId);
        $subscrObj->setVar('subscr_email',     Request::getString('subscr_email', ''));
        $subscrObj->setVar('subscr_firstname', Request::getString('subscr_firstname', ''));
        $subscrObj->setVar('subscr_lastname',  Request::getString('subscr_lastname', ''));
        $subscrObj->setVar('subscr_uid',       Request::getInt('subscr_uid', 0));
        $subscrObj->setVar('subscr_sex',       Request::getString('subscr_sex', ''));
        $subscrObj->setVar('subscr_submitter', Request::getInt('subscr_submitter', 0));
        $subscrObj->setVar('subscr_created',   Request::getInt('subscr_created', 0));
        $subscrObj->setVar('subscr_ip',        Request::getString('subscr_ip', ''));
        $subscrActkey = Request::getString('subscr_actkey', '');
        if ('' === $subscrActkey) {
            $subscrActkey = xoops_makepass();
        }
        $subscrObj->setVar('subscr_actkey',    Request::getString('subscr_actkey', ''));
        $subscrObj->setVar('subscr_activated', Request::getInt('subscr_activated', 0));

        if ($helper->getHandler('Subscr')->insert($subscrObj)) {
            redirect_header('?op=list&amp;start=' . $start, 3, _AM_XNEWSLETTER_FORMOK);
        }
        $GLOBALS['xoopsTpl']->assign('error', $subscrObj->getHtmlErrors());
        $form = $subscrObj->getFormAdmin();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_subscr':
        $start = Request::getInt('start', 0);
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWSUBSCR, '?op=new_subscr', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_SUBSCRLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $subscrObj = $helper->getHandler('Subscr')->get($subscrId);
        $subscrObj->setVar('start', $start);
        $form      = $subscrObj->getFormAdmin(false, true);
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_subscr':
        $start = Request::getInt('start', 0);
        $GLOBALS['xoopsTpl']->assign('start', $start);
        $GLOBALS['xoopsTpl']->assign('limit', $limit);
        
        $subscrObj = $helper->getHandler('Subscr')->get($subscrId);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
            if ($helper->getHandler('Subscr')->delete($subscrObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $subscrObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'subscr_id' => $subscrId, 'op' => 'delete_subscr', 'start' => $start], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $subscrObj->getVar('subscr_email')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
