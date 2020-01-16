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
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :   svn:$id$
 * ****************************************************************************
 */
// Common
if (!defined('_CO_XNEWSLETTER_WARNING_NOPERMISSIONS')) {
    define('_CO_XNEWSLETTER_WARNING_NOPERMISSIONS', 'Achtung: keine ausreichenden Berechtigungen!');
}
$moduleDirName = basename(dirname(dirname(__DIR__)));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);
$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
if (!defined($moduleDirNameUpper . '_AUTHOR_LOGOIMG')) {
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', $pathIcon32 . '/xoopsmicrobutton.gif');
}
//Latest Version Check
if (!defined('CO_' . $moduleDirNameUpper . '_' . 'NEW_VERSION')) {
    define('CO_' . $moduleDirNameUpper . '_' . 'NEW_VERSION', 'Neue Version: ');
}
if (!defined('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_FEEDBACK')) {
    //Menu
    define('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_FEEDBACK', 'Feedback');
}








