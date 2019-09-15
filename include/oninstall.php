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
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/common.php';
//@require XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $xoopsConfig['language'] . '/admin.php';
xoops_loadLanguage('admin', 'xnewsletter');

define('INDEX_FILE_PATH', XOOPS_UPLOAD_PATH . '/index.html');
define('BLANK_FILE_PATH', XOOPS_UPLOAD_PATH . '/blank.gif');

/**
 * @param \XoopsModule $module
 *
 * @return bool
 */
function xoops_module_pre_install_xnewsletter(\XoopsModule $module)
{
    // NOP
    return true;
}

/**
 * @param \XoopsModule $module
 *
 * @return bool
 */
function xoops_module_install_xnewsletter(\XoopsModule $module)
{
    // get module config values
    $hModConfig  = xoops_getHandler('config');
    $configArray = $hModConfig->getConfigsByCat(0, $module->getVar('mid'));

    //Creation of folder "uploads" for the module to the site root
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the file accounts in uploads directory
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter/accounts';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the file cat in uploads directory
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter/cat';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the file subscr in uploads directory
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter/subscr';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the file catsubscr in uploads directory
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter/catsubscr';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the file letter in uploads directory
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter/letter';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the file protocol in uploads directory
    $path = XOOPS_ROOT_PATH . '/uploads/xnewsletter/protocol';
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    //Creation of the folder letter_attachment in uploads directory for files
    $path = XOOPS_ROOT_PATH . '/uploads' . $configArray['xn_attachment_path'];
    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
    chmod($path, 0777);
    copy(INDEX_FILE_PATH, $path . '/index.html');

    return true;
}
