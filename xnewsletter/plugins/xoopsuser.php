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
// defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_xoopsuser()
{
    $pluginInfo         = array();
    $pluginInfo['name'] = 'xoopsuser';
    $pluginInfo['icon'] = XNEWSLETTER_URL . '/plugins/system_user.png';
    //$pluginInfo['modulepath'] = XNEWSLETTER_ROOT_PATH . '/plugins/xoopsuser.php';
    $pluginInfo['tables'][0] = $GLOBALS['xoopsDB']->prefix('users');
    $pluginInfo['tables'][1] = $GLOBALS['xoopsDB']->prefix('groups_users_link');
    $pluginInfo['descr']     = 'Import XoopsUser';
    $pluginInfo['hasform']   = 1;

    return $pluginInfo;
}

/**
 * @param $cat_id
 * @param $checkSubscrsAfterRead
 * @param $checkLimit
 * @param $skipCatsubscrExist
 * @param $arr_groups
 *
 * @return int
 */
function xnewsletter_plugin_getdata_xoopsuser($cat_id, $checkSubscrsAfterRead, $checkLimit, $skipCatsubscrExist, $arr_groups)
{
    $xnewsletter = XnewsletterXnewsletter::getInstance();
    //
    $import_status = ($checkSubscrsAfterRead === false) ? _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE : _XNEWSLETTER_IMPORT_STATUS_TOCHECK;
    $sql = "SELECT `email`, `name`,`uname` FROM {$GLOBALS['xoopsDB']->prefix('groups_users_link')}";
    $sql .= " INNER JOIN {$GLOBALS['xoopsDB']->prefix('users')} ON {$GLOBALS['xoopsDB']->prefix('groups_users_link')}.uid = {$GLOBALS['xoopsDB']->prefix('users')}.uid";
    $sql .= " WHERE ({$GLOBALS['xoopsDB']->prefix('groups_users_link')}.groupid IN (" . implode(',', $arr_groups) . "))";
    $sql .= " GROUP BY `email`, `name`, `uname`";
    if (!$result_users = $GLOBALS['xoopsDB']->query($sql)) {
        die ('MySQL-Error: ' . mysql_error());
    }
    $j = 0;
    $line = 0;
    while ($lineArray = mysql_fetch_array($result_users)) {
        ++$i;
        $email  = $lineArray[0];
        $subscr_id    = xnewsletter_pluginCheckEmail($email);
        $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);
        if ($skipCatsubscrExist === true && $catsubscr_id > 0) {
            //skip existing subscriptions
        } else {
            $currcatid = $catsubscr_id > 0 ? 0 : $cat_id;
            $importObj = $xnewsletter->getHandler('import')->create();
            $importObj->setVar('import_email', $email);
            $importObj->setVar('import_sex', '');
            $importObj->setVar('import_firstname', '');
            $importObj->setVar('import_lastname', ($lineArray[1] == '') ? $lineArray[2] : $lineArray[1]);
            $importObj->setVar('import_cat_id', $currcatid);
            $importObj->setVar('import_subscr_id', $subscr_id);
            $importObj->setVar('import_catsubscr_id', $catsubscr_id);
            $importObj->setVar('import_status', $import_status);
            if (!$xnewsletter->getHandler('import')->insert($importObj)) {
                echo $importObj->getHtmlErrors();
                exit();
            }
            ++$j;
        }
        if ($j == 100000) {
            break;
        } //maximum number of processing to avoid cache overflow
        if ($checkLimit > 0 && $j == $checkLimit) {
            $import_status = _XNEWSLETTER_IMPORT_STATUS_TOCHECK;
        }
    }
    return $j;
}

/**
 * @param $cat_id
 * @param $checkSubscrsAfterRead
 * @param $checkLimit
 * @param $skipCatsubscrExist
 *
 * @return XoopsThemeForm
 */
function xnewsletter_plugin_getform_xoopsuser($cat_id, $checkSubscrsAfterRead = true, $checkLimit, $skipCatsubscrExist = true)
{
    $xnewsletter = XnewsletterXnewsletter::getInstance();
    $member_handler = xoops_gethandler('member');

    $userGroups = $member_handler->getGroupList();

    $title = _AM_XNEWSLETTER_IMPORT_XOOPSUSER;

    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $form = new XoopsThemeForm($title, 'form_add_xoopsuser', 'import.php', 'post', true);
    $form->setExtra('enctype="multipart/form-data"');

    $form->addElement(new XoopsFormLabel('Info', _AM_XNEWSLETTER_IMPORT_INFO));

    $catCriteria = new CriteriaCompo();
    $catCriteria->setSort('cat_id ASC, cat_name');
    $catCriteria->setOrder('ASC');
    $cat_select = new XoopsFormSelect(_AM_XNEWSLETTER_IMPORT_PRESELECT_CAT, 'cat_id', $cat_id);
    $cat_select->addOptionArray($xnewsletter->getHandler('cat')->getList($catCriteria));
    $form->addElement($cat_select, false);

    // checkboxes other groups
    $select_groups = new XoopsFormCheckBox(_AM_XNEWSLETTER_IMPORT_XOOPSUSER_GROUPS, 'xoopsuser_group', 0);
    foreach ($userGroups as $group_id => $group_name) {
        if ($group_id != XOOPS_GROUP_ANONYMOUS) {
            $select_groups->addOption($group_id, $group_name);
        }
    }
    $form->addElement($select_groups, true);
    $form->addElement(new XoopsFormHidden('plugin', 'xoopsuser'));
    $form->addElement(new XoopsFormHidden('cat_id', $cat_id));
    $form->addElement(new XoopsFormHidden('checkSubscrsAfterRead', $checkSubscrsAfterRead));
    $form->addElement(new XoopsFormHidden('checkLimit', $checkLimit));
    $form->addElement(new XoopsFormHidden('skipcatsubscrexist', $skipCatsubscrExist));
    $form->addElement(new XoopsFormHidden('op', 'searchdata'));
    $form->addElement(new XoopsFormButton('', 'submit', _AM_XNEWSLETTER_IMPORT_CONTINUE, 'submit'));
    return $form;
}
