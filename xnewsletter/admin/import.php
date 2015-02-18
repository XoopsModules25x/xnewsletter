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

define('XNEWSLETTER_BASIC_LIMIT_IMPORT_CHECKED', 100);
define('XNEWSLETTER_BASIC_LIMIT_IMPORT_AT_ONCE', 10);

$plugin = XoopsRequest::getString('plugin', 'csv');
$cat_id = XoopsRequest::getInt('cat_id', 0, 'int');
$start = XoopsRequest::getInt('start', 0);
$checkSubscrsAfterRead = XoopsRequest::getBool('checkSubscrsAfterRead', true);
$checkLimit = XoopsRequest::getInt('checkLimit', XNEWSLETTER_BASIC_LIMIT_IMPORT_CHECKED);
$skipcatsubscrexist = XoopsRequest::getInt('skipcatsubscrexist', 1);
$checkImport = XoopsRequest::getInt('checkImport', 0);

echo $indexAdmin->addNavigation($currentFile);

$op = XoopsRequest::getString('op', 'default');
switch ($op) {
    case 'show_formcheck':
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, $currentFile, 'list');
        echo $indexAdmin->renderButton();
        //
        $importCount = $xnewsletter->getHandler('import')->getCount();
        if ($importCount > 0) {
            $importCriteria = new CriteriaCompo();
            $importCriteria->setSort("import_id");
            $importCriteria->setOrder("ASC");
            $importCriteria->setStart($start);
            $importCriteria->setLimit($checkLimit);
            $importObjs = $xnewsletter->getHandler('import')->getAll($importCriteria);
            //
            include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
            $action = $_SERVER['REQUEST_URI'];
            $unique_id = uniqid(mt_rand());
            $form      = "<br/>";
            $form .= "<form name='form_import_{$unique_id}' id='form_import_{$unique_id}' action='{$currentFile}' method='post' enctype='multipart/form-data'>";

            $showlimit = str_replace("%s", $start + 1, _AM_XNEWSLETTER_IMPORT_SHOW);
            if ($checkLimit < $importCount) {
                $showlimit = str_replace("%l", $checkLimit, $showlimit);
            } else {
                $showlimit = str_replace("%l", $importCount, $showlimit);
            }
            $showlimit = str_replace("%n", $importCount, $showlimit);

            $form .= "
            <h3>" . _AM_XNEWSLETTER_IMPORT_AFTER_READ . "</h3>
            <table width='100%' cellspacing='1' class='outer'>
            <tr>
                <td align='left' colspan='8'>" . $showlimit . "</td>
            </tr>";

            $class = "odd";
            $form .= "
            <tr>
                <th>&nbsp;</th>
                <th>" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                <th>" . _AM_XNEWSLETTER_SUBSCR_SEX . "</th>
                <th>" . _AM_XNEWSLETTER_SUBSCR_FIRSTNAME . "</th>
                <th>" . _AM_XNEWSLETTER_SUBSCR_LASTNAME . "</th>
                <th>" . _AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . "</th>
                <th>" . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST . "</th>
                <th>" . _AM_XNEWSLETTER_CAT_NAME . "</th>
            </tr>";

            $class   = 'odd';
            $counter = 0;

            //get data for dropdown with cats
            $catCriteria = new CriteriaCompo();
            $catCriteria->setSort('cat_id ASC, cat_name');
            $catCriteria->setOrder('ASC');
            $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);

            foreach ($importObjs as $i => $importObj) {
                ++$counter;
                $form .= "<tr class='{$class}' >";
                $class = ($class == 'even') ? 'odd' : 'even';
                // import_id
                $form .= "<td>" . $counter;
                $form .= "<input type='hidden' name='import_id_{$counter}' title='import_id_{$counter}' id='import_id_{$counter}' value='{$importObj->getVar('import_id')}' />";
                $form .= "</td>";
                // import_email
                $email_text = new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, "email_{$counter}", 25, 255, $importObj->getVar('import_email'));
                $email_text->setExtra('disabled=disabled');
                $form .= "<td>";
                $form .= $email_text->render();
                unset($email_text);
                $form .= "</td>";
                // import_sex
                $sex_select = new XoopsFormSelect(_AM_XNEWSLETTER_SUBSCR_SEX, "sex_{$counter}", $importObj->getVar('import_sex'), 1, false);
                $sex_options = array(
                    _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY => _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY,
                    _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE => _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE,
                    _AM_XNEWSLETTER_SUBSCR_SEX_MALE => _AM_XNEWSLETTER_SUBSCR_SEX_MALE,
                    _AM_XNEWSLETTER_SUBSCR_SEX_COMP => _AM_XNEWSLETTER_SUBSCR_SEX_COMP,
                    _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY => _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY
                    );
                $sex_select->addOptionArray($sex_options);
                $form .= "<td>";
                $form .= $sex_select->render();
                unset($sex_select);
                $form .= "</td>";
                // import_firstname
                $firstname_text = new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_FIRSTNAME, "firstname_{$counter}", 25, 255, $importObj->getVar('import_firstname'));
                $form .= "<td>";
                $form .= $firstname_text->render();
                unset($firstname_text);
                $form .= "</td>";
                // import_lastname
                $lastname_text = new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_LASTNAME, "lastname_{$counter}", 25, 255, $importObj->getVar('import_lastname'));
                $form .= "<td>";
                $form .= $lastname_text->render();
                unset($lastname_text);
                $form .= "</td>";
                // import_subscr_id
                $form .= "<td>";
                $subscr_id = $importObj->getVar('import_subscr_id');
                $form .= "<input type='hidden' name='subscr_id_{$counter}' title='subscr_id' id='subscr_id_{$counter}' value='{$subscr_id}' />";
                if ($subscr_id > 0) {
                    $form .= "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_ok.png' alt='" . _AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . "' title='" . _AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . "' />";
                }
                $form .= "&nbsp;</td>";
                // import_catsubscr_id
                $form .= "<td>";
                $catsubscr_id = $importObj->getVar('import_catsubscr_id');
                $form .= "<input type='hidden' name='catsubscr_id_{$counter}' title='catsubscr_id' id='catsubscr_id_{$counter}' value='{$catsubscr_id}' />";
                if ($catsubscr_id > 0) {
                    $form .= "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_ok.png' alt='" . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST . "' title='" . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST . "' />";
                }
                $form .= "&nbsp;</td>";
                // import_cat_id
                $cat_id_select = new XoopsFormSelect(_AM_XNEWSLETTER_CAT_NAME, "cat_id_{$counter}", $importObj->getVar('import_cat_id'), 1, false);
                $cat_id_select->addOption('0', _AM_XNEWSLETTER_IMPORT_NOIMPORT);
                foreach ($catObjs as $cat_id => $catObj) {
                    $cat_id_select->addOption($cat_id, $catObj->getVar('cat_name'));
                }
                $form .= "<td>";
                $form .= $cat_id_select->render();
                unset($cat_id_select);
                $form .= "</td>";

                $form .= "</tr>";
            }
            $form .= "<tr class='{$class}'>";
            $class = ($class == 'even') ? 'odd' : 'even';
            $form .= "<td colspan='8'>";
            $form .= "<input type='hidden' name='counter' title='counter' id='counter' value='{$counter}' />";
            $form .= "<input type='hidden' name='checkLimit' title='checkLimit' id='checkLimit' value='" . $checkLimit . "' />";
            $form .= "<input type='hidden' name='op' title='op' id='op' value='apply_import_form' />";
            $form .= "<input type='submit' class='formButton' name='submit' id='submit' value='" . _AM_XNEWSLETTER_IMPORT_EXEC . "' title='" . _AM_XNEWSLETTER_IMPORT_EXEC . "' />";
            $form .= "</td>";
            $form .= "</tr>";

            $form .= "</table>";
            $form .= "</form>";
            echo $form;
        }
        break;

    case 'apply_import_form':
        //update xnewsletter with settings form_import
        $counter = XoopsRequest::getInt('counter', 0);
        for ($i = 1; $i < ($counter + 1); ++$i) {
            $import_id        = XoopsRequest::getString("import_id_{$i}", 'default');
            $subscr_firstname = XoopsRequest::getString("firstname_{$i}", '');
            $subscr_lastname  = XoopsRequest::getString("lastname_{$i}", '');
            $subscr_sex       = XoopsRequest::getString("sex_{$i}", '');
            $cat_id           = XoopsRequest::getInt("cat_id_{$i}", 0);
            if ($cat_id > 0) {
                if ($subscr_id == 0) {
                    //update sex, firstname, lastname
                    $sql = "UPDATE {$GLOBALS['xoopsDB']->prefix('xnewsletter_import')}";
                    $sql .= " SET `import_sex` = '{$subscr_sex}', `import_firstname` = '{$subscr_firstname}', `import_lastname` = '{$subscr_lastname}'";
                    $sql .= " WHERE `import_id`={$import_id}";
                    $result = $GLOBALS['xoopsDB']->queryF($sql);
                }
            }
            //update cat_id and import_status
            $sql = "UPDATE {$GLOBALS['xoopsDB']->prefix('xnewsletter_import')}";
            $sql .= " SET `import_cat_id` = '{$cat_id}', `import_status` = " . _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE . "";
            $sql .= " WHERE `import_id` = {$import_id}";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
        }
        redirect_header("?op=exec_import_final&checkImport=1&checkLimit={$checkLimit}", 0, '');
        break;

    case 'exec_import_final':
        //execute final import of all data from xnewsletter_import, where import_status = _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE
        //delete data from xnewsletter_import, when imported (successful or not)
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, $currentFile, 'list');
        echo $indexAdmin->renderButton();
        //
        $ip = xoops_getenv('REMOTE_ADDR');
        $submitter = $GLOBALS['xoopsUser']->uid();
        //
        $importCount = $xnewsletter->getHandler('import')->getCount();
        $importCriteria = new CriteriaCompo();
        $importCriteria->add(new Criteria('import_status', _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE));
        $importCheckedCount = $xnewsletter->getHandler('import')->getCount($importCriteria);
        if ($importCheckedCount > 0) {
            $sql = "SELECT *";
            $sql .= " FROM {$GLOBALS['xoopsDB']->prefix('xnewsletter_import')}";
            $sql .= " WHERE ((import_status) = " . _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE . ")";
            $sql .= " ORDER BY `import_id` ASC";
            $counter = 0;
            if (!$users_import = $GLOBALS['xoopsDB']->queryF($sql)) {
                die ('MySQL-Error: ' . mysql_error());
            }
            while ($user_import = mysql_fetch_assoc($users_import)) {
                $import_id        = $user_import['import_id'];
                $subscr_email     = $user_import['import_email'];
                $subscr_firstname = $user_import['import_firstname'];
                $subscr_lastname  = $user_import['import_lastname'];
                $subscr_sex       = $user_import['import_sex'];
                $cat_id           = $user_import['import_cat_id'];
                $subscr_id        = $user_import['import_subscr_id'];
                $catsubscr_id     = $user_import['import_catsubscr_id'];
                $subscribe        = false;

                if ($cat_id == 0) {
// IN PROGRESS
                    $status = str_replace("%e", $subscr_email, _AM_XNEWSLETTER_IMPORT_RESULT_SKIP);
                    $xnewsletter->getHandler('protocol')->protocol(0, 0, $status, _XNEWSLETTER_PROTOCOL_STATUS_SKIP_IMPORT, array('%subscr_email' => $subscr_email), true);
                } else {
                    // register email
                    if ($subscr_id == 0) {
                        $subscr_uid = 0;
                        $sql = "SELECT `uid`";
                        $sql .= " FROM {$GLOBALS['xoopsDB']->prefix('users')}";
                        $sql .= " WHERE (`email` = '{$subscr_email}') LIMIT 1";
                        if ($user = $GLOBALS['xoopsDB']->queryF($sql)) {
                            $row_user   = mysql_fetch_array($user);
                            $subscr_uid = $row_user[0];
                        }
                        unset($row_user);
                        unset($user);
                        //
                        $subscrObj = $xnewsletter->getHandler('subscr')->create();
                        $subscrObj->setVar('subscr_email', $subscr_email);
                        $subscrObj->setVar('subscr_firstname', $subscr_firstname);
                        $subscrObj->setVar('subscr_lastname', $subscr_lastname);
                        $subscrObj->setVar('subscr_uid', (int) $subscr_uid);
                        $subscrObj->setVar('subscr_sex', $subscr_sex);
                        $subscrObj->setVar('subscr_submitter', $submitter);
                        $subscrObj->setVar('subscr_created', time());
                        $subscrObj->setVar('subscr_ip', $ip);
                        $subscrObj->setVar('subscr_activated', 1);
                        // add subscriber
                        if (!$xnewsletter->getHandler('subscr')->insert($subscrObj)) {
// IN PROGRESS
                            $status = str_replace("%e", $subscr_email, _AM_XNEWSLETTER_IMPORT_RESULT_FAILED);
                            $xnewsletter->getHandler('protocol')->protocol(0, 0, $status, _XNEWSLETTER_PROTOCOL_STATUS_ERROR_IMPORT, array('%subscr_email' => $subscr_email), false);
                        } else {
                            // register email successful
                            $resulttext = $subscr_email . ': ' . _AM_XNEWSLETTER_IMPORT_RESULT_REG_OK . ' | ';
                            $subscr_id  = $GLOBALS['xoopsDB']->getInsertId();
                            $subscribe  = true;
                        }
                    } else {
                        // email already registered
                        $resulttext = $subscr_email . ': ' . _AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . ' | ';
                        $subscribe  = true;
                    }
                    if ($subscribe) {
                        if ($catsubscr_id == 0) {
                            //add subscription of this email
                            $sql = "INSERT";
                            $sql .= " INTO `{$GLOBALS['xoopsDB']->prefix('xnewsletter_catsubscr')}`";
                            $sql .= " (`catsubscr_catid`, `catsubscr_subscrid`, `catsubscr_submitter`, `catsubscr_created`)";
                            $sql .= " VALUES ({$cat_id}, {$subscr_id}, {$submitter}, " . time() . ")";
                            if ($GLOBALS['xoopsDB']->queryF($sql)) {
// IN PROGRESS
                                $status = $resulttext . _AM_XNEWSLETTER_IMPORT_RESULT_SUBSCR_OK;
                                $xnewsletter->getHandler('protocol')->protocol(0, 0, $status, _XNEWSLETTER_PROTOCOL_STATUS_OK_IMPORT, array('%result_text' => $resulttext), true);
                                //handle mailinglists
                                $cat_mailinglist = 0;
                                $sql = "SELECT `cat_mailinglist`";
                                $sql .= " FROM {$GLOBALS['xoopsDB']->prefix('xnewsletter_cat')}";
                                $sql .= " WHERE (`cat_id` = {$cat_id}) LIMIT 1";
                                if ($cat_mls = $GLOBALS['xoopsDB']->queryF($sql)) {
                                    $cat_ml          = mysql_fetch_array($cat_mls);
                                    $cat_mailinglist = $cat_ml[0];
                                }
                                unset($cat_ml);
                                unset($cat_mls);

                                if ($cat_mailinglist > 0) {
                                    require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                                    subscribingMLHandler(1, $subscr_id, $cat_mailinglist);
                                }
                            } else {
// IN PROGRESS
                                $status = str_replace("%e", $subscr_email, _AM_XNEWSLETTER_IMPORT_RESULT_FAILED);
                                $xnewsletter->getHandler('protocol')->protocol(0, 0, $status, _XNEWSLETTER_PROTOCOL_STATUS_ERROR_IMPORT, array('%subscr_email' => $subscr_email), false);

                            }
                        } else {
// IN PROGRESS
                            $status = $resulttext . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST;
                            $xnewsletter->getHandler('protocol')->protocol(0, 0, $status, _XNEWSLETTER_PROTOCOL_STATUS_EXIST_IMPORT, array('%result_text' => $resulttext), false);

                        }
                    }
                }
                $sql_delete = "DELETE";
                $sql_delete .= " FROM {$GLOBALS['xoopsDB']->prefix('xnewsletter_import')}";
                $sql_delete .= " WHERE `import_id` = {$import_id}";
                $result = $GLOBALS['xoopsDB']->queryF($sql_delete);
            }

            echo "<div style='margin-top:20px;margin-bottom:20px;color:#ff0000;font-weight:bold;font-size:14px'>";
            $resulttext = str_replace("%p", $importCheckedCount, _AM_XNEWSLETTER_IMPORT_FINISHED);
            $resulttext = str_replace("%t", $importCount, $resulttext);
            echo XNEWSLETTER_IMG_OK . $resulttext;
            echo "</div>";

            $importPendingCount = $xnewsletter->getHandler('import')->getCount();
            if ($importPendingCount > 0) {
                $form_continue = "<form id='form_continue' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_continue'>";
                $form_continue .= "<input id='submit' class='formButton' type='submit' title='" . _AM_XNEWSLETTER_IMPORT_CONTINUE . "' value='" . _AM_XNEWSLETTER_IMPORT_CONTINUE . "' name='submit'>";
                $form_continue .= "<input type='hidden' id='checkLimit' name='checkLimit' value='{$checkLimit}'>";
                if ($checkImport === 1) {
                    //show next form for check settings
                    $form_continue .= "<input type='hidden' id='op' name='op' value='show_formcheck'>";
                } else {
                    // set import_status = _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE for next package
                    $sql_update = "UPDATE {$GLOBALS['xoopsDB']->prefix('xnewsletter_import')}";
                    $sql_update .= " SET `import_status` = " . _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE . "";
                    $sql_update .= " ORDER BY import_id LIMIT " . $checkLimit;
                    $GLOBALS['xoopsDB']->queryF($sql_update);
                    //execute import for the next package
                    $form_continue .= "<input type='hidden' id='op' name='op' value='exec_import_final' >";
                }
                $form_continue .= "<input type='hidden' id='checkSubscrsAfterRead' name='checkSubscrsAfterRead' value='{$checkSubscrsAfterRead}'>";
                $form_continue .= "<input type='hidden' id='checkLimit' name='checkLimit' value='{$checkLimit}'>";
                $form_continue .= "<input type='hidden' id='plugin' name='plugin' value='{$plugin}'>";
                $form_continue .= "<input type='hidden' id='checkImport' name='checkImport' value='{$checkImport}' >";
                $form_continue .= "</form>";
                echo $form_continue;
            } else {
                $form_continue = "<form id='form_continue' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_continue'>";
                $form_continue .= "<input id='submit' class='formButton' type='submit' title='" . _AM_XNEWSLETTER_IMPORT_END . "' value='" . _AM_XNEWSLETTER_IMPORT_END . "' name='submit'>";
                $form_continue .= "<input type='hidden' id='op' name='op' value='default'>";
                $form_continue .= "</form>";
                echo $form_continue;
            }
        } else {
            echo _AM_XNEWSLETTER_IMPORT_NODATA;
        }
        break;

    case 'searchdata':
        // delete all existing import data
        // import data into xnewsletter_import table with plugin
        // set cat_id as preselected, update information about existing registration/subscriptions
        // if ($checkSubscrsAfterRead === true) execute import else show form for check before executing import

        // delete all existing data
        $sql = "TRUNCATE TABLE " . $GLOBALS['xoopsDB']->prefix('xnewsletter_import');
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        // check and load plugin
        $pluginFile = XNEWSLETTER_ROOT_PATH . "/plugins/{$plugin}.php";
        if (!file_exists($pluginFile)) {
            echo str_replace("%p", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_PLUGIN);
            break;
        }
        require_once $pluginFile;
        $function = 'xnewsletter_plugin_getdata_' . $plugin;
        if (!function_exists($function)) {
            echo "Error: require function 'xnewsletter_plugin_getdata_{$plugin}' doesn't exist";
            echo str_replace("%f", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_FUNCTION);
            break;
        }
        //import data into xnewsletter_import with plugin
        if ($plugin == 'csv') {
            $csv_file = $_FILES['csv_file']['tmp_name'];
            $csvSkipHeader = XoopsRequest::getBool('csvSkipHeader', false);
            $csvDelimiter = XoopsRequest::getString('csvDelimiter', ',');
            //$numData = $function($cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist, $csv_file, $csvDelimiter, $csvSkipHeader);
            $numData = call_user_func($function, $cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist, $csv_file, $csvDelimiter, $csvSkipHeader);
        } else {
            if ($plugin == 'xoopsuser') {
                $arr_groups = $_POST['xoopsuser_group'];
                //$numData = $function($cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist, $arr_groups);
                $numData = call_user_func($function, $cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist, $arr_groups);
            } else {
                //$numData = $function($cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist);
                $numData = call_user_func($function, $cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist);
            }
        }
        if ($numData > 0) {
            if ($checkSubscrsAfterRead === false) {
                //execute import without check
                redirect_header("{$currentFile}?op=exec_import_final&checkSubscrsAfterRead=0&checkLimit={$checkLimit}", 3, '');
            } else {
                //show form for check before executing import
                redirect_header("{$currentFile}?op=show_formcheck&checkSubscrsAfterRead=1&plugin={$plugin}&checkLimit={$checkLimit}", 3, '');
            }
        } else {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_IMPORT_NODATA);
        }
        break;

    case 'form_additional':
        //show form for additional settings
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, $currentFile, 'list');
        echo $indexAdmin->renderButton();
        //
        $pluginFile = XNEWSLETTER_ROOT_PATH . "/plugins/{$plugin}.php";
        if (!file_exists($pluginFile)) {
            echo str_replace("%p", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_PLUGIN);
            break;
        }
        require_once $pluginFile;

        $function = "xnewsletter_plugin_getform_{$plugin}";
        if (!function_exists($function)) {
            echo str_replace("%f", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_FUNCTION);
            break;
        }
        //$form = $function( $cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist );
        $form = call_user_func($function, $cat_id, $checkSubscrsAfterRead, $checkLimit, $skipcatsubscrexist);
        $form->display();
        break;

    case 'default':
    default:
        //show basic search form
        $importObj = $xnewsletter->getHandler('import')->create();
        $form      = $importObj->getSearchForm($plugin, $checkSubscrsAfterRead, $checkLimit);
        $form->display();
        break;
}
include_once __DIR__ . '/admin_footer.php';
