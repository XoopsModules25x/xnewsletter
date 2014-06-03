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
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_smartpartner()
{
    global $xoopsDB;

    $pluginInfo         = array();
    $pluginInfo['name'] = "smartpartner";
    if (file_exists(XOOPS_URL . "/modules/smartpartner/images/module_logo.gif")) {
        $pluginInfo['icon'] = XOOPS_URL . "/modules/smartpartner/images/module_logo.gif";
    } elseif (file_exists(XOOPS_URL . "/modules/smartpartner/assets/images/module_logo.png")) {
        $pluginInfo['icon'] = XOOPS_URL . "/modules/smartpartner/assets/images/module_logo.png";
    }
    //$pluginInfo['modulepath'] = XOOPS_ROOT_PATH . "/modules/smartpartner/xoops_version.php";
    $pluginInfo['tables'][0] = $xoopsDB->prefix("smartpartner_partner");
    $pluginInfo['descr']     = "Import from Smartpartner";
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
function xnewsletter_plugin_getdata_smartpartner($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist) {
    global $xoopsDB;
    $xnewsletter = xNewsletterxNewsletter::getInstance();

    $table_import = $xoopsDB->prefix('xnewsletter_import');
    $import_status = $action_after_read == 0 ? 1 : 0;
    $i = 0;
    $j = 0;

    $sql = "SELECT `contact_email`, `contact_name`";
    $sql .= " FROM " . $xoopsDB->prefix("smartpartner_partner");
    $sql .= " WHERE (`contact_email` is not null and not(`contact_email`=''))";
    $result_users = $xoopsDB->query($sql) || die ("MySQL-Error: " . mysql_error());
    while ($lineArray = mysql_fetch_array($result_users)) {
        ++$i;
        $email     = $lineArray[0];
        $sex       = "";
        $firstname = "";
        $lastname  = $lineArray[1];

        $subscr_id = xNewsletter_pluginCheckEmail($email);
        $catsubscr_id = xNewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);

        if ($skipcatsubscrexist == 1 && $catsubscr_id > 0) {
            //skip existing subscriptions
        } else {
            $currcatid = $catsubscr_id > 0 ? 0 : $cat_id;
            $importObj = $xnewsletter->getHandler('xNewsletter_import')->create();
            $importObj->setVar('import_email', $email);
            $importObj->setVar('import_sex', $sex);
            $importObj->setVar('import_firstname', $firstname);
            $importObj->setVar('import_lastname', $lastname);
            $importObj->setVar('import_cat_id', $currcatid);
            $importObj->setVar('import_subscr_id', $subscr_id);
            $importObj->setVar('import_catsubscr_id', $catsubscr_id);
            $importObj->setVar('import_status', $import_status);
            if (!$xnewsletter->getHandler('xNewsletter_import')->insert($importObj)) {
                echo $importObj->getHtmlErrors();
                exit();
            }
//            $sql = "INSERT INTO {$table_import} (import_email, import_sex, import_firstname, import_lastname, import_cat_id, import_subscr_id, import_catsubscr_id, import_status)";
//            $sql .= " VALUES ('$email', '$sex', '$firstname', '$lastname', $currcatid, $subscr_id, $catsubscr_id, $import_status)";
//            $result_insert = $xoopsDB->query($sql) || die ("MySQL-Error: " . mysql_error());
            ++$j;
        }
        ++$i;
        if ($j == 100000) break; //maximum number of processing to avoid cache overflow
        if ($limitcheck > 0 && $j == $limitcheck) $import_status = 0;
    }

    return $j;
}
