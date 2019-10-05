<?php
/*
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
 * ****************************************************************************
 */
 /**
 *  @copyright  Goffy
 *  @link http://wedega.com Wedega
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy <webmaster@wedega.com>
 *
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_simplenewsletter() {
    global $xoopsDB;

    $pluginInfo = [];
    $pluginInfo['name'] = 'simplenewsletter';
    $pluginInfo['icon'] = XOOPS_URL . '/modules/simplenewsletter/images/news_subscribe.png';
    $pluginInfo['tables'][0] = $xoopsDB->prefix('simplenewsletter_members');
    $pluginInfo['descr'] = 'Import from simplenewsletter';
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
function xnewsletter_plugin_getdata_simplenewsletter($cat_id, $action_after_read, $limitcheck, $skipcatsubscrexist) {
    global $xoopsDB;
    $helper = \XoopsModules\Xnewsletter\Helper::getInstance();

    $import_status = (0 == $action_after_read) ? 1 : 0;
    $i = 0;
    $j = 0;

    $sql = 'SELECT `member_email`, `member_firstname`, `member_lastname`';
    $sql .= " FROM {$xoopsDB->prefix('simplenewsletter_members')}";
    $sql .= " WHERE (`member_email` IS NOT NULL AND NOT(`member_email`=''))";
    if (!$result_users = $xoopsDB->query($sql)) die ('MySQL-Error: ' . $xoopsDB->error());
    while ($lineArray = $xoopsDB->fetchBoth($result_users)) {
        ++$i;
        $email     = substr($lineArray[0], 0, 100); // only allow 1st 100 chars
        $email     = checkEmail($email);  // make sure truncated string is valid email addr
        $email     = (false === $email) ? '' : $email;
        $sex       = '';
        $firstname = substr($lineArray[1], 0, 100); // only allow 1st 100 chars
        $lastname  = substr($lineArray[2], 0, 100); // only allow 1st 100 chars

        $subscr_id    = xnewsletter_pluginCheckEmail($email);
        $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);

        if (((1 == $skipcatsubscrexist) && ($catsubscr_id > 0)) || ('' == $email)) {
            //skip existing subscriptions
        } else {
//        if (((1 != $skipcatsubscrexist) || ($catsubscr_id <= 0)) && ('' != $email)) {
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
            ++$j;
        }
        ++$i;
        if (100000 == $j) break; //maximum number of processing to avoid cache overflow
        if ($limitcheck > 0 && $j == $limitcheck) $import_status = 0;
    }

    return $j;
}
