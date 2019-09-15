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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 *
 * @param      $xoopsModule
 * @param null $oldversion
 *
 * @return bool
 */

xoops_loadLanguage('admin', 'xnewsletter');

/**
 * @param \XoopsObject $xoopsModule
 * @param null         $oldversion
 * @return bool
 */
function xoops_module_update_xnewsletter(\XoopsObject $xoopsModule, $oldversion = null)
{
    if (100 == $oldversion) {
        xoops_module_update_xnewsletter_101();
    }
    if ($oldversion < 103) {
        xoops_module_update_xnewsletter_103();
    }
    if ($oldversion < 104) {
        xoops_module_update_xnewsletter_104();
    }
    if ($oldversion < 130) {
        xoops_module_update_xnewsletter_130();
    }

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_130()
{
    // change module dirname to lowercase
    $path    = dirname(__DIR__);
    $dirname = basename(dirname(__DIR__));
    rename($path, mb_strtolower($dirname));
    // update module dirname field in database to lowercase
    global $xoopsDB;
    $sql    = "UPDATE `{$xoopsDB->prefix('modules')}` SET `dirname` = '" . mb_strtolower($dirname) . "'";
    $sql    .= " WHERE LOWER(`dirname`) = '" . mb_strtolower($dirname) . "';";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": UPDATE 'modules' SET `dirname` = '" . mb_strtolower($dirname) . "'";
    }

    // reverse 'mod_' prefix on tables
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_accounts');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_attachment');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_bmh');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_cat');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_catsubscr');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_import');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_letter');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_mailinglist');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_protocol');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_subscr');
    xoops_module_update_xnewsletter_rename_mod_table('xnewsletter_task');
    $sql = sprintf('DROP TABLE IF EXISTS `' . $xoopsDB->prefix('xnewsletter_template') . '`');

    // create 'xnewsletter_template' table
    global $xoopsDB;
    $sql    = sprintf('DROP TABLE IF EXISTS `' . $xoopsDB->prefix('xnewsletter_template') . '`');
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _AM_XNEWSLETTER_UPGRADEFAILED . ": 'DROP TABLE 'xnewsletter_template'";
    }

    $sql    = '
        CREATE TABLE `' . $xoopsDB->prefix('xnewsletter_template') . "` (
        `template_id` INT (8)   NOT NULL  AUTO_INCREMENT,
        `template_title` VARCHAR (100)   NOT NULL DEFAULT '',
        `template_description` TEXT   NOT NULL DEFAULT '',
        `template_content` TEXT   NOT NULL DEFAULT '',
        `template_submitter` INT (8)   NOT NULL DEFAULT '0',
        `template_created` INT (8)   NOT NULL DEFAULT '0',
        PRIMARY KEY (`template_id`)
        ) ENGINE=MyISAM;";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": CREATE TABLE 'xnewsletter_template'";
    }

    // add fields to 'xnewsletter_cat' table
    global $xoopsDB;
    $sql    = 'ALTER TABLE `' . $xoopsDB->prefix('xnewsletter_cat') . '`';
    $sql    .= " ADD COLUMN `dohtml` tinyint(1) NOT NULL default '0',";
    $sql    .= " ADD COLUMN `dosmiley` tinyint(1) NOT NULL default '1',";
    $sql    .= " ADD COLUMN `doxcode` tinyint(1) NOT NULL default '1',";
    $sql    .= " ADD COLUMN `doimage` tinyint(1) NOT NULL default '1',";
    $sql    .= " ADD COLUMN `dobr` tinyint(1) NOT NULL default '1';";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_cat' ADD";
    }

    // add fields to 'xnewsletter_letter' table
    global $xoopsDB;
    $sql    = 'ALTER TABLE `' . $xoopsDB->prefix('xnewsletter_letter') . '`';
    $sql    .= " ADD COLUMN `letter_sender` int(8) NOT NULL default '0',";
    $sql    .= " ADD COLUMN `letter_sent` int(10) NOT NULL default '0';";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_letter' ADD";
    }

    // add fields to 'xnewsletter_attachment' table
    global $xoopsDB;
    $sql    = 'ALTER TABLE `' . $xoopsDB->prefix('xnewsletter_attachment') . '`';
    $sql    .= " ADD COLUMN `attachment_size` int(8) NOT NULL default '0',";
    $sql    .= " ADD COLUMN `attachment_mode` int(8) NOT NULL default '0';";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_attachment' ADD";
    }

    // delete old html template files
    $templateDirectory = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/';
    $template_list     = array_diff(scandir($templateDirectory, SCANDIR_SORT_NONE), ['..', '.']);
    foreach ($template_list as $k => $v) {
        $fileinfo = new \SplFileInfo($templateDirectory . $v);
        if ('html' === $fileinfo->getExtension() && 'index.html' !== $fileinfo->getFilename()) {
            @unlink($templateDirectory . $v);
        }
    }
    // Load class XoopsFile
    xoops_load('xoopsfile');

    //delete /images directory
    $imagesDirectory = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/images/';
    $folderHandler   = XoopsFile::getHandler('folder', $imagesDirectory);
    $folderHandler->delete($imagesDirectory);

    //delete /templates/style.css file
    $cssFile       = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/style.css';
    $folderHandler = XoopsFile::getHandler('file', $cssFile);
    $folderHandler->delete($cssFile);

    //delete .html entries from the tpl table
    $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE LOWER(`tpl_module`) = '" . mb_strtolower($dirname) . "' AND `tpl_file` LIKE '%.html%'";
    $GLOBALS['xoopsDB']->queryF($sql);

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_104()
{
    global $xoopsDB;

    $sql    = sprintf('DROP TABLE IF EXISTS `' . $xoopsDB->prefix('mod_xnewsletter_task') . '`');
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _AM_XNEWSLETTER_UPGRADEFAILED . ": 'DROP TABLE 'mod_xnewsletter_task'";
    }

    $sql    = sprintf('CREATE TABLE `' . $xoopsDB->prefix('mod_xnewsletter_task') . "` (
        `task_id` INT(8) NOT NULL AUTO_INCREMENT,
        `task_letter_id` INT(8) NOT NULL DEFAULT '0',
        `task_subscr_id` INT(8) NOT NULL DEFAULT '0',
        `task_starttime` INT(8) NOT NULL DEFAULT '0',
        `task_submitter` INT(8) NOT NULL DEFAULT '0',
        `task_created` INT(8) NOT NULL DEFAULT '0',
        PRIMARY KEY (`task_id`),
        KEY `idx_task_starttime` (`task_starttime`)
        ) ENGINE=MyISAM;");
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": CREATE TABLE 'mod_xnewsletter_task'";
    }

    unlink(XOOPS_ROOT_PATH . '/modules/xnewsletter/include/sendletter.php');

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_103()
{
    global $xoopsDB;

    $sql    = sprintf('DROP TABLE IF EXISTS `' . $xoopsDB->prefix('mod_xnewsletter_import') . '`');
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": 'DROP TABLE 'mod_xnewsletter_import'";
    }

    $sql    = sprintf('CREATE TABLE `' . $xoopsDB->prefix('mod_xnewsletter_import') . "` (
            `import_id` INT (8)   NOT NULL  AUTO_INCREMENT,
            `import_email` VARCHAR (100)   NOT NULL DEFAULT ' ',
            `import_firstname` VARCHAR (100)   NULL DEFAULT ' ',
            `import_lastname` VARCHAR (100)   NULL DEFAULT ' ',
            `import_sex` VARCHAR (100)   NULL DEFAULT ' ',
            `import_cat_id` INT (8)   NOT NULL DEFAULT '0',
            `import_subscr_id` INT (8)   NOT NULL DEFAULT '0',
            `import_catsubscr_id` INT (8)   NOT NULL DEFAULT '0',
            `import_status` TINYINT (1)   NOT NULL DEFAULT '0',
            PRIMARY KEY (`import_id`),
            KEY `idx_email` (`import_email`),
            KEY `idx_subscr_id` (`import_subscr_id`),
            KEY `idx_import_status` (`import_status`)
            ) ENGINE=MyISAM;");
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": CREATE TABLE 'mod_xnewsletter_import'";
    }

    $sql    = sprintf('ALTER TABLE `' . $xoopsDB->prefix('mod_xnewsletter_subscr') . '` ADD INDEX `idx_subscr_email` ( `subscr_email` )');
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ': ADD INDEX `idx_subscr_email`';
    }

    $sql    = sprintf('ALTER TABLE `' . $xoopsDB->prefix('mod_xnewsletter_catsubscr') . '` ADD UNIQUE `idx_subscription` ( `catsubscr_catid` , `catsubscr_subscrid` )');
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ': ADD INDEX `idx_subscription`';
    }

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_101()
{
    global $xoopsDB;

    //rename tables to new xoops naming scheme
    xoops_module_update_xnewsletter_rename_table('xnewsletter_accounts');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_cat');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_subscr');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_catsubscr');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_letter');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_protocol');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_attachment');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_mailinglist');
    xoops_module_update_xnewsletter_rename_table('xnewsletter_bmh');

    return true;
}

/**
 * @param string $tablename
 *
 * @return bool
 */
function xoops_module_update_xnewsletter_rename_table($tablename)
{
    global $xoopsDB;

    if (tableExists($xoopsDB->prefix($tablename))) {
        $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix($tablename) . ' RENAME ' . $xoopsDB->prefix('mod_' . $tablename));
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": RENAME table '" . $tablename . "'";
            //            ++$errors;
        }
    }

    return true;
}

/**
 * @param string $tablename
 *
 * @return bool
 */
function xoops_module_update_xnewsletter_rename_mod_table($tablename)
{
    global $xoopsDB;

    if (tableExists($xoopsDB->prefix('mod_' . $tablename))) {
        $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('mod_' . $tablename) . ' RENAME ' . $xoopsDB->prefix($tablename));
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            echo '<br>' . _MI_XNEWSLETTER_UPGRADEFAILED . ": RENAME table '" . $tablename . "'";
            //            ++$errors;
        }
    }

    return true;
}

/**
 * @param $tablename
 *
 * @return bool
 */
function tableExists($tablename)
{
    global $xoopsDB;
    $result = $xoopsDB->queryF("SHOW TABLES LIKE '{$tablename}'");

    return ($xoopsDB->getRowsNum($result) > 0);
}
