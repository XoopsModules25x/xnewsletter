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

// We recovered the value of the argument op in the URL$
$op = XoopsRequest::getString('op', 'list_protocols');
switch ($op) {
    case 'list':
    case 'list_protocols':
    default:
        $apply_filter = XoopsRequest::getBool('apply_filter', false);
        // render start here
        xoops_cp_header();
        // render submenu
        $protocolAdmin = new ModuleAdmin();
        echo $protocolAdmin->addNavigation($currentFile);
        if ($apply_filter == true) {
            $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOL_SHOW_ALL, '?op=list_protocols', 'view_detailed');
        }
        $protocolCount = $xnewsletter->getHandler('protocol')->getCount();
        if ($protocolCount > 0) {
            $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOL_DELETE_ALL, '?op=delete_protocols', 'delete');
        }
        echo $protocolAdmin->renderButton();
        //
        $protocolCount = $xnewsletter->getHandler('protocol')->getCount();
        $GLOBALS['xoopsTpl']->assign('protocolCount', $protocolCount);
        if ($protocolCount > 0) {
            $protocolCriteria = new CriteriaCompo();
            // get filter parameters
            $filter_protocol_letter_ids = XoopsRequest::getArray('filter_protocol_letter_ids', array());
            //
            $filter_protocol_success = XoopsRequest::getInt('filter_protocol_success', 2); // all
            //
            if (isset($_REQUEST['filter_protocol_created_from']['date'])) {
                $dateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $_REQUEST['filter_protocol_created_from']['date']);
                $dateTimeObj->setTime(0, 0, 0);
                $filter_protocol_created_from = (int) ($dateTimeObj->getTimestamp() + $_REQUEST['filter_protocol_created_from']['time']);
                unset($dateTimeObj);
            } else {
                $filter_protocol_created_from = 3600;
            }
            if (isset($_REQUEST['filter_protocol_created_to']['date'])) {
                $dateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $_REQUEST['filter_protocol_created_to']['date']);
                $dateTimeObj->setTime(0, 0, 0);
                $filter_protocol_created_to = (int) ($dateTimeObj->getTimestamp() + $_REQUEST['filter_protocol_created_to']['time']);
                unset($dateTimeObj);
            } else {
                $filter_protocol_created_to = time();
            }
            if ($apply_filter == true) {
                // apply filter
                if (count($filter_protocol_letter_ids) > 0) {
                    $protocolCriteria->add(new Criteria('protocol_letter_id', '(' . implode(',', $filter_protocol_letter_ids) . ')', 'IN'));
                }
                //
                if ($filter_protocol_success == 0 or $filter_protocol_success == 1) {
                    $protocolCriteria->add(new Criteria('protocol_success', $filter_protocol_success));
                }
                //
                if ($filter_protocol_created_from != 0) {
                    $protocolCriteria->add(new Criteria('protocol_created', $filter_protocol_created_from, '>='));
                }
                if ($filter_protocol_created_to != 0) {
                    $protocolCriteria->add(new Criteria('protocol_created', $filter_protocol_created_to, '<='));
                }
            }
            $GLOBALS['xoopsTpl']->assign('apply_filter', $apply_filter);
            $protocolFilterCount = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);
            $GLOBALS['xoopsTpl']->assign('protocolFilterCount', $protocolFilterCount);
            //
            $protocolCriteria->setSort('protocol_id');
            $protocolCriteria->setOrder('DESC');
            //
            $start = XoopsRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $protocolCriteria->setStart($start);
            $protocolCriteria->setLimit($limit);
            //
            $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
            $protocols = $xnewsletter->getHandler('protocol')->getObjects($protocolCriteria, true, false); // as array
            //
            $letterCriteria = new CriteriaCompo();
            $letterCriteria->setSort('letter_created');
            $letterCriteria->setOrder('DESC');
            //$letterObjs = $xnewsletter->getHandler('letter')->getAll($letterCriteria);
            $letters = $xnewsletter->getHandler('letter')->getObjects($letterCriteria, true, false);
            //
            if ($protocolFilterCount > $limit) {
                xoops_load('xoopspagenav');
                $linklist = "op={$op}";
                foreach ($filter_protocol_letter_ids as $filter_protocol_letter_id) {
                    $linklist .= "&filter_protocol_letter_ids[]={$filter_protocol_letter_id}";
                }
                $linklist .= "&filter_protocol_success={$filter_protocol_success}";
                $linklist .= "&filter_protocol_created_from[date]={$_REQUEST['filter_protocol_created_from']['date']}";
                $linklist .= "&filter_protocol_created_from[time]={$_REQUEST['filter_protocol_created_from']['time']}";
                $linklist .= "&filter_protocol_created_to[date]={$_REQUEST['filter_protocol_created_to']['date']}";
                $linklist .= "&filter_protocol_created_to[time]={$_REQUEST['filter_protocol_created_to']['time']}";
                $pagenav = new XoopsPageNav($protocolFilterCount, $limit, $start, 'start', $linklist);
                $pagenav = $pagenav->renderNav(4);
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('protocols_pagenav', $pagenav);
            //
            $filter_protocol_letter_ids_select = new XoopsFormSelect(_AM_XNEWSLETTER_LETTER_TITLE, 'filter_protocol_letter_ids', $filter_protocol_letter_ids, 1, true);
            $filter_protocol_letter_ids_select->addOption(0, _AM_XNEWSLETTER_PROTOCOL_MISC);
            foreach ($letters as $letter) {
                $filter_protocol_letter_ids_select->addOption($letter['letter_id'], $letter['letter_title']);
            }
            $GLOBALS['xoopsTpl']->assign('filter_protocol_letter_ids_select', $filter_protocol_letter_ids_select->render());
            //
            $filter_protocol_success_radio = new XoopsFormRadio(_AM_XNEWSLETTER_PROTOCOL_STATUS, 'filter_protocol_success', $filter_protocol_success, '&nbsp;');
            $filter_protocol_success_radio->addOption(0, _AM_XNEWSLETTER_FAILED);
            $filter_protocol_success_radio->addOption(1, _AM_XNEWSLETTER_OK);
            $filter_protocol_success_radio->addOption(2, _ALL);
            $GLOBALS['xoopsTpl']->assign('filter_protocol_success_radio', $filter_protocol_success_radio->render());
            //
            $filter_protocol_created_from_datetime = new XoopsFormDateTime(_AM_XNEWSLETTER_PROTOCOL_CREATED_FILTER_FROM, 'filter_protocol_created_from', 15, $filter_protocol_created_from, true);
            $GLOBALS['xoopsTpl']->assign('filter_protocol_created_from_datetime', $filter_protocol_created_from_datetime->render());
            //
            $filter_protocol_created_to_datetime = new XoopsFormDateTime(_AM_XNEWSLETTER_PROTOCOL_CREATED_FILTER_TO, 'filter_protocol_created_to', 15, $filter_protocol_created_to, true);
            $GLOBALS['xoopsTpl']->assign('filter_protocol_created_to_datetime', $filter_protocol_created_to_datetime->render());
            //
            $GLOBALS['xoopsTpl']->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
            // fill protocols array
            foreach ($protocols as $protocol_id => $protocol) {
                $protocol['protocol_created_formatted'] = formatTimestamp($protocol['protocol_created'], $xnewsletter->getConfig('dateformat'));
                $protocol['protocol_submitter_uname'] = XoopsUser::getUnameFromId($protocol['protocol_submitter'], 'S');

                if (isset($letters[$protocol['protocol_letter_id']])) {
                    $protocol['protocol_letter_title'] = $letters[$protocol['protocol_letter_id']]['letter_title'];
                } else {
                    $protocol['protocol_letter_title'] = _AM_XNEWSLETTER_PROTOCOL_MISC;
                }
                if ($subscrObj = $xnewsletter->getHandler('subscr')->get($protocol['protocol_subscriber_id'])) {
                    $protocol['protocol_subscriber_email'] = $subscrObj->getVar('subscr_email');
                } else {
                    $protocol['protocol_subscriber_email'] = _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                }
                $GLOBALS['xoopsTpl']->append('protocols', $protocol);
            }
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_protocols_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOPROTOCOLS;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'apply_actions':
        $action = XoopsRequest::getString('actions_action');
        $protocol_ids = XoopsRequest::getArray('protocol_ids', unserialize(XoopsRequest::getString('serialize_protocol_ids')));
        $protocolCriteria = new Criteria('protocol_id', '(' . implode(',', $protocol_ids) . ')', 'IN');
        switch ($action) {
            case 'delete':
                if (XoopsRequest::getBool('ok', false, 'POST') == true) {
                    // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
                    if ($xnewsletter->getHandler('protocol')->deleteAll($protocolCriteria, true, true)) {
                        redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
                    } else {
                        echo $subscrObj->getHtmlErrors();
                    }
                } else {
                    // render start here
                    xoops_cp_header();
                    // render confirm form
                    xoops_confirm(
                        array('ok' => true, 'op' => 'apply_actions', 'actions_action' => $action, 'serialize_protocol_ids' => serialize($protocol_ids)),
                        $_SERVER['REQUEST_URI'],
                        sprintf(_AM_XNEWSLETTER_FORMSUREDEL, implode(', ', $protocol_ids))
                    );
                    include_once __DIR__ . '/admin_footer.php';
                }
                break;
            default:
                // NOP
                break;
        }
        break;

    case 'delete_protocol':
        $protocol_id = XoopsRequest::getInt('protocol_id', 0);
        $protocolObj = $xnewsletter->getHandler('protocol')->get($protocol_id);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('protocol')->delete($protocolObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $protocolObj->getHtmlErrors();
            }
        } else {
            // render start here
            xoops_cp_header();
            // render confirm form
            xoops_confirm(
                array('ok' => true, 'protocol_id' => $protocol_id, 'op' => 'delete_protocol'),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $protocolObj->getVar('protocol_id'))
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;

    case 'delete_protocols':
        $field = XoopsRequest::getString('field', '');
        $value = XoopsRequest::getInt('value', 0);
        switch ($field) {
            case 'letter_id':
            case 'protocol_letter_id':
                $sql = "DELETE FROM `{$GLOBALS['xoopsDB']->prefix('xnewsletter_protocol')}` WHERE `protocol_letter_id` = {$value}";
                $letterObj = $xnewsletter->getHandler('letter')->get($value);
                $title = $letterObj->getVar('letter_title');
                break;
            case 'subscr_id':
            case 'protocol_subscriber_id':
                $sql = "DELETE FROM `{$GLOBALS['xoopsDB']->prefix('xnewsletter_protocol')}` WHERE `protocol_subscriber_id` = {$value}";
                $subscrObj = $xnewsletter->getHandler('subscr')->get($value);
                $title = $letterObj->getVar('subscr_email');
                break;
            case 'success':
            case 'protocol_success':
                $sql = "DELETE FROM `{$GLOBALS['xoopsDB']->prefix('xnewsletter_protocol')}` WHERE `protocol_success` = {$value}";
                $title = ($value) ? XNEWSLETTER_IMG_OK : XNEWSLETTER_IMG_FAILED;
                break;
            default:
                $sql = "TRUNCATE TABLE `{$GLOBALS['xoopsDB']->prefix('xnewsletter_protocol')}`";
                $title = _AM_XNEWSLETTER_PROTOCOL_DELETE_ALL;
                break;
        }
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $result = $GLOBALS['xoopsDB']->query($sql);
            if ($result) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELNOTOK);
            }
        } else {
            // render start here
            xoops_cp_header();
            // render confirm form
            xoops_confirm(
                array('ok' => true, 'op' => 'delete_protocols', 'field' => $field, 'value' => $value),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL_LIST, $title)
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;

}
