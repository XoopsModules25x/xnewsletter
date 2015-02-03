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
$op = XoopsRequest::getString('op', 'list_tasks');
switch ($op) {
    case 'list':
    case 'list_tasks':
    default:
        $apply_filter = XoopsRequest::getBool('apply_filter', false);
        // render start here
        xoops_cp_header();
        // render submenu
        $taskAdmin = new ModuleAdmin();
        echo $taskAdmin->addNavigation($currentFile);
        if ($apply_filter == true) {
            $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_SHOW_ALL, '?op=list_tasks', 'view_detailed');
        }
        $taskCount = $xnewsletter->getHandler('task')->getCount();
        $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_CONFIGS, '?op=configs_show', 'exec');
        if ($taskCount > 0) {
            $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_DELETE_ALL, '?op=delete_tasks', 'delete');
            $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_RUN_CRON_NOW, '?op=run_cron_now', 'exec');
        }
        echo $taskAdmin->renderButton();
        //
        $GLOBALS['xoopsTpl']->assign('taskCount', $taskCount);
        if ($taskCount > 0) {
            $taskCriteria = new CriteriaCompo();
            // get filter parameters
            $filter_task_letter_ids = XoopsRequest::getArray('filter_task_letter_ids', array());
            //
            if (!empty($_REQUEST['filter_task_created_from']['date'])) {
                $dateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $_REQUEST['filter_task_created_from']['date']);
                $dateTimeObj->setTime(0, 0, 0);
                $filter_task_created_from = (int) ($dateTimeObj->getTimestamp() + $_REQUEST['filter_task_created_from']['time']);
                unset($dateTimeObj);
            } else {
                $filter_task_created_from = 3600;
            }
            if (!empty($_REQUEST['filter_task_created_to']['date'])) {
                $dateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $_REQUEST['filter_task_created_to']['date']);
                $dateTimeObj->setTime(0, 0, 0);
                $filter_task_created_to = (int) ($dateTimeObj->getTimestamp() + $_REQUEST['filter_task_created_to']['time']);
                unset($dateTimeObj);
            } else {
                $filter_task_created_to = time();
            }
            //
            if (!empty($_REQUEST['filter_task_starttime_from']['date'])) {
                $dateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $_REQUEST['filter_task_starttime_from']['date']);
                $dateTimeObj->setTime(0, 0, 0);
                $filter_task_starttime_from = (int) ($dateTimeObj->getTimestamp() + $_REQUEST['filter_task_starttime_from']['time']);
                unset($dateTimeObj);
            } else {
                $filter_task_starttime_from = 3600;
            }
            if (!empty($_REQUEST['filter_task_starttime_to']['date'])) {
                $dateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $_REQUEST['filter_task_starttime_to']['date']);
                $dateTimeObj->setTime(0, 0, 0);
                $filter_task_starttime_to = (int) ($dateTimeObj->getTimestamp() + $_REQUEST['filter_task_starttime_to']['time']);
                unset($dateTimeObj);
            } else {
                $filter_task_starttime_to = time();
            }


            if ($apply_filter == true) {
                // apply filter
                if (count($filter_protocol_letter_ids) > 0) {
                    $taskCriteria->add(new Criteria('task_letter_id', '(' . implode(',', $filter_task_letter_ids) . ')', 'IN'));
                }
                //
                if ($filter_task_created_from != 0) {
                    $taskCriteria->add(new Criteria('task_created', $filter_task_created_from, '>='));
                }
                if ($filter_task_created_to != 0) {
                    $taskCriteria->add(new Criteria('task_created', $filter_task_created_to, '<='));
                }
                //
                if ($filter_task_starttime_from != 0) {
                    $taskCriteria->add(new Criteria('task_starttime', $filter_task_starttime_from, '>='));
                }
                if ($filter_task_starttime_to != 0) {
                    $taskCriteria->add(new Criteria('task_starttime', $filter_task_starttime_to, '<='));
                }
            }
            $GLOBALS['xoopsTpl']->assign('apply_filter', $apply_filter);
            $taskFilterCount = $xnewsletter->getHandler('task')->getCount($taskCriteria);
            $GLOBALS['xoopsTpl']->assign('taskFilterCount', $taskFilterCount);
            //
            $taskCriteria->setSort('task_starttime');
            $taskCriteria->setOrder('ASC');
            //
            $start = XoopsRequest::getInt('start', 0);
            $limit = $xnewsletter->getConfig('adminperpage');
            $taskCriteria->setStart($start);
            $taskCriteria->setLimit($limit);
            //
            $taskObjs = $xnewsletter->getHandler('task')->getAll($taskCriteria);
            $tasks = $xnewsletter->getHandler('task')->getObjects($taskCriteria, true, false); // as array
            //
            $letterCriteria = new CriteriaCompo();
            $letterCriteria->setSort('letter_created');
            $letterCriteria->setOrder('DESC');
            //$letterObjs = $xnewsletter->getHandler('letter')->getAll($letterCriteria);
            $letters = $xnewsletter->getHandler('letter')->getObjects($letterCriteria, true, false);
            //
            if ($taskFilterCount > $limit) {
                xoops_load('xoopspagenav');
                $linklist = "op={$op}";
                foreach ($filter_task_letter_ids as $filter_task_letter_id) {
                    $linklist .= "&filter_task_letter_ids[]={$filter_task_letter_id}";
                }
                $linklist .= "&filter_task_created_from[date]={$_REQUEST['filter_task_created_from']['date']}";
                $linklist .= "&filter_task_created_from[time]={$_REQUEST['filter_task_created_from']['time']}";
                $linklist .= "&filter_task_created_to[date]={$_REQUEST['filter_task_created_to']['date']}";
                $linklist .= "&filter_task_created_to[time]={$_REQUEST['filter_task_created_to']['time']}";
                $linklist .= "&filter_task_starttime_from[date]={$_REQUEST['filter_task_starttime_from']['date']}";
                $linklist .= "&filter_task_starttime_from[time]={$_REQUEST['filter_task_starttime_from']['time']}";
                $linklist .= "&filter_task_starttime_to[date]={$_REQUEST['filter_task_starttime_to']['date']}";
                $linklist .= "&filter_task_starttime_to[time]={$_REQUEST['filter_task_starttime_to']['time']}";
                $pagenav = new XoopsPageNav($taskFilterCount, $limit, $start, 'start', $linklist);
                $pagenav = $pagenav->renderNav(4);
            } else {
                $pagenav = '';
            }
            $GLOBALS['xoopsTpl']->assign('tasks_pagenav', $pagenav);
            //
            $filter_task_letter_ids_select = new XoopsFormSelect(_AM_XNEWSLETTER_LETTER_TITLE, 'filter_task_letter_ids', $filter_task_letter_ids, 1, true);
            $filter_task_letter_ids_select->addOption(0, _AM_XNEWSLETTER_PROTOCOL_MISC);
            foreach ($letters as $letter) {
                $filter_task_letter_ids_select->addOption($letter['letter_id'], $letter['letter_title']);
            }
            $GLOBALS['xoopsTpl']->assign('filter_task_letter_ids_select', $filter_task_letter_ids_select->render());
            //
            $filter_task_created_from_datetime = new XoopsFormDateTime(_AM_XNEWSLETTER_TASK_CREATED_FILTER_FROM, 'filter_task_created_from', 15, $filter_task_created_from, true);
            $GLOBALS['xoopsTpl']->assign('filter_task_created_from_datetime', $filter_task_created_from_datetime->render());
            $filter_task_created_to_datetime = new XoopsFormDateTime(_AM_XNEWSLETTER_TASK_CREATED_FILTER_TO, 'filter_task_created_to', 15, $filter_task_created_to, true);
            $GLOBALS['xoopsTpl']->assign('filter_task_created_to_datetime', $filter_task_created_to_datetime->render());
            //
            $filter_task_starttime_from_datetime = new XoopsFormDateTime(_AM_XNEWSLETTER_TASK_STARTTIME_FILTER_FROM, 'filter_task_starttime_from', 15, $filter_task_starttime_from, true);
            $GLOBALS['xoopsTpl']->assign('filter_task_starttime_from_datetime', $filter_task_starttime_from_datetime->render());
            $filter_task_starttime_to_datetime = new XoopsFormDateTime(_AM_XNEWSLETTER_TASK_STARTTIME_FILTER_TO, 'filter_task_starttime_to', 15, $filter_task_starttime_to, true);
            $GLOBALS['xoopsTpl']->assign('filter_task_starttime_to_datetime', $filter_task_starttime_to_datetime->render());
            //
            $GLOBALS['xoopsTpl']->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
            // fill tasks array
            $now = time(); // timestamp
            foreach ($tasks as $task_id => $task) {
                $task['task_letter_title'] = $letters[$task['task_letter_id']]['letter_title'];
                $task['task_starttime_formatted'] = formatTimestamp($task['task_starttime'], $xnewsletter->getConfig('dateformat')); // or 'mysql'
                $task['task_starttime_expired'] = ($task['task_starttime'] < $now) ? true : false;
                if ($task['task_subscr_id'] == 0) {
                    //send_test
                    $task['task_subscr_email'] = $letterObj->getVar('letter_email_test') . "<br/>(send_test)";
                } else {
                    $subscrObj = $xnewsletter->getHandler('subscr')->get($task['task_subscr_id']);
                    if (is_object($subscrObj)) {
                        $task['task_subscr_email'] = $subscrObj->getVar("subscr_email");
                    } else {
                        $task['task_subscr_email'] = _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                    }
                }
                $task['task_submitter_uname'] = XoopsUser::getUnameFromId($task['task_submitter'], 'S');
                $task['task_created_formatted'] = formatTimestamp($task['task_created'], $xnewsletter->getConfig('dateformat')); // or 'mysql'
                $GLOBALS['xoopsTpl']->append('tasks', $task);
            }
            //
            $GLOBALS['xoopsTpl']->display("db:{$xnewsletter->getModule()->dirname()}_admin_tasks_list.tpl");
        } else {
            echo _CO_XNEWSLETTER_WARNING_NOTASKS;
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'apply_actions':
        $action = XoopsRequest::getString('actions_action');
        $task_ids = XoopsRequest::getArray('task_ids', unserialize(XoopsRequest::getString('serialize_task_ids')));
        $taskCriteria = new Criteria('task_id', '(' . implode(',', $task_ids) . ')', 'IN');
        switch ($action) {
            case 'delete':
                if (XoopsRequest::getBool('ok', false, 'POST') == true) {
                    // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
                    if ($xnewsletter->getHandler('task')->deleteAll($taskCriteria, true, true)) {
                        redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
                    } else {
                        echo $subscrObj->getHtmlErrors();
                    }
                } else {
                    // render start here
                    xoops_cp_header();
                    // render confirm form
                    xoops_confirm(
                        array('ok' => true, 'op' => 'apply_actions', 'actions_action' => $action, 'serialize_task_ids' => serialize($task_ids)),
                        $_SERVER['REQUEST_URI'],
                        sprintf(_AM_XNEWSLETTER_FORMSUREDEL, implode(', ', $task_ids))
                    );
                    include_once __DIR__ . '/admin_footer.php';
                }
                break;
            case 'execute':
                include_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/functions.task.php';
                // update tasks startdate and execute expired tasks
                if ($xnewsletter->getHandler('task')->updateAll('task_starttime', time() - 1, $taskCriteria, true)) {
                    xnewsletter_executeTasks(); // execute all expired tasks: startime < time()
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMEXECUTEOK);
                } else {
                    echo $subscrObj->getHtmlErrors();
                }
                break;
            default:
                // NOP
                break;
        }
        break;

    case 'delete_task':
        $task_id = XoopsRequest::getInt('task_id', 0);
        $taskObj = $xnewsletter->getHandler('task')->get($task_id);
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('task')->delete($taskObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $taskObj->getHtmlErrors();
            }
        } else {
            // render start here
            xoops_cp_header();
            // render confirm form
            xoops_confirm(
                array('ok' => true, 'op' => 'delete_task', 'task_id' => $task_id),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $taskObj->getVar('task'))
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;

    case 'delete_tasks':
        $field = XoopsRequest::getString('field', '');
        $value = XoopsRequest::getInt('value', 0);
        switch ($field) {
            case 'letter_id':
            case 'task_letter_id':
                $sql = "DELETE FROM `{$GLOBALS['xoopsDB']->prefix('xnewsletter_task')}` WHERE `task_letter_id` = {$value}";
                $letterObj = $xnewsletter->getHandler('letter')->get($value);
                $title = $letterObj->getVar('letter_title');
                break;
            case 'subscr_id':
            case 'task_subscr_id':
                $sql = "DELETE FROM `{$GLOBALS['xoopsDB']->prefix('xnewsletter_task')}` WHERE `task_subscr_id` = {$value}";
                $subscrObj = $xnewsletter->getHandler('subscr')->get($value);
                $title = $letterObj->getVar('subscr_email');
                break;
            default:
                $sql = "TRUNCATE TABLE `{$GLOBALS['xoopsDB']->prefix('xnewsletter_task')}`";
                $title = _AM_XNEWSLETTER_TASK_DELETE_ALL;
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
                array('ok' => true, 'op' => 'delete_tasks', 'field' => $field, 'value' => $value),
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_XNEWSLETTER_FORMSUREDEL_LIST, $title)
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;

    case 'run_cron_now':
        if (XoopsRequest::getBool('ok', false, 'POST') == true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            // render start here
            xoops_cp_header();
            include_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/functions.task.php';
            // execute all pending tasks
            $result_exec = xnewsletter_executeTasks($xnewsletter->getConfig('xn_send_in_packages'), 0);
            redirect_header($currentFile, 3, "result cron: {$result_exec}");
        } else {
            // render start here
            xoops_cp_header();
            // render confirm form
            xoops_confirm(
                array('ok' => true, 'op' => 'run_cron_now'),
                $_SERVER['REQUEST_URI'],
                _AM_XNEWSLETTER_FORMSURERUNCRONNOW
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;

    case 'configs_show':
        $module = $xnewsletter->getModule();
        $mod = $module->mid();
        $modname = $module->name();
        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/preferences', 'system');
        if (isset($_POST)) {
            foreach ($_POST as $k => $v) {
                ${$k} = $v;
            }
        }
        $config_handler = xoops_gethandler('config');
        $config = $config_handler->getConfigs(new Criteria('conf_modid', $module->mid()));
        $count = count($config);
        if ($count < 1) {
            redirect_header($module->getInfo('adminindex'), 1);
        }
        $xv_configs = $module->getInfo('config');
        $config_cats = $module->getInfo('configcat');
        if (!in_array('others', array_keys($config_cats))) {
            $config_cats['others'] = array(
                'name' => _MI_XNEWSLETTER_CONFCAT_OTHERS,
                'description' => _MI_XNEWSLETTER_CONFCAT_OTHERS_DESC
            );
        }
        $cat_others_used = false;
        xoops_loadLanguage('modinfo', $module->getVar('dirname'));
        if ($module->getVar('hascomments') == 1) {
            xoops_loadLanguage('comment');
        }
        if ($module->getVar('hasnotification') == 1) {
            xoops_loadLanguage('notification');
        }
        xoops_load('XoopsFormLoader');
        foreach ($config_cats as $form_cat => $info) {
            $$form_cat = new XoopsThemeForm($info['name'], 'pref_form_' . $form_cat, 'task.php', 'post', true);
        }
        for ($i = 0; $i < $count; $i++) {
            foreach ($xv_configs as $xv_config) {
                if ($config[$i]->getVar('conf_name') == $xv_config['name']) break;
            }
            $form_cat = @$xv_config['category'];
            if (!in_array($form_cat, array_keys($config_cats))) {
                $form_cat = 'others';
                $cat_others_used = true;
            }
            $title = (!defined($config[$i]->getVar('conf_desc')) || constant($config[$i]->getVar('conf_desc')) == '') ? constant($config[$i]->getVar('conf_title')) : constant($config[$i]->getVar('conf_title')) . '<br /><br /><span style="font-weight:normal;">' . constant($config[$i]->getVar('conf_desc')) . '</span>';
            switch ($config[$i]->getVar('conf_formtype')) {
                case 'textarea':
                    $myts = MyTextSanitizer::getInstance();
                    if ($config[$i]->getVar('conf_valuetype') == 'array') {
                        // this is exceptional.. only when value type is arrayneed a smarter way for this
                        $ele = ($config[$i]->getVar('conf_value') != '') ? new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars(implode('|', $config[$i]->getConfValueForOutput())), 5, 50) : new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), '', 5, 50);
                    } else {
                        $ele = new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()), 5, 50);
                    }
                    break;
                case 'select':
                    $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                    $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                    $opcount = count($options);
                    for ($j = 0; $j < $opcount; $j++) {
                        $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                        $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                        $ele->addOption($optval, $optkey);
                    }
                    break;
                case 'select_multi':
                    $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                    $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                    $opcount = count($options);
                    for ($j = 0; $j < $opcount; $j++) {
                        $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                        $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                        $ele->addOption($optval, $optkey);
                    }
                    break;
                case 'yesno':
                    $ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
                    break;
                case 'group':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                    break;
                case 'group_multi':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                    break;
                case 'user':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                    break;
                case 'user_multi':
                    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                    $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                    break;
                case 'password':
                    $myts = MyTextSanitizer::getInstance();
                    $ele = new XoopsFormPassword($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;
                case 'color':
                    $myts = MyTextSanitizer::getInstance();
                    $ele = new XoopsFormColorPicker($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;
                case 'hidden':
                    $myts = MyTextSanitizer::getInstance();
                    $ele = new XoopsFormHidden($config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;
                case 'textbox':
                default:
                    $myts = MyTextSanitizer::getInstance();
                    $ele = new XoopsFormText($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                    break;
            }
            $hidden = new XoopsFormHidden('conf_ids[]', $config[$i]->getVar('conf_id'));
            $$form_cat->addElement($ele);
            $$form_cat->addElement($hidden);
            unset($ele);
            unset($hidden);
        }

        // render start here
        xoops_cp_header();
        // render submenu
        $taskAdmin = new ModuleAdmin();
        echo $taskAdmin->addNavigation($currentFile);
        $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_SHOW_ALL, '?op=list_tasks', 'view_detailed');
        $taskCount = $xnewsletter->getHandler('task')->getCount();
        if ($taskCount > 0) {
            $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_DELETE_ALL, '?op=delete_tasks', 'delete');
            $taskAdmin->addItemButton(_AM_XNEWSLETTER_TASK_RUN_CRON_NOW, '?op=run_cron_now', 'exec');
        }
        echo $taskAdmin->renderButton();
        //
        foreach ($config_cats as $form_cat => $info) {
            if ($form_cat != 'task') continue;
            $$form_cat->addElement(new XoopsFormHidden('op', 'configs_save'));
            $$form_cat->addElement(new XoopsFormButton('', 'button', _GO, 'submit'));
            $$form_cat->display();
        }
        include_once __DIR__ . '/admin_footer.php';
        break;

    case 'configs_save':
        $module = $xnewsletter->getModule();
        $mod = $module->mid();
        $modname = $module->name();
        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/preferences', 'system');
        if (isset($_POST)) {
            foreach ($_POST as $k => $v) {
                ${$k} = $v;
            }
        }
        if (isset($_GET['configcat'])) {
            $configcat = $_GET['configcat'];
        }
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($module->getInfo('adminindex'), 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $count = count($conf_ids);
        $config_handler = xoops_gethandler('config');
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $config = $config_handler->getConfig($conf_ids[$i]);
                $new_value =& ${$config->getVar('conf_name')};
                if (is_array($new_value) || $new_value != $config->getVar('conf_value')) {
                    $config->setConfValueForInput($new_value);
                    $config_handler->insertConfig($config);
                }
                unset($new_value);
            }
        }
        redirect_header('task.php', 3, _AM_DBUPDATED);
}
