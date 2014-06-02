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
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

include "admin_header.php";
xoops_cp_header();

//global $indexAdmin;

//count "total"
$count_cat = $xnewsletter->getHandler('xNewsletter_cat')->getCount();
$count_accounts = $xnewsletter->getHandler('xNewsletter_accounts')->getCount();

$count_subscr = $xnewsletter->getHandler('xNewsletter_subscr')->getCount();
$count_catsubscr = $xnewsletter->getHandler('xNewsletter_catsubscr')->getCount();
$count_letter = $xnewsletter->getHandler('xNewsletter_letter')->getCount();
$count_protocol = $xnewsletter->getHandler('xNewsletter_protocol')->getCount();
$count_attachment = $xnewsletter->getHandler('xNewsletter_attachment')->getCount();
if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
    $count_mailinglist = $xnewsletter->getHandler('xNewsletter_mailinglist')->getCount();
}
$count_bmh = $xnewsletter->getHandler('xNewsletter_bmh')->getCount();
if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $count_task = $xnewsletter->getHandler('xNewsletter_task')->getCount();
}

define('_RED', '#FF0000'); // red color
define('_GREEN', '#00AA00'); // green color

$indexAdmin->addInfoBox(_AM_XNEWSLETTER_LETTER);

$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_ACCOUNTS, $count_accounts, ($count_accounts == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_CAT, $count_cat, ($count_cat == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_SUBSCR, $count_subscr, ($count_subscr == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_CATSUBSCR, $count_catsubscr, ($count_catsubscr == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_LETTER, $count_letter, ($count_letter == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_ATTACHMENT, $count_attachment, ($count_attachment == 0) ? _RED : _GREEN);
$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_PROTOCOL, $count_protocol, ($count_protocol == 0) ? _RED : _GREEN);

if ($xnewsletter->getConfig('xn_use_mailinglist') == 1) {
    $indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_MAILINGLIST, $count_mailinglist, ($count_mailinglist == 0) ? _RED : _GREEN);
}

if ($xnewsletter->getConfig('xn_send_in_packages') > 0) {
    $indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_TASK, $count_task, ($count_task == 0) ? _RED : _GREEN);
}

$indexAdmin->addInfoBoxLine(_AM_XNEWSLETTER_LETTER, _AM_XNEWSLETTER_THEREARE_BMH, $count_bmh, ($count_bmh == 0) ? _RED : _GREEN);

echo $indexAdmin->addNavigation("index.php") ;
echo $indexAdmin->renderIndex();

include "admin_footer.php";
