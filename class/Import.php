<?php

namespace XoopsModules\Xnewsletter;

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

//use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Import
 */
class Import extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('import_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('import_email', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('import_firstname', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('import_lastname', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('import_sex', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('import_cat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('import_subscr_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('import_catsubscr_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('import_status', XOBJ_DTYPE_INT, false, false); // boolean
    }

    /**
     * @param      $plugin
     * @param int  $action_after_read
     * @param int  $limitcheck
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getSearchForm($plugin, $action_after_read = 1, $limitcheck = 0, $action = false)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = _AM_XNEWSLETTER_IMPORT_SEARCH;

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form_select_import', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $catCriteria = new \CriteriaCompo();
        $catCriteria->setSort('cat_id ASC, cat_name');
        $catCriteria->setOrder('ASC');
        $cat_select = new \XoopsFormSelect(_AM_XNEWSLETTER_IMPORT_PRESELECT_CAT, 'cat_id', '1');
        $cat_select->addOptionArray($this->helper->getHandler('Cat')->getList($catCriteria));
        $form->addElement($cat_select, false);

        $opt_import_type = new \XoopsFormRadio(_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL, 'plugin', $plugin, '<br>');
        $opt_import_type->setExtra('onclick="document.forms.form_select_import.submit()"');
        $aFiles            = \XoopsLists::getFileListAsArray(XNEWSLETTER_ROOT_PATH . '/plugins/');
        $arrPlugin         = [];
        $currpluginhasform = 0;
        foreach ($aFiles as $file) {
            if ('.php' === mb_substr($file, mb_strlen($file) - 4, 4)) {
                $pluginName = str_replace('.php', '', $file);
                $pluginFile = XNEWSLETTER_ROOT_PATH . '/plugins/' . $pluginName . '.php';
                if (file_exists($pluginFile)) {
                    require_once $pluginFile;
                    $function    = 'xnewsletter_plugin_getinfo_' . $pluginName;
                    $arrPlugin   = $function();
                    $show_plugin = $this->tableExists($arrPlugin['tables'][0]);
                    if (true === $show_plugin && @is_array($arrPlugin['tables'][1])) {
                        $show_plugin = $this->tableExists($arrPlugin['tables'][1]);
                    }

                    if (true === $show_plugin) {
                        $label = "<img src='" . $arrPlugin['icon'] . "' title='" . $arrPlugin['descr'] . "' alt='" . $arrPlugin['descr'] . "' style='height:32px;margin-bottom:5px;margin-right:5px'>" . $arrPlugin['descr'];
                        $opt_import_type->addOption($arrPlugin['name'], $label);
                        $form->addElement(new \XoopsFormHidden('hasform_' . $pluginName, $arrPlugin['hasform']));
                        if ($plugin == $pluginName && 1 == $arrPlugin['hasform']) {
                            $currpluginhasform = 1;
                        }
                    }
                }
            }
        }
        $form->addElement($opt_import_type, false);

        //option, whether data should be shown for check or directly imported
        $check_after = new \XoopsFormRadio(_AM_XNEWSLETTER_IMPORT_AFTER_READ, 'action_after_read', $action_after_read, '<br>');
        $check_after->addOption(0, _AM_XNEWSLETTER_IMPORT_READ_IMPORT);
        $check_after->addOption(1, _AM_XNEWSLETTER_IMPORT_READ_CHECK);
        $check_after->setExtra('onclick="document.forms.form_select_import.submit()"');
        $form->addElement($check_after, false);

        //limit for import
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_IMPORT_CHECK_LIMIT, '100000'), false);
        if (0 == $action_after_read) {
            if ($limitcheck < 500 && $limitcheck > 0) {
                $limitcheck = 500;
            }
        } else {
            if ($limitcheck > 200) {
                $limitcheck = 200;
            }
        }
        $sel_limitcheck = new \XoopsFormSelect(_AM_XNEWSLETTER_IMPORT_CHECK_LIMIT_PACKAGE, 'limitcheck', $limitcheck);
        if (0 == $action_after_read) {
            $sel_limitcheck->addOption(0, _AM_XNEWSLETTER_IMPORT_NOLIMIT);
            $sel_limitcheck->addOption(500, 500);
            $sel_limitcheck->addOption(1000, 1000);
            $sel_limitcheck->addOption(10000, 10000);
            $sel_limitcheck->addOption(25000, 25000);
        } else {
            $limitOptions = [25, 50, 100, 200, 400];
            foreach ($limitOptions as $limitOption) {
                // check if limit options are compatible with php.ini 'max_input_vars' setting
                if ((0 == ini_get('max_input_vars')) || ((($limitOption * 7) + 4) < ini_get('max_input_vars'))) {
                    $sel_limitcheck->addOption($limitOption, $limitOption);
                }
            }
        }
        $form->addElement($sel_limitcheck, false);

        $skip               = 1 == $action_after_read ? 0 : 1;
        $skipcatsubscrexist = new \XoopsFormRadioYN(_AM_XNEWSLETTER_IMPORT_SKIP_EXISTING, 'skipcatsubscrexist', $skip);
        if (0 == $action_after_read) {
            $skipcatsubscrexist->setExtra('disabled="disabled"');
        }
        $form->addElement($skipcatsubscrexist, false);

        $form->addElement(new \XoopsFormHidden('op', 'default'));
        $buttonTray = new \XoopsFormElementTray('', '');
        if (1 == $currpluginhasform) {
            //show form for additional options
            $button1 = new \XoopsFormButton('', 'form_additional', _AM_XNEWSLETTER_IMPORT_CONTINUE, 'submit1');
            $button1->setExtra('onclick="document.getElementById(\'op\').value = \'form_additional\';document.forms.form_select_import.submit()"');
            $buttonTray->addElement($button1);
        } else {
            $button2 = new \XoopsFormButton('', 'searchdata', _AM_XNEWSLETTER_IMPORT_CONTINUE, 'submit2');
            $button2->setExtra('onclick="document.getElementById(\'op\').value = \'searchdata\';document.forms.form_select_import.submit()"');
            $buttonTray->addElement($button2);
        }
        $form->addElement($buttonTray);

        return $form;
    }

    /**
     * @param $tablename
     *
     * @return bool
     */
    private function tableExists($tablename)
    {
        if ('' == $tablename) {
            return true;
        }
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW TABLES LIKE '$tablename'");

        return ($xoopsDB->getRowsNum($result) > 0);
    }
}
