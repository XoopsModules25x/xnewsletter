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

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// count 'total'
$catCount        = $xnewsletter->getHandler('cat')->getCount();
$accountsCount   = $xnewsletter->getHandler('accounts')->getCount();
$subscrCount     = $xnewsletter->getHandler('subscr')->getCount();
$catsubscrCount  = $xnewsletter->getHandler('catsubscr')->getCount();
$letterCount     = $xnewsletter->getHandler('letter')->getCount();
$protocolCount   = $xnewsletter->getHandler('protocol')->getCount();
$attachmentCount = $xnewsletter->getHandler('attachment')->getCount();
$templateCount = $xnewsletter->getHandler('template')->getCount();
if ($xnewsletter->getConfig('xn_use_mailinglist') == true) {
    $mailinglistCount = $xnewsletter->getHandler('mailinglist')->getCount();
}
$bmhCount = $xnewsletter->getHandler('bmh')->getCount();
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $taskCount = $xnewsletter->getHandler('task')->getCount();
}

define('_RED', '#FF0000'); // red color
define('_GREEN', '#00AA00'); // green color

// Navigation
echo $indexAdmin->addNavigation($currentFile);

// Info box
$indexAdmin->addInfoBox(_AM_XNEWSLETTER_LETTER);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_ACCOUNTS, $accountsCount, ($accountsCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_CAT, $catCount, ($catCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_SUBSCR, $subscrCount, ($subscrCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_CATSUBSCR, $catsubscrCount, ($catsubscrCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_LETTER, $letterCount, ($letterCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_TEMPLATE, $templateCount, ($templateCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_ATTACHMENT, $attachmentCount, ($attachmentCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_PROTOCOL, $protocolCount, ($protocolCount == 0) ? _RED : _GREEN);
if ($xnewsletter->getConfig('xn_use_mailinglist') == true) {
    $indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_MAILINGLIST, $mailinglistCount, ($mailinglistCount == 0) ? _RED : _GREEN);
}
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_TASK, $taskCount, ($taskCount == 0) ? _RED : _GREEN);
}
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_BMH, $bmhCount, ($bmhCount == 0) ? _RED : _GREEN);

// Config box
if ($accountsCount < 1) {
    $indexAdmin->addConfigBoxLine(_AM_XNEWSLETTER_THEREARE_NOT_ACCOUNTS);
}

// Render
echo $indexAdmin->renderIndex();

include_once __DIR__ . '/admin_footer.php';
