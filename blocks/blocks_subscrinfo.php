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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @param $options
 *
 * @return array
 */
function b_xnewsletter_subscrinfo($options) {
    xoops_loadLanguage('modinfo', 'xnewsletter');
    $unique_id = uniqid(mt_rand());
    $subscrinfo = [];
    $subscrinfo['formname'] = "formsubscrinfo_{$unique_id}";
    $subscrinfo['formaction'] = XOOPS_URL . '/modules/xnewsletter/subscription.php';
    $subscrinfo['infotext'] = _MI_XNEWSLETTER_SUBSCRINFO_TEXT_BLOCK;
    $subscrinfo['buttontext'] = _MI_XNEWSLETTER_SUBSCRIBE;

    return $subscrinfo;
}
