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

$currentFile = basename(__FILE__);
include "admin_header.php";
xoops_cp_header();

//count "total"
$catsCount = $xnewsletter->getHandler('cat')->getCount();
$accountssCount = $xnewsletter->getHandler('accounts')->getCount();

$subscrsCount = $xnewsletter->getHandler('subscr')->getCount();
$catsubscrsCount = $xnewsletter->getHandler('catsubscr')->getCount();
$lettersCount = $xnewsletter->getHandler('letter')->getCount();
$protocolsCount = $xnewsletter->getHandler('protocol')->getCount();
$attachmentsCount = $xnewsletter->getHandler('attachment')->getCount();
if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
    $count_mailinglist = $xnewsletter->getHandler('mailinglist')->getCount();
}
$count_bmh = $xnewsletter->getHandler('bmh')->getCount();
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $count_task = $xnewsletter->getHandler('task')->getCount();
}

define('_RED', '#FF0000'); // red color
define('_GREEN', '#00AA00'); // green color

$indexAdmin->addInfoBox(_AM_XNEWSLETTER_LETTER);

$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_ACCOUNTS, $accountssCount, ($accountssCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_CAT, $catsCount, ($catsCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_SUBSCR, $subscrsCount, ($subscrsCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_CATSUBSCR, $catsubscrsCount, ($catsubscrsCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_LETTER, $lettersCount, ($lettersCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_ATTACHMENT, $attachmentsCount, ($attachmentsCount == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_PROTOCOL, $protocolsCount, ($protocolsCount == 0) ? _RED : _GREEN);

if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
    $indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_MAILINGLIST, $count_mailinglist, ($count_mailinglist == 0) ? _RED : _GREEN);
}

if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_TASK, $count_task, ($count_task == 0) ? _RED : _GREEN);
}

$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_BMH, $count_bmh, ($count_bmh == 0) ? _RED : _GREEN);

echo $indexAdmin->addNavigation($currentFile) ;
echo $indexAdmin->renderIndex();

include "admin_footer.php";
