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
 *  Version : $Id $
 * ****************************************************************************
 */

include_once dirname(dirname(dirname(__FILE__))) . '/mainfile.php';
include_once dirname(__FILE__) . '/include/common.php';

include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
include_once XOOPS_ROOT_PATH . '/include/functions.php';

global $xoopsConfig;

//Load languages
xoops_loadLanguage('admin', $xnewsletter->getModule()->dirname());
xoops_loadLanguage('modinfo', $xnewsletter->getModule()->dirname());
xoops_loadLanguage('main', $xnewsletter->getModule()->dirname());
