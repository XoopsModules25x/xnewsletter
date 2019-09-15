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
 * ****************************************************************************
 */

use Xmf\Request;
use XoopsModules\Xnewsletter;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/header.php';

$op = \Xmf\Request::getString('op', 'welcome');

switch ($op) {
    case 'welcome':
    default:
        $GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_index.tpl";
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $xoopsTpl->assign('welcome_message', $helper->getConfig('welcome_message'));
        $xoopsTpl->assign('xnewsletter_content', _MA_XNEWSLETTER_WELCOME); // this definition is not removed for backward compatibility issues
        break;
}
require_once __DIR__ . '/footer.php';
