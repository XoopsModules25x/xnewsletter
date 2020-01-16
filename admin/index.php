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
$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// Template Index
$templateMain = 'xnewsletter_admin_index.tpl';

// count "total"
$catCount        = $helper->getHandler('Cat')->getCount();
$accountsCount   = $helper->getHandler('Accounts')->getCount();
$subscrCount     = $helper->getHandler('Subscr')->getCount();
$catsubscrCount  = $helper->getHandler('Catsubscr')->getCount();
$letterCount     = $helper->getHandler('Letter')->getCount();
$protocolCount   = $helper->getHandler('Protocol')->getCount();
$attachmentCount = $helper->getHandler('Attachment')->getCount();
if (true === $helper->getConfig('xn_use_mailinglist')) {
    $mailinglistCount = $helper->getHandler('Mailinglist')->getCount();
}
$bmhCount = $helper->getHandler('Bmh')->getCount();
if ($helper->getConfig('xn_send_in_packages') > 0) {
    $taskCount = $helper->getHandler('Task')->getCount();
}
$templatesCount = $helper->getHandler('Template')->getCount();

define('_RED', '#FF0000'); // red color
define('_GREEN', '#00AA00'); // green color

// Info box
$adminObject->addInfoBox(_AM_XNEWSLETTER_LETTER);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_ACCOUNTS, $accountsCount), '', (0 == $accountsCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_CAT, $catCount), '', (0 == $catCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_SUBSCR, $subscrCount), '', (0 == $subscrCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_CATSUBSCR, $catsubscrCount), '', (0 == $catsubscrCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_LETTER, $letterCount), '', (0 == $letterCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_ATTACHMENT, $attachmentCount), '', (0 == $attachmentCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_PROTOCOL, $protocolCount), '', (0 == $protocolCount) ? _RED : _GREEN);
if (true === $helper->getConfig('xn_use_mailinglist')) {
    $adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_MAILINGLIST, $mailinglistCount), '', (0 == $mailinglistCount) ? _RED : _GREEN);
}
if ($helper->getConfig('xn_send_in_packages') > 0) {
    $adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_TASK, $taskCount), '', (0 == $taskCount) ? _RED : _GREEN);
}
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_BMH, $bmhCount), '', (0 == $bmhCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_TEMPLATE, $templatesCount), '', (0 == $templatesCount) ? _RED : _GREEN);

// Config box
$config = include dirname(__DIR__) . '/config/config.php';
$folder = $config->uploadFolders;
foreach (array_keys($folder) as $i) {
    $adminObject->addConfigBoxLine($folder[$i], 'folder');
    $adminObject->addConfigBoxLine([$folder[$i], '777'], 'chmod');
}

if ($accountsCount < 1) {
    $adminObject->addConfigBoxLine(_AM_XNEWSLETTER_THEREARE_NOT_ACCOUNTS);
}

// Render
// display Navigation
$xoopsTpl->assign('navigation', $adminObject->displayNavigation('index.php'));
// display Index();
$xoopsTpl->assign('index', $adminObject->renderIndex($currentFile));

require_once __DIR__ . '/admin_footer.php';

