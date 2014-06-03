<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xnewsletter module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xnewsletter
 * @since           1.3
 * @author          Xoops Development Team
 * @version         svn:$id$
 */
// constants for account
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL",     "1");
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL", "2");
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3",         "3");
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP",         "4");
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL",        "5");

define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_INBOX","INBOX");
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_HARDBOX","INBOX.hard");
define("_AM_XNEWSLETTER_ACCOUNTS_TYPE_SOFTBOX","INBOX.soft");

// constants for actions letter
define("_AM_XNEWSLETTER_LETTER_ACTION_VAL_NO",       "0");
define("_AM_XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW",  "1");
define("_AM_XNEWSLETTER_LETTER_ACTION_VAL_SEND",     "2");
define("_AM_XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST", "3");

// constants for bounced mail handler
define("_AM_XNEWSLETTER_BOUNCETYPE_HARD","hard");
define("_AM_XNEWSLETTER_BOUNCETYPE_SOFT","soft");
define("_AM_XNEWSLETTER_BMH_MEASURE_VAL_ALL",     -1);
define("_AM_XNEWSLETTER_BMH_MEASURE_VAL_PENDING",  0);
define("_AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING",  1);
define("_AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT",     2);
define("_AM_XNEWSLETTER_BMH_MEASURE_VAL_DELETE",   3);
