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
 *  @copyright  Goffy ( wedega.com )
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_csv() {
    global $xoopsDB;

    $pluginInfo = [];
    $pluginInfo['name'] = 'csv';
    $pluginInfo['icon'] = XNEWSLETTER_URL . '/plugins/csv.png';
    //$pluginInfo['modulepath'] = XNEWSLETTER_ROOT_PATH . "/plugins/csv.php";
    $pluginInfo['tables'][0] = '';
    $pluginInfo['descr'] = 'Import CSV';
    $pluginInfo['hasform'] = 1;

    return $pluginInfo;
}

/**
 * @param $cat_id
 * @param $action_after_read
 * @param $limitCheck
 * @param $skipCatsubscrExist
 * @param $file
 * @param $delimiter
 * @param $header
 *
 * @return int
 */
function xnewsletter_plugin_getdata_csv($cat_id, $action_after_read, $limitCheck, $skipCatsubscrExist = true, $file, $delimiter, $header = true) {
    global $xoopsDB;
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    //$table_import = $xoopsDB->prefix('xnewsletter_import');
    $import_status = $action_after_read == 0 ? 1 : 0;
    $i = 0;
    $j = 0;

    if (($handle = fopen($file, 'r')) !== false) {
        while (($lineArray = fgetcsv($handle, 4000, $delimiter)) !== false) {
            if ($header == true && $i == 0) {
                // remove header line
                // NOP
            } else {
                $email     = $lineArray[0];
                $sex       = isset($lineArray[1]) ? $lineArray[1] : '';
                $firstname = isset($lineArray[2]) ? $lineArray[2] : '';
                $lastname  = isset($lineArray[3]) ? $lineArray[3] : '';
                if (!empty($email)) {
                    $subscr_id = xnewsletter_pluginCheckEmail($email);
                    $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);

                    if ($skipCatsubscrExist == true && $catsubscr_id > 0) {
                        //skip existing subscriptions
                        // NOP
                    } else {
                        $current_cat_id = $catsubscr_id > 0 ? 0 : $cat_id;
                        $importObj = $xnewsletter->getHandler('import')->create();
                        $importObj->setVar('import_email', $email);
                        $importObj->setVar('import_sex', $sex);
                        $importObj->setVar('import_firstname', $firstname);
                        $importObj->setVar('import_lastname', $lastname);
                        $importObj->setVar('import_cat_id', $current_cat_id);
                        $importObj->setVar('import_subscr_id', $subscr_id);
                        $importObj->setVar('import_catsubscr_id', $catsubscr_id);
                        $importObj->setVar('import_status', $import_status);
                        if (!$xnewsletter->getHandler('import')->insert($importObj)) {
                            echo $importObj->getHtmlErrors();
                            exit();
                        }
//                    $sql = "INSERT INTO {$table_import} (import_email, import_sex, import_firstname, import_lastname, import_cat_id, import_subscr_id, import_catsubscr_id, import_status)";
//                    $sql .= " VALUES ('$email', '$sex', '$firstname', '$lastname', $current_cat_id, $subscr_id, $catsubscr_id, $import_status)";
//                    $result_insert = $xoopsDB->query($sql) || die ("MySQL-Error: " . $xoopsDB->error());
                        ++$j;
                    }
                }
            }
            ++$i;
            if ($j == 100000) break; //maximum number of processing to avoid cache overflow
            if ($limitCheck > 0 && $j == $limitCheck) $import_status = 0;
        }
    fclose($handle);
    } else {
        exit('FILE NOT FOUND');
    }

    return $j;
}

/**
 * @param      $cat_id
 * @param      $action_after_read
 * @param      $limitCheck
 * @param      $skipCatsubscrExist
 * @param bool $action
 *
 * @return XoopsThemeForm
 */
function xnewsletter_plugin_getform_csv( $cat_id, $action_after_read, $limitCheck, $skipCatsubscrExist, $action = false) {
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }

    $title = _AM_XNEWSLETTER_IMPORT_CSV_OPT;

    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $form = new XoopsThemeForm($title, 'form_add_csv', $action, 'post', true);
    $form->setExtra('enctype="multipart/form-data"');

    $form->addElement(new XoopsFormLabel('Info', _AM_XNEWSLETTER_IMPORT_CSV));

    //limit file size 16 MB
    $form->addElement(new XoopsFormFile(_AM_XNEWSLETTER_IMPORT_CSV_FILE, 'csv_file', '16777216'), true);
    $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_IMPORT_CSV_DELIMITER, 'csv_delimiter', 10, 1, ','), true);
    $form->addElement(new XoopsFormRadioYN(_AM_XNEWSLETTER_IMPORT_CSV_HEADER, 'csv_header', 1, _YES, _NO), false);

    $form->addElement(new XoopsFormHidden('plugin', 'csv'));
    $form->addElement(new XoopsFormHidden('cat_id', $cat_id));
    $form->addElement(new XoopsFormHidden('action_after_read', $action_after_read));
    $form->addElement(new XoopsFormHidden('limitcheck', $limitCheck));
    $form->addElement(new XoopsFormHidden('skipcatsubscrexist', $skipCatsubscrExist));
    $form->addElement(new XoopsFormHidden('op', 'searchdata'));
    $form->addElement(new XoopsFormButton('', 'submit', _AM_XNEWSLETTER_IMPORT_CONTINUE, 'submit'));

    return $form;
}
