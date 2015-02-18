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

include_once dirname(__DIR__) . '/include/common.php';

/**
 * @return array
 */
function xnewsletter_plugin_getinfo_csv()
{
    $pluginInfo = array();
    $pluginInfo['name'] = 'csv';
    $pluginInfo['icon'] = XNEWSLETTER_URL . '/plugins/csv.png';
    //$pluginInfo['modulepath'] = XNEWSLETTER_ROOT_PATH . '/plugins/csv.php';
    $pluginInfo['tables'][0] = '';
    $pluginInfo['descr']= 'Import CSV';
    $pluginInfo['hasform'] = true;

    return $pluginInfo;
}

/**
 * @param $cat_id
 * @param $checkSubscrsAfterRead
 * @param $checkLimit
 * @param $skipCatsubscrExist
 * @param $csv_file
 * @param $csvDelimiter
 * @param $header
 *
 * @return int
 */
function xnewsletter_plugin_getdata_csv($cat_id, $checkSubscrsAfterRead = true, $checkLimit, $skipCatsubscrExist = true, $csv_file, $csvDelimiter = ',', $csvSkipHeader = true)
{
    $xnewsletter = XnewsletterXnewsletter::getInstance();
    //
    $import_status = ($checkSubscrsAfterRead === false) ? _XNEWSLETTER_IMPORT_STATUS_IMPORTABLE : _XNEWSLETTER_IMPORT_STATUS_TOCHECK;
    if (($fileHandler = fopen($csv_file, 'r')) === false) {
        return false;
    }
    $j = 0;
    $line = 0;
    while (($lineArray = fgetcsv($fileHandler, 4000, $csvDelimiter)) !== false) {
        ++$line;
        if ($csvSkipHeader == true && $line == 1) {
            // skip header/first line
            // NOP
        } else {
            $email = $lineArray[0];
            if (!empty($email)) {
                $subscr_id = xnewsletter_pluginCheckEmail($email);
                $catsubscr_id = xnewsletter_pluginCheckCatSubscr($subscr_id, $cat_id);
                if ($skipCatsubscrExist === true && $catsubscr_id > 0) {
                    // skip existing subscriptions
                    // NOP
                } else {
                    $current_cat_id = $catsubscr_id > 0 ? 0 : $cat_id;
                    $importObj = $xnewsletter->getHandler('import')->create();
                    $importObj->setVar('import_email', $email);
                    $importObj->setVar('import_sex', isset($lineArray[1]) ? $lineArray[1] : '');
                    $importObj->setVar('import_firstname', isset($lineArray[2]) ? $lineArray[2] : '');
                    $importObj->setVar('import_lastname', isset($lineArray[3]) ? $lineArray[3] : '');
                    $importObj->setVar('import_cat_id', $current_cat_id);
                    $importObj->setVar('import_subscr_id', $subscr_id);
                    $importObj->setVar('import_catsubscr_id', $catsubscr_id);
                    $importObj->setVar('import_status', $import_status);
                    if (!$xnewsletter->getHandler('import')->insert($importObj)) {
                        echo $importObj->getHtmlErrors();
                        exit();
                    }
                    ++$j;
                }
            }
        }
        if ($j == 100000) {
            break;
        }
        // maximum number of processing to avoid cache overflow
        if ($checkLimit > 0 && $j == $checkLimit) {
            $import_status = _XNEWSLETTER_IMPORT_STATUS_TOCHECK;
        }
    }
    fclose($fileHandler);
    return $j;
}

/**
 * @param      $cat_id
 * @param      $checkSubscrsAfterRead
 * @param      $checkLimit
 * @param      $skipCatsubscrExist
 * @param bool $action
 *
 * @return XoopsThemeForm
 */
function xnewsletter_plugin_getform_csv($cat_id, $checkSubscrsAfterRead, $checkLimit, $skipCatsubscrExist, $action = false)
{
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
    $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_IMPORT_CSV_DELIMITER, 'csvDelimiter', 10, 1, ','), true);
    $form->addElement(new XoopsFormRadioYN(_AM_XNEWSLETTER_IMPORT_CSV_HEADER, 'csvSkipHeader', true, _YES, _NO), false);

    $form->addElement(new XoopsFormHidden('plugin', 'csv'));
    $form->addElement(new XoopsFormHidden('cat_id', $cat_id));
    $form->addElement(new XoopsFormHidden('checkSubscrsAfterRead', $checkSubscrsAfterRead));
    $form->addElement(new XoopsFormHidden('checkLimit', $checkLimit));
    $form->addElement(new XoopsFormHidden('skipcatsubscrexist', $skipCatsubscrExist));
    $form->addElement(new XoopsFormHidden('op', 'searchdata'));
    $form->addElement(new XoopsFormButton('', 'submit', _AM_XNEWSLETTER_IMPORT_CONTINUE, 'submit'));

    return $form;
}
