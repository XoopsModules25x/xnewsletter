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
 *  Version : $Id $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

define('XNEWSLETTER_BASIC_LIMIT_IMPORT_CHECKED', 100);
define('XNEWSLETTER_BASIC_LIMIT_IMPORT_AT_ONCE', 10);

$op                 = XnewsletterRequest::getString('op', 'default');
$plugin             = XnewsletterRequest::getString('plugin', 'csv');
$cat_id             = XnewsletterRequest::getInt('cat_id', 0, 'int');
$action_after_read  = XnewsletterRequest::getInt('action_after_read', 1);
$start              = XnewsletterRequest::getInt('start', 0);
$limitcheck         = XnewsletterRequest::getInt('limitcheck', XNEWSLETTER_BASIC_LIMIT_IMPORT_CHECKED);
$skipcatsubscrexist = XnewsletterRequest::getInt('skipcatsubscrexist', 1);
$check_import       = XnewsletterRequest::getInt('check_import', 0);

echo $indexAdmin->addNavigation($currentFile);

switch ($op) {
    case "show_formcheck":
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, $currentFile, 'list');
        echo $indexAdmin->renderButton();
        //
        $importCriteria = new CriteriaCompo();
        $importCriteria->setSort("import_id");
        $importCriteria->setOrder("ASC");
        $importsCount = $xnewsletter->getHandler('import')->getCount($importCriteria);

        $importCriteria->setStart($start);
        $importCriteria->setLimit($limitcheck);
        $importObjs = $xnewsletter->getHandler('import')->getAll($importCriteria);

        if ($importsCount > 0) {
            include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");

            $action = $_SERVER["REQUEST_URI"];
            $unique_id = uniqid(mt_rand());
            $form = "<br/>";
            $form .= "<form name=\"form_import_{$unique_id}\" id=\"form_import_{$unique_id}\" action=\"{$currentFile}\" method=\"post\" enctype=\"multipart/form-data\">";

            $showlimit = str_replace("%s", $start + 1, _AM_XNEWSLETTER_IMPORT_SHOW);
            if ($limitcheck < $importsCount) {
                $showlimit = str_replace("%l", $limitcheck, $showlimit);
            } else {
                $showlimit = str_replace("%l", $importsCount, $showlimit);
            }
            $showlimit = str_replace("%n", $importsCount, $showlimit);

            $form .= "
            <table width=\"100%\" cellspacing=\"1\" class=\"outer\">
            <tr>
                <td align=\"left\" colspan='8'>" . $showlimit . "</td>
            </tr>";

            $class = "odd";
            $form .= "
            <tr>
                <th align=\"center\">&nbsp;</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_SUBSCR_SEX . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_SUBSCR_FIRSTNAME . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_SUBSCR_LASTNAME . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST . "</th>
                <th align=\"center\">" . _AM_XNEWSLETTER_CAT_NAME . "</th>
            </tr>";

            $class = "odd";
            $counter = 0;

            //get data for dropdown with cats
            $catCriteria = new CriteriaCompo();
            $catCriteria->setSort('cat_id ASC, cat_name');
            $catCriteria->setOrder('ASC');
            $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);

            foreach ($importObjs as $i => $importObj) {
                ++$counter;
                $form .= "<tr class=\"" . $class . "\">";
                $class = ($class == "even") ? "odd" : "even";
                $form .=  "<td align=\"center\">" . $counter;
                $form .=  "<input type='hidden' name='import_id_" . $counter . "' title='import_id_" . $counter . "' id='import_id_" . $counter . "' value='" . $importObj->getVar("import_id") . "' />";
                $form .=  "</td>";
                $form .=  "<td align=\"center\">";
                $form .=  "<input type='text' disabled=disabled name='email_" . $counter . "' title='" . _AM_XNEWSLETTER_SUBSCR_EMAIL . "' id='email_" . $counter . "' value='" . $importObj->getVar("import_email") . "' />";
                $form .=  "</td>";

                $form .=  "<td align=\"center\">";
                $sex = $importObj->getVar("import_sex");
                $form .= "<select size=\"1\" name=\"sex_" . $counter . "\" id=\"sex_" . $counter . "\" title=\"" . _AM_XNEWSLETTER_SUBSCR_SEX . "\" ";
                $form .= "value=\"" . $sex . "\">";
                $form .= "<option value=\"\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY . "</option>";
                $form .= "<option value=\"" . _AM_XNEWSLETTER_SUBSCR_SEX_GIRL . "\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_GIRL)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_GIRL . "</option>";
                $form .= "<option value=\"" . _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE . "\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE . "</option>";
                $form .= "<option value=\"" . _AM_XNEWSLETTER_SUBSCR_SEX_BOY . "\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_BOY)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_BOY . "</option>";
                $form .= "<option value=\"" . _AM_XNEWSLETTER_SUBSCR_SEX_MALE . "\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_MALE)
                    $form .= " selected=\"selected\"";
                $form .= ">"._AM_XNEWSLETTER_SUBSCR_SEX_MALE . "</option>";
                $form .= "<option value=\"" . _AM_XNEWSLETTER_SUBSCR_SEX_DOCTOR . "\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_DOCTOR)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_DOCTOR . "</option>";
                $form .= "<option value=\"" . _AM_XNEWSLETTER_SUBSCR_SEX_COMP . "\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_COMP)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_COMP . "</option>";
                $form .= "<option value=\""._AM_XNEWSLETTER_SUBSCR_SEX_FAMILY."\"";
                if ($sex == _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY)
                    $form .= " selected=\"selected\"";
                $form .= ">" . _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY . "</option>";
                $form .= "</select>\n";
                $form .=  "</td>";

                $form .=  "<td align=\"center\">";
                $form .=  "<input type='text' name='firstname_" . $counter . "' title='" . _AM_XNEWSLETTER_SUBSCR_FIRSTNAME . "' id='firstname_" . $counter . "' value='" . $importObj->getVar("import_firstname") . "' />";
                $form .=  "</td>";
                $form .=  "<td align=\"center\">";
                $form .=  "<input type='text' name='lastname_" . $counter . "' title='" . _AM_XNEWSLETTER_SUBSCR_LASTNAME . "' id='lastname_" . $counter . "' value='" . $importObj->getVar("import_lastname") . "' />";
                $form .=  "</td>";
                $form .=  "<td align=\"center\">";
                $subscr_id = $importObj->getVar("import_subscr_id");
                $form .= "<input type='hidden' name='subscr_id_" . $counter . "' title='subscr_id' id='subscr_id_" . $counter . "' value='" . $subscr_id . "' />";
                if ($subscr_id > 0)
                    $form .= "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_ok.png' alt='" . _AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . "' title='"._AM_XNEWSLETTER_IMPORT_EMAIL_EXIST."' />";
                $form .=  "</td>";
                $form .=  "<td align=\"center\">";
                $catsubscr_id = $importObj->getVar("import_catsubscr_id");
                $form .= "<input type='hidden' name='catsubscr_id_" . $counter . "' title='catsubscr_id' id='catsubscr_id_" . $counter . "' value='".$catsubscr_id . "' />";
                if ($catsubscr_id > 0)
                    $form .= "<img src='" . XNEWSLETTER_ICONS_URL . "/xn_ok.png' alt='" . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST . "' title='" . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST . "' />";
                $form .=  "</td>";
                $form .=  "</td>";
                $form .=  "<td align=\"center\">";

                $curr_cat_id =$importObj->getVar("import_cat_id");
                $form .= "<select size=\"1\" name=\"cat_id_" . $counter . "\" id=\"cat_id_" . $counter . "\" title=\"cat\" ";
                $form .= "value=\"" . $curr_cat_id . "\">";
                $cat_select = "<option value=\"0\"";
                $cat_select .= ">" . _AM_XNEWSLETTER_IMPORT_NOIMPORT . "</option>";
                foreach ($catObjs as $cat_id => $catObj) {
                    $cat_select .= "<option value=\"" . $cat_id . "\"";
                    if ($curr_cat_id == $j) $cat_select .= " selected=\"selected\"";
                    $cat_select .= ">" . $catObj->getVar("cat_name") . "</option>";
                }

                $form .= $cat_select;
                $form .= "</select>\n";

                $form .=  "</td>";
                $form .=  "</tr>";
            }
            $form .= "<tr class=\"".$class."\">";
            $class = ($class == "even") ? "odd" : "even";
            $form .= "<td colspan=\"8\"align=\"center\">";
            $form .= "<input type='hidden' name='counter' title='counter' id='counter' value='" . $counter . "' />";
            $form .= "<input type='hidden' name='limitcheck' title='limitcheck' id='limitcheck' value='" . $limitcheck . "' />";
            $form .= "<input type='hidden' name='op' title='op' id='op' value='apply_import_form' />";
            $form .= "<input type='submit' class='formButton' name='submit' id='submit' value='" . _AM_XNEWSLETTER_IMPORT_EXEC . "' title='" . _AM_XNEWSLETTER_IMPORT_EXEC . "' />";
            $form .= "</td></tr>";

            $form .=  "</table></form>";
            echo $form;
        }
        break;



    case "apply_import_form":
        //update xnewsletter with settings form_import
        $counter = XnewsletterRequest::getInt('counter', 0);

        for ($i=1; $i < ($counter + 1); ++$i) {
            $import_id        = XnewsletterRequest::getString("import_id_{$i}", 'default');
            $subscr_firstname = XnewsletterRequest::getString("firstname_{$i}", '');
            $subscr_lastname  = XnewsletterRequest::getString("lastname_{$i}", '');
            $subscr_sex       = XnewsletterRequest::getString("sex_{$i}", '');
            $cat_id           = XnewsletterRequest::getInt("cat_id_{$i}", 0);

            if ($cat_id > 0) {
                if ($subscr_id == 0) {
                    //update sex, firstname, lastname
                    $sql = "UPDATE {$xoopsDB->prefix('xnewsletter_import')}";
                    $sql .= " SET `import_sex`='{$subscr_sex}', `import_firstname`='{$subscr_firstname}', `import_lastname`='{$subscr_lastname}'";
                    $sql .= " WHERE `import_id`={$import_id}";
                    $result=$xoopsDB->queryF($sql);
                }
            }
            //update cat_id and import_status
            $sql = "UPDATE {$xoopsDB->prefix('xnewsletter_import')}";
            $sql .= " SET `import_cat_id`='{$cat_id}', `import_status`=1";
            $sql .= " WHERE `import_id`={$import_id}";
            $result=$xoopsDB->queryF($sql);
        }

        redirect_header("{$currentFile}?op=exec_import_final&check_import=1&limitcheck={$limitcheck}", 0, '');
        break;



    case "exec_import_final":
        //execute final import of all data from xnewsletter_import, where import_status = 1
        //delete data from xnewsletter_import, when imported (successful or not)
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, $currentFile, 'list');
        echo $indexAdmin->renderButton();
        //
        $ip = xoops_getenv("REMOTE_ADDR");
        $submitter = $xoopsUser->uid();

        $importCriteria = new CriteriaCompo();
        $importCriteria->add(new Criteria('import_status', '1'));
        $numrows_total     = $xnewsletter->getHandler('import')->getCount();
        $numrows_act     = $xnewsletter->getHandler('import')->getCount($importCriteria);
        if ($numrows_act > 0) {
            $sql = "SELECT *";
            $sql .= " FROM {$xoopsDB->prefix("xnewsletter_import")}";
            $sql .= " WHERE ((import_status)=1)";
            $sql .= " ORDER BY `import_id` ASC";
            $counter = 0;
            if(!$users_import = $xoopsDB->queryF($sql)) die ("MySQL-Error: " . mysql_error());
            while ($user_import = mysql_fetch_assoc($users_import)) {
                $import_id = $user_import["import_id"];
                $subscr_email = $user_import["import_email"];
                $subscr_firstname = $user_import["import_firstname"];
                $subscr_lastname = $user_import["import_lastname"];
                $subscr_sex = $user_import["import_sex"];
                $cat_id = $user_import["import_cat_id"];
                $subscr_id = $user_import["import_subscr_id"];
                $catsubscr_id = $user_import["import_catsubscr_id"];
                $subscribe = 0;

                if ($cat_id == 0) {
                    createProtocol(str_replace("%e", $subscr_email, _AM_XNEWSLETTER_IMPORT_RESULT_SKIP), 1, $submitter);
                } else {
                    //register email
                    if ($subscr_id==0) {
                        $subscr_uid = 0;
                        $sql = "SELECT `uid`";
                        $sql .= " FROM {$xoopsDB->prefix('users')}";
                        $sql .= " WHERE (`email`='{$subscr_email}') LIMIT 1";
                        if ($user = $xoopsDB->queryF($sql)) {
                            $row_user = mysql_fetch_array($user);
                            $subscr_uid = $row_user[0];
                        }
                        unset($row_user);
                        unset($user);

                        $sql = "INSERT";
                        $sql .= " INTO `{$xoopsDB->prefix('xnewsletter_subscr')}`";
                        $sql .= " (`subscr_email`, `subscr_firstname`, `subscr_lastname`, `subscr_uid`, `subscr_sex`, `subscr_submitter`, `subscr_created`, `subscr_ip`, `subscr_activated`, `subscr_actoptions`)";
                        $sql .= " VALUES ('{$subscr_email}', '{$subscr_firstname}', '{$subscr_lastname}', " . intval($subscr_uid) . ", '{$subscr_sex}', {$submitter}, " . time() . ",'{$ip}', '1', '')";
                        if (!$xoopsDB->queryF($sql)) {
                            createProtocol(str_replace("%e", $subscr_email, _AM_XNEWSLETTER_IMPORT_RESULT_FAILED), 0, $submitter);
                        } else {
                            //register email successful
                            $resulttext = $subscr_email . ": " . _AM_XNEWSLETTER_IMPORT_RESULT_REG_OK . " | ";
                            $subscr_id = $xoopsDB->getInsertId();
                            $subscribe=1;
                        }
                    } else {
                        //email already registered
                        $resulttext = $subscr_email . ": "  ._AM_XNEWSLETTER_IMPORT_EMAIL_EXIST . " | ";
                        $subscribe = 1;
                    }
                    if ($subscribe == 1) {
                        if ($catsubscr_id == 0) {
                        //add subscription of this email
                        $sql = "INSERT";
                        $sql .= " INTO `{$xoopsDB->prefix('xnewsletter_catsubscr')}`";
                        $sql .= " (`catsubscr_catid`, `catsubscr_subscrid`, `catsubscr_submitter`, `catsubscr_created`)";
                        $sql .= " VALUES ({$cat_id}, {$subscr_id}, {$submitter}," . time() . ")";
                        if ($xoopsDB->queryF($sql)) {
                            createProtocol($resulttext . _AM_XNEWSLETTER_IMPORT_RESULT_SUBSCR_OK, 1, $submitter);
                            //handle mailinglists
                            $cat_mailinglist = 0;
                            $sql = "SELECT `cat_mailinglist`";
                            $sql .= " FROM {$xoopsDB->prefix("xnewsletter_cat")}";
                            $sql .= " WHERE (`cat_id`={$cat_id}) LIMIT 1";
                            if ($cat_mls = $xoopsDB->queryF($sql)) {
                                $cat_ml = mysql_fetch_array($cat_mls);
                                $cat_mailinglist = $cat_ml[0];
                            }
                            unset($cat_ml);
                            unset($cat_mls);

                            if ($cat_mailinglist > 0) {
                                require_once(XOOPS_ROOT_PATH . "/modules/xnewsletter/include/mailinglist.php");
                                subscribingMLHandler(1, $subscr_id, $cat_mailinglist);
                            }
                        } else {
                            createProtocol(str_replace("%e", $subscr_email, _AM_XNEWSLETTER_IMPORT_RESULT_FAILED), 0, $submitter);
                            }
                        } else {
                            createProtocol($resulttext . _AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST, 1, $submitter);
                        }
                    }
                }
                $sql_del = "DELETE";
                $sql_del .= " FROM {$xoopsDB->prefix('xnewsletter_import')}";
                $sql_del .= " WHERE `import_id`={$import_id}";
                $result = $xoopsDB->queryF($sql_del);
            }

            echo "<div style='margin-top:20px;margin-bottom:20px;color:#ff0000;font-weight:bold;font-size:14px'>";
            $resulttext = str_replace("%p", $numrows_act ,_AM_XNEWSLETTER_IMPORT_FINISHED);
            $resulttext = str_replace("%t", $numrows_total ,$resulttext);
            echo XNEWSLETTER_IMG_OK . $resulttext;
            echo "</div>";

            $numrows_pend = $xnewsletter->getHandler('xnewsletter_import')->getCount();
            if ($numrows_pend > 0) {
                $form_continue = "<form id='form_continue' enctype='multipart/form-data' method='post' action='{$currentFile}' name='form_continue'>";
                $form_continue .= "<input id='submit' class='formButton' type='submit' title='" . _AM_XNEWSLETTER_IMPORT_CONTINUE . "' value='" . _AM_XNEWSLETTER_IMPORT_CONTINUE . "' name='submit'>";
                $form_continue .= '<input id="limitcheck" type="hidden" value="' . $limitcheck . '" name="limitcheck">';
                if ($check_import==1) {
                    //show next form for check settings
                    $form_continue .= '<input id="op" type="hidden" value="show_formcheck" name="op">';
                } else {
                    // set import_status = 1 for next package
                    $sql_update = "UPDATE " . $xoopsDB->prefix("xnewsletter_import")." SET `import_status`=1 ORDER BY import_id LIMIT " . $limitcheck;
                    $xoopsDB->queryF($sql_update);
                    //execute import for the next package
                    $form_continue .= '<input id="op" type="hidden" value="exec_import_final" name="op">';
                }
                $form_continue .= '<input id="action_after_read" type="hidden" value="' . $action_after_read . '" name="action_after_read">';
                $form_continue .= '<input id="limitcheck" type="hidden" value="' . $limitcheck . '" name="limitcheck">';
                $form_continue .= '<input id="plugin" type="hidden" value="' . $plugin . '" name="plugin">';
                $form_continue .= '<input id="check_import" type="hidden" value="' . $check_import . '" name="check_import">';
                $form_continue .= "</form>";
                echo $form_continue;
            }
        } else {
            echo _AM_XNEWSLETTER_IMPORT_NODATA;
        }
        break;



    case "searchdata":
        //delete all existing data, import data into xnewsletter_import with plugin
        //set cat_id as preselected, update information about existing registration/subscriptions
        //if ($action_after_read==1) execute import else show form for check before executing import

        $pluginFile = XNEWSLETTER_ROOT_PATH . "/plugins/{$plugin}.php";
        if (!file_exists($pluginFile)) {
            echo str_replace("%p", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_PLUGIN);
            break;
        }
        require_once($pluginFile);

        $function = 'xnewsletter_plugin_getdata_' . $plugin;
        if (!function_exists($function)) {
            echo "Error: require function 'xnewsletter_plugin_getdata_{$plugin}' doesn't exist";
            echo str_replace("%f", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_FUNCTION);
            break;
        }

        //delete all existing data
        $sql = "TRUNCATE TABLE " . $xoopsDB->prefix('xnewsletter_import');
        $result= $xoopsDB->queryF($sql);

        //import data into xnewsletter_import with plugin
        if ($plugin == 'csv') {
            $csv_file = $_FILES['csv_file']['tmp_name'];
            $csv_header    = XnewsletterRequest::getInt('csv_header', 0);
            $csv_delimiter = XnewsletterRequest::getString('csv_delimiter', ',');
            //$numData = $function($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist, $csv_file, $csv_delimiter, $csv_header);
            $numData = call_user_func($function, $cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist, $csv_file, $csv_delimiter, $csv_header);
        } else if ($plugin == 'xoopsuser') {
            $arr_groups = $_POST["xoopsuser_group"];
            //$numData = $function($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist, $arr_groups);
            $numData = call_user_func($function, $cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist, $arr_groups);
        } else {
            //$numData = $function($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist);
            $numData = call_user_func($function, $cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist);
        }

        if ($numData > 0) {
            if ($action_after_read == 0) {
                //execute import without check
                redirect_header("{$currentFile}?op=exec_import_final&action_after_read=0&limitcheck={$limitcheck}", 0, '');
            } else {
                //show form for check before executing import
                redirect_header("{$currentFile}?op=show_formcheck&action_after_read=1&plugin={$plugin}&limitcheck={$limitcheck}", 0, '');
            }
        } else {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_IMPORT_NODATA);
        }
        break;



    case "form_additional":
        //show form for additional settings
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, $currentFile, 'list');
        echo $indexAdmin->renderButton();
        //
        $pluginFile = XNEWSLETTER_ROOT_PATH . "/plugins/{$plugin}.php";
        if (!file_exists($pluginFile)) {
            echo str_replace("%p", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_PLUGIN);
            break;
        }
        require_once($pluginFile);

        $function = "xnewsletter_plugin_getform_{$plugin}";
        if (!function_exists($function)) {
            echo str_replace("%f", $plugin, _AM_XNEWSLETTER_IMPORT_ERROR_NO_FUNCTION);
            break;
        }
        //$form = $function( $cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist );
        $form = call_user_func($function, $cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist);
        $form->display();
        break;



    case "default":
    default:
        //show basic search form
        $importObj = $xnewsletter->getHandler('import')->create();
        $form = $importObj->getSearchForm($plugin, $action_after_read, $limitcheck);
        $form->display();
        break;
}
include_once __DIR__ . '/admin_footer.php';

/**
 * @param $prot_text
 * @param $success
 * @param $submitter
 */
function createProtocol($prot_text, $success, $submitter) {
    global $xoopsDB;
    $sql = "INSERT INTO `{$xoopsDB->prefix('xnewsletter_protocol')}`";
    $sql .= " (`protocol_letter_id`, `protocol_subscriber_id`, `protocol_status`, `protocol_success`, `protocol_submitter`, `protocol_created`)";
    $sql .= " VALUES (0,0,'{$prot_text}', {$success}, {$submitter}, " . time() . ")";
    if(!$xoopsDB->queryF($sql)) die ("MySQL-Error: " . mysql_error());
}
