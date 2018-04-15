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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :
 * ****************************************************************************
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once  dirname(__DIR__) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_xoopsuser()
{
    global $xoopsDB;

    $pluginInfo         = [];
    $pluginInfo['name'] = 'xoopsuser';
    $pluginInfo['icon'] = XNEWSLETTER_URL . '/plugins/system_user.png';
    //$pluginInfo['modulepath'] = XNEWSLETTER_ROOT_PATH . "/plugins/xoopsuser.php";
    $pluginInfo['tables'][0] = $xoopsDB->prefix('users');
    $pluginInfo['tables'][1] = $xoopsDB->prefix('groups_users_link');
    $pluginInfo['descr']     = 'Import \XoopsUser';
    $pluginInfo['hasform']   = 1;

    return $pluginInfo;
}

/**
 * @param $cat_id
 * @param $action_after_read
 * @param $limitcheck
 * @param $skipCatsubscrExist
 * @param $arr_groups
 *
 * @return int
 */
function xnewsletter_plugin_getdata_xoopsuser(
    $cat_id,
    $action_after_read,
    $limitcheck,
    $skipCatsubscrExist,
    $arr_groups
) {
    global $xoopsDB;
    $xnewsletter = XnewsletterXnewsletter::getInstance();

    //$table_import = $xoopsDB->prefix('xnewsletter_import');
    $import_status = 0 == $action_after_read ? true : false;
    $i             = 0;
    $j             = 0;

    $sql = "SELECT `email`, `name`,`uname` FROM {$xoopsDB->prefix('groups_users_link')}";
    $sql .= " INNER JOIN {$xoopsDB->prefix('users')} ON {$xoopsDB->prefix('groups_users_link')}.uid = {$xoopsDB->prefix('users')}.uid";
    $sql .= " WHERE ({$xoopsDB->prefix('groups_users_link')}.groupid IN (" . implode(',', $arr_groups) . '))';
    $sql .= ' GROUP BY `email`, `name`, `uname`';

    if (!$result_users = $xoopsDB->query($sql)) {
        die('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
    }
    while ($lineArray = $xoopsDB->fetchBoth($result_users)) {
        ++$i;
        $email     = $lineArray[0];
        $sex       = '';
        $firstname = '';
        $lastname  = ('' == $lineArray[1]) ? $lineArray[2] : $lineArray[1];

        $subscr_id    = xnewsletter_pluginCheckEmail($email);
        $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);

        if (1 == $skipCatsubscrExist && $catsubscr_id > 0) {
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
            //            $result_insert = $xoopsDB->query($sql) || die ("MySQL-Error: " . $GLOBALS['xoopsDB']->error());
            ++$j;
        }
        ++$i;
        if (100000 == $j) {
            break;
        } //maximum number of processing to avoid cache overflow
        if ($limitCheck > 0 && $j == $limitCheck) {
            $import_status = false;
        }
    }

    return $j;
}

/**
 * @param $cat_id
 * @param $action_after_read
 * @param $limitCheck
 * @param $skipCatsubscrExist
 *
 * @return XoopsThemeForm
 */
function xnewsletter_plugin_getform_xoopsuser($cat_id, $action_after_read, $limitCheck, $skipCatsubscrExist)
{
    global $xoopsDB;
    $xnewsletter   = XnewsletterXnewsletter::getInstance();
    $memberHandler = xoops_getHandler('member');

    $userGroups = $memberHandler->getGroupList();

    $title = _AM_XNEWSLETTER_IMPORT_XOOPSUSER;

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $form = new \XoopsThemeForm($title, 'form_add_xoopsuser', 'import.php', 'post', true);
    $form->setExtra('enctype="multipart/form-data"');

    $form->addElement(new \XoopsFormLabel('Info', _AM_XNEWSLETTER_IMPORT_INFO));

    $catCriteria = new \CriteriaCompo();
    $catCriteria->setSort('cat_id ASC, cat_name');
    $catCriteria->setOrder('ASC');
    $cat_select = new \XoopsFormSelect(_AM_XNEWSLETTER_IMPORT_PRESELECT_CAT, 'cat_id', $cat_id);
    $cat_select->addOptionArray($xnewsletter->getHandler('cat')->getList($catCriteria));
    $form->addElement($cat_select, false);

    // checkboxes other groups
    $select_groups = new \XoopsFormCheckBox(_AM_XNEWSLETTER_IMPORT_XOOPSUSER_GROUPS, 'xoopsuser_group', 0);
    foreach ($userGroups as $group_id => $group_name) {
        if (XOOPS_GROUP_ANONYMOUS != $group_id) {
            $select_groups->addOption($group_id, $group_name);
        }
    }
    $form->addElement($select_groups, true);

    $form->addElement(new \XoopsFormHidden('plugin', 'xoopsuser'));
    $form->addElement(new \XoopsFormHidden('cat_id', $cat_id));
    $form->addElement(new \XoopsFormHidden('action_after_read', $action_after_read));
    $form->addElement(new \XoopsFormHidden('limitcheck', $limitCheck));
    $form->addElement(new \XoopsFormHidden('skipcatsubscrexist', $skipCatsubscrExist));
    $form->addElement(new \XoopsFormHidden('op', 'searchdata'));
    $form->addElement(new \XoopsFormButton('', 'submit', _AM_XNEWSLETTER_IMPORT_CONTINUE, 'submit'));

    return $form;
}
