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
function xnewsletter_plugin_getinfo_rmbulletin() {
    global $xoopsDB;

    $pluginInfo = [];
    $pluginInfo['name'] = 'rmbulletin';
    $pluginInfo['icon'] = XOOPS_URL . '/modules/rmbulletin/images/logo.png';
    //$pluginInfo['modulepath'] = XOOPS_ROOT_PATH . "/modules/rmbulletin/xoops_version.php";
    $pluginInfo['tables'][0] = $xoopsDB->prefix('rmb_users');
    $pluginInfo['descr'] = 'Import from RM-Bulletin';
    $pluginInfo['hasform'] = 0;

    return $pluginInfo;
}

/**
 * @param $cat_id
 * @param $action_after_read
 * @param $limitcheck
 * @param $skipcatsubscrexist
 *
 * @return int
 */
function xnewsletter_plugin_getdata_rmbulletin($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist) {
    global $xoopsDB;
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    //$table_import = $xoopsDB->prefix('xnewsletter_import');
    $import_status = $action_after_read == 0 ? 1 : 0;
    $i = 0;
    $j = 0;

    $sql = 'SELECT `email`';
    $sql .= ' FROM ' . $xoopsDB->prefix('rmb_users');
    if(!$result_users = $xoopsDB->query($sql)) die ('MySQL-Error: ' . $xoopsDB->error());
    while ($lineArray = $xoopsDB->fetchBoth($result_users)) {
        ++$i;
        $email     = $lineArray[0];
        $sex       = '';
        $firstname = '';
        $lastname  = '';

        $subscr_id = xnewsletter_pluginCheckEmail($email);
        $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);

        if ($skipcatsubscrexist == 1 && $catsubscr_id > 0) {
            //skip existing subscriptions
        } else {
            $currcatid = $catsubscr_id > 0 ? 0 : $cat_id;
            $importObj = $xnewsletter->getHandler('import')->create();
            $importObj->setVar('import_email', $email);
            $importObj->setVar('import_sex', $sex);
            $importObj->setVar('import_firstname', $firstname);
            $importObj->setVar('import_lastname', $lastname);
            $importObj->setVar('import_cat_id', $currcatid);
            $importObj->setVar('import_subscr_id', $subscr_id);
            $importObj->setVar('import_catsubscr_id', $catsubscr_id);
            $importObj->setVar('import_status', $import_status);
            if (!$xnewsletter->getHandler('import')->insert($importObj)) {
                echo $importObj->getHtmlErrors();
                exit();
            }
//            $sql = "INSERT INTO {$table_import} (import_email, import_sex, import_firstname, import_lastname, import_cat_id, import_subscr_id, import_catsubscr_id, import_status)";
//            $sql .= " VALUES ('$email', '$sex', '$firstname', '$lastname', $currcatid, $subscr_id, $catsubscr_id, $import_status)";
//            $result_insert = $xoopsDB->query($sql) || die ("MySQL-Error: " . $xoopsDB->error());
            ++$j;
        }
        ++$i;
        if ($j == 100000) break; //maximum number of processing to avoid cache overflow
        if ($limitcheck > 0 && $j == $limitcheck) $import_status = 0;
    }

    return $j;
}
