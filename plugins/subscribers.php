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
 * ****************************************************************************
 */

use XoopsModules\Xnewsletter;

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once dirname(__DIR__) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_subscribers()
{
    global $xoopsDB;

    $pluginInfo         = [];
    $pluginInfo['name'] = 'subscribers';
    if (file_exists(XOOPS_URL . '/modules/subscribers/images/module_logo.gif')) {
        $pluginInfo['icon'] = XOOPS_URL . '/modules/subscribers/images/module_logo.gif';
    } elseif (file_exists(XOOPS_URL . '/modules/subscribers/assets/images/logo_module.png')) {
        $pluginInfo['icon'] = XOOPS_URL . '/modules/subscribers/assets/images/logo_module.png';
    }
    //$pluginInfo['modulepath'] = XOOPS_ROOT_PATH . "/modules/subscribers/xoops_version.php";
    $pluginInfo['tables'][0] = $xoopsDB->prefix('subscribers_user');
    $pluginInfo['descr']     = 'Import from subscribers';
    $pluginInfo['hasform']   = 0;

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
function xnewsletter_plugin_getdata_subscribers($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist)
{
    global $xoopsDB;
    $helper = Xnewsletter\Helper::getInstance();

    //$table_import = $xoopsDB->prefix('xnewsletter_import');
    $import_status = 0 == $action_after_read ? true : false;
    $i             = 0;
    $j             = 0;

    $sql = 'SELECT `user_email`, `user_name`';
    $sql .= ' FROM ' . $xoopsDB->prefix('subscribers_user');
    $sql .= " WHERE (`user_email` is not null and not(`user_email`=''))";
    if (!$result_users = $xoopsDB->query($sql)) {
        die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
    }
    while ($lineArray = $xoopsDB->fetchBoth($result_users)) {
        ++$i;
        $email     = $lineArray[0];
        $sex       = '';
        $firstname = '';
        $lastname  = $lineArray[1];

        $subscr_id    = xnewsletter_pluginCheckEmail($email);
        $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);

        if (1 == $skipcatsubscrexist && $catsubscr_id > 0) {
            //skip existing subscriptions
        } else {
            $currcatid = $catsubscr_id > 0 ? 0 : $cat_id;
            $importObj = $helper->getHandler('Import')->create();
            $importObj->setVar('import_email', $email);
            $importObj->setVar('import_sex', $sex);
            $importObj->setVar('import_firstname', $firstname);
            $importObj->setVar('import_lastname', $lastname);
            $importObj->setVar('import_cat_id', $currcatid);
            $importObj->setVar('import_subscr_id', $subscr_id);
            $importObj->setVar('import_catsubscr_id', $catsubscr_id);
            $importObj->setVar('import_status', $import_status);
            if (!$helper->getHandler('Import')->insert($importObj)) {
                echo $importObj->getHtmlErrors();
                exit();
            }
            //            $sql = "INSERT INTO {$table_import} (import_email, import_sex, import_firstname, import_lastname, import_cat_id, import_subscr_id, import_catsubscr_id, import_status)";
            //            $sql .= " VALUES ('$email', '$sex', '$firstname', '$lastname', $currcatid, $subscr_id, $catsubscr_id, $import_status)";
            //            $result_insert = $xoopsDB->query($sql) or die ("MySQL-Error: " . $GLOBALS['xoopsDB']->error());
            ++$j;
        }
        ++$i;
        if (100000 == $j) {
            break;
        } //maximum number of processing to avoid cache overflow
        if ($limitcheck > 0 && $j == $limitcheck) {
            $import_status = false;
        }
    }

    return $j;
}
