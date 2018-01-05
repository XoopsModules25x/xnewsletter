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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// count "total"
$catCount        = $xnewsletter->getHandler('cat')->getCount();
$accountsCount   = $xnewsletter->getHandler('accounts')->getCount();
$subscrCount     = $xnewsletter->getHandler('subscr')->getCount();
$catsubscrCount  = $xnewsletter->getHandler('catsubscr')->getCount();
$letterCount     = $xnewsletter->getHandler('letter')->getCount();
$protocolCount   = $xnewsletter->getHandler('protocol')->getCount();
$attachmentCount = $xnewsletter->getHandler('attachment')->getCount();
if (true === $xnewsletter->getConfig('xn_use_mailinglist')) {
    $mailinglistCount = $xnewsletter->getHandler('mailinglist')->getCount();
}
$bmhCount = $xnewsletter->getHandler('bmh')->getCount();
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $taskCount = $xnewsletter->getHandler('task')->getCount();
}

define('_RED', '#FF0000'); // red color
define('_GREEN', '#00AA00'); // green color

// Navigation
$adminObject->displayNavigation($currentFile);

// Info box
$adminObject->addInfoBox(_AM_XNEWSLETTER_LETTER);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_ACCOUNTS, $accountsCount), '', (0 == $accountsCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_CAT, $catCount), '', (0 == $catCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_SUBSCR, $subscrCount), '', (0 == $subscrCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_CATSUBSCR, $catsubscrCount), '', (0 == $catsubscrCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_LETTER, $letterCount), '', (0 == $letterCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_ATTACHMENT, $attachmentCount), '', (0 == $attachmentCount) ? _RED : _GREEN);
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_PROTOCOL, $protocolCount), '', (0 == $protocolCount) ? _RED : _GREEN);
if (true === $xnewsletter->getConfig('xn_use_mailinglist')) {
    $adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_MAILINGLIST, $mailinglistCount), '', (0 == $mailinglistCount) ? _RED : _GREEN);
}
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_TASK, $taskCount), '', (0 == $taskCount) ? _RED : _GREEN);
}
$adminObject->addInfoBoxLine(sprintf(_AM_XNEWSLETTER_THEREARE_BMH, $bmhCount), '', (0 == $bmhCount) ? _RED : _GREEN);

// Config box
if ($accountsCount < 1) {
    $adminObject->addConfigBoxLine(_AM_XNEWSLETTER_THEREARE_NOT_ACCOUNTS);
}

// Render
$adminObject->displayIndex();

require_once __DIR__ . '/admin_footer.php';
