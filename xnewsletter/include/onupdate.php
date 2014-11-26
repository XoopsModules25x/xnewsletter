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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 *
 * @param      $xoopsModule
 * @param null $previousVersion
 *
 * @return bool
 */

function xoops_module_update_xnewsletter(&$xoopsModule, $previousVersion = null)
{
    if ($previousVersion == 100) {
        xoops_module_update_xnewsletter_101();
    }
    if ($previousVersion < 103) {
        xoops_module_update_xnewsletter_103();
    }
    if ($previousVersion < 104) {
        xoops_module_update_xnewsletter_104();
    }
    if ($previousVersion < 130) {
        xoops_module_update_xnewsletter_130();
    }
    xoops_module_update_xnewsletter_dirtolowercase();

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_dirtolowercase()
{
    // change module dirname to lowercase
    $modulePath = dirname(__DIR__);
    $moduleDirname = basename(dirname(__DIR__));
    if ($moduleDirname != strtolower($moduleDirname)) {
        if (!xnewsletter_copyDir($modulePath, XOOPS_ROOT_PATH . '/modules/' . strtolower($moduleDirname) . '.tmp')) {
            return false;
        }
        if (!xnewsletter_delDir($modulePath)) {
            return false;
        }
        if (!xnewsletter_copyDir(XOOPS_ROOT_PATH . '/modules/' . strtolower($moduleDirname) . '.tmp', XOOPS_ROOT_PATH . '/modules/' . strtolower($moduleDirname))) {
            return false;
        }
        if (!xnewsletter_delDir(XOOPS_ROOT_PATH . '/modules/' . strtolower($moduleDirname) . '.tmp')) {
            return false;
        }
        // update module dirname field in database to lowercase
        $sql = "UPDATE `{$GLOBALS['xoopsDB']->prefix('modules')}` SET `dirname` = '" . strtolower($moduleDirname) . "'";
        $sql .= " WHERE LOWER(`dirname`) = '" . strtolower($moduleDirname) . "';";
        if (!$GLOBALS['xoopsDB']->queryF($sql)) {
            echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": UPDATE 'modules' SET `dirname` = '" . strtolower($moduleDirname) . "'";
            return false;
        }
    } else {
        // NOP
    }
    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_130()
{
    $moduleDirname = basename(dirname(__DIR__));

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
    $sql = sprintf("DROP TABLE IF EXISTS `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_template') . "`");

    // create 'xnewsletter_template' table
    $sql    = sprintf("DROP TABLE IF EXISTS `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_template') . "`");
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _AM_XNEWSLETTER_UPGRADEFAILED . ": 'DROP TABLE 'xnewsletter_template'";
    }

    $sql = "
        CREATE TABLE `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_template') . "` (
        `template_id` int (8)   NOT NULL  auto_increment,
        `template_title` varchar (100)   NOT NULL default '',
        `template_description` text   NOT NULL default '',
        `template_content` text   NOT NULL default '',
        `template_submitter` int (8)   NOT NULL default '0',
        `template_created` int (8)   NOT NULL default '0',
        PRIMARY KEY (`template_id`)
        ) ENGINE=MyISAM;";
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": CREATE TABLE 'xnewsletter_template'";
    }

    // add fields to 'xnewsletter_cat' table
    $sql = "ALTER TABLE `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_cat') . "`";
    $sql .= " ADD COLUMN `dohtml` tinyint(1) NOT NULL default '0',";
    $sql .= " ADD COLUMN `dosmiley` tinyint(1) NOT NULL default '1',";
    $sql .= " ADD COLUMN `doxcode` tinyint(1) NOT NULL default '1',";
    $sql .= " ADD COLUMN `doimage` tinyint(1) NOT NULL default '1',";
    $sql .= " ADD COLUMN `dobr` tinyint(1) NOT NULL default '1';";
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_cat' ADD";
    }

    // add fields to 'xnewsletter_letter' table
    $sql = "ALTER TABLE `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_letter') . "`";
    $sql .= " ADD COLUMN `letter_sender` int(8) NOT NULL default '0',";
    $sql .= " ADD COLUMN `letter_sent` int(10) NOT NULL default '0';";
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_letter' ADD";
    }

    // add fields to 'xnewsletter_attachment' table
    $sql = "ALTER TABLE `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_attachment') . "`";
    $sql .= " ADD COLUMN `attachment_size` int(8) NOT NULL default '0',";
    $sql .= " ADD COLUMN `attachment_mode` int(8) NOT NULL default '0';";
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_attachment' ADD";
    }

    // add fields to 'xnewsletter_protocol' table
    $sql = "ALTER TABLE `" . $GLOBALS['xoopsDB']->prefix('xnewsletter_protocol') . "`";
    $sql .= " ADD COLUMN `protocol_status_str_id` int(8) NOT NULL default '0',";
    $sql .= " ADD COLUMN `protocol_status_vars` text;";
    if (!$GLOBALS['xoopsDB']->queryF($sql))
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ALTER TABLE 'xnewsletter_protocol' ADD";


    // delete old html template files
    $templateDirectory = XOOPS_ROOT_PATH . "/modules/" . $moduleDirname . "/templates/";
    $template_list     = array_diff(@scandir($templateDirectory), array('..', '.'));
    foreach ($template_list as $k => $v) {
        $fileinfo = new SplFileInfo($templateDirectory . $v);
        if ($fileinfo->getExtension() == 'html' && $fileinfo->getFilename() != 'index.html') {
            @unlink($templateDirectory . $v);
        }
    }
    // Load class XoopsFile
    xoops_load('xoopsfile');

    //delete /images directory
    $imagesDirectory = XOOPS_ROOT_PATH . "/modules/" . $moduleDirname . "/images/";
    $folderHandler   = XoopsFile::getHandler("folder", $imagesDirectory);
    $folderHandler->delete($imagesDirectory);

    //delete /templates/style.css file
    $cssFile       = XOOPS_ROOT_PATH . "/modules/" . $moduleDirname . "/templates/style.css";
    $folderHandler = XoopsFile::getHandler("file", $cssFile);
    $folderHandler->delete($cssFile);

    //delete .html entries from the tpl table
    $sql = "DELETE FROM " . $GLOBALS['xoopsDB']->prefix("tplfile") . " WHERE LOWER(`tpl_module`) = '" . strtolower($moduleDirname) . "' AND `tpl_file` LIKE '%.html%'";
    $GLOBALS['xoopsDB']->queryF($sql);

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_104()
{
    $sql    = sprintf("DROP TABLE IF EXISTS `" . $GLOBALS['xoopsDB']->prefix('mod_xnewsletter_task') . "`");
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _AM_XNEWSLETTER_UPGRADEFAILED . ": 'DROP TABLE 'mod_xnewsletter_task'";
    }

    $sql    = sprintf(
        "CREATE TABLE `" . $GLOBALS['xoopsDB']->prefix('mod_xnewsletter_task') . "` (
        `task_id` int(8) NOT NULL AUTO_INCREMENT,
        `task_letter_id` int(8) NOT NULL DEFAULT '0',
        `task_subscr_id` int(8) NOT NULL DEFAULT '0',
        `task_starttime` int(8) NOT NULL DEFAULT '0',
        `task_submitter` int(8) NOT NULL DEFAULT '0',
        `task_created` int(8) NOT NULL DEFAULT '0',
        PRIMARY KEY (`task_id`),
        KEY `idx_task_starttime` (`task_starttime`)
        ) ENGINE=MyISAM;"
    );
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": CREATE TABLE 'mod_xnewsletter_task'";
    }

    unlink(XOOPS_ROOT_PATH . "/modules/xnewsletter/include/sendletter.php");

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_103()
{
    $sql    = sprintf("DROP TABLE IF EXISTS `" . $GLOBALS['xoopsDB']->prefix('mod_xnewsletter_import') . "`");
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": 'DROP TABLE 'mod_xnewsletter_import'";
    }

    $sql    = sprintf(
        "CREATE TABLE `" . $GLOBALS['xoopsDB']->prefix('mod_xnewsletter_import') . "` (
            `import_id` int (8)   NOT NULL  auto_increment,
            `import_email` varchar (100)   NOT NULL default ' ',
            `import_firstname` varchar (100)   NULL default ' ',
            `import_lastname` varchar (100)   NULL default ' ',
            `import_sex` varchar (100)   NULL default ' ',
            `import_cat_id` int (8)   NOT NULL default '0',
            `import_subscr_id` int (8)   NOT NULL default '0',
            `import_catsubscr_id` int (8)   NOT NULL default '0',
            `import_status` tinyint (1)   NOT NULL default '0',
            PRIMARY KEY (`import_id`),
            KEY `idx_email` (`import_email`),
            KEY `idx_subscr_id` (`import_subscr_id`),
            KEY `idx_import_status` (`import_status`)
            ) ENGINE=MyISAM;"
    );
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": CREATE TABLE 'mod_xnewsletter_import'";
    }

    $sql    = sprintf("ALTER TABLE `" . $GLOBALS['xoopsDB']->prefix('mod_xnewsletter_subscr') . "` ADD INDEX `idx_subscr_email` ( `subscr_email` )");
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ADD INDEX `idx_subscr_email`";
    }

    $sql    = sprintf("ALTER TABLE `" . $GLOBALS['xoopsDB']->prefix('mod_xnewsletter_catsubscr') . "` ADD UNIQUE `idx_subscription` ( `catsubscr_catid` , `catsubscr_subscrid` )");
    if (!$GLOBALS['xoopsDB']->queryF($sql)) {
        echo '<br />' . _MI_XNEWSLETTER_UPGRADEFAILED . ": ADD INDEX `idx_subscription`";
    }

    return true;
}

/**
 * @return bool
 */
function xoops_module_update_xnewsletter_101()
{
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
 * @param $tablename
 *
 * @return bool
 */
function xoops_module_update_xnewsletter_rename_table($tablename)
{
    if (tableExists($GLOBALS['xoopsDB']->prefix($tablename))) {
        $sql    = sprintf('ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix($tablename) . ' RENAME ' . $GLOBALS['xoopsDB']->prefix('mod_' . $tablename));
        if (!$GLOBALS['xoopsDB']->queryF($sql)) {
            echo "<br />" . _MI_XNEWSLETTER_UPGRADEFAILED . ": RENAME table '" . $tablename . "'";
        }
    }

    return true;
}

/**
 * @param $tablename
 *
 * @return bool
 */
function xoops_module_update_xnewsletter_rename_mod_table($tablename)
{
    if (tableExists($GLOBALS['xoopsDB']->prefix('mod_' . $tablename))) {
        $sql    = sprintf('ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('mod_' . $tablename) . ' RENAME ' . $GLOBALS['xoopsDB']->prefix($tablename));
        if (!$GLOBALS['xoopsDB']->queryF($sql)) {
            echo "<br />" . _MI_XNEWSLETTER_UPGRADEFAILED . ": RENAME table '" . $tablename . "'";
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
    $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '{$tablename}'");

    return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0);
}

/**
 * Copy a directory and its contents
 *
 * @param   string $source      is the original directory
 * @param   string $destination is the destination directory
 *
 * @return  bool                    Returns true on success or false on failure
 *
 */
function xnewsletter_copyDir($source, $destination)
{
    if (!$dirHandler = opendir($source)) {
        return false;
    }
    @mkdir($destination);
    while (false !== ($file = readdir($dirHandler))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir("{$source}/{$file}")) {
                if (!xnewsletter_copyDir("{$source}/{$file}", "{$destination}/{$file}")) {
                    return false;
                }
            } else {
                if (!copy("{$source}/{$file}", "{$destination}/{$file}")) {
                    return false;
                }
            }
        }
    }
    closedir($dirHandler);
    return true;
}

/**
 * Delete a empty/not empty directory
 *
 * @param   string $dir          path to the directory to delete
 * @param   bool   $if_not_empty if false it delete directory only if false
 *
 * @return  bool                    Returns true on success or false on failure
 */
function xnewsletter_delDir($dir, $if_not_empty = true)
{
    if (!file_exists($dir)) {
        return true;
    }
    if ($if_not_empty == true) {
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!xnewsletter_delDir("{$dir}/{$item}")) {
                return false;
            }
        }
    } else {
        // NOP
    }
    return rmdir($dir);
}