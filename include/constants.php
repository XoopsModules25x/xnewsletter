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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xnewsletter
 * @since           1.3
 * @author          Xoops Development Team
 */
// constants for accounts
define('_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL', 1);
define('_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL', 2);
define('_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3', 3);
define('_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP', 4);
define('_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL', 5);

define('_XNEWSLETTER_ACCOUNTS_TYPE_INBOX', 'INBOX');
define('_XNEWSLETTER_ACCOUNTS_TYPE_HARDBOX', 'INBOX.hard');
define('_XNEWSLETTER_ACCOUNTS_TYPE_SOFTBOX', 'INBOX.soft');

// constants for attachments
define('_XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT', 0);
define('_XNEWSLETTER_ATTACHMENTS_MODE_ASLINK', 1);
define('_XNEWSLETTER_ATTACHMENTS_MODE_AUTO', 2); // for future features

// constants for actions letter
define('_XNEWSLETTER_LETTER_ACTION_VAL_NO', 0);
define('_XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW', 1);
define('_XNEWSLETTER_LETTER_ACTION_VAL_SEND', 2);
define('_XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST', 3);

// constants for catsubscr_quit_now
define('_XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NONE', 0);
define('_XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NOW', 1);
define('_XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_REMOVE', 2);

// constants for bounced mail handler
define('_XNEWSLETTER_BOUNCETYPE_HARD', 'hard');
define('_XNEWSLETTER_BOUNCETYPE_SOFT', 'soft');
define('_XNEWSLETTER_BMH_MEASURE_VAL_ALL', -1);
define('_XNEWSLETTER_BMH_MEASURE_VAL_PENDING', 0);
define('_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING', 1);
define('_XNEWSLETTER_BMH_MEASURE_VAL_QUIT', 2);
define('_XNEWSLETTER_BMH_MEASURE_VAL_DELETE', 3);

// constants for protocol_status_str
define('_XNEWSLETTER_PROTOCOL_STATUS_SAVED', 1);
define('_XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK', 2);
define('_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST', 3);
define('_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND', 4);
define('_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND', 5);
// IN PROGRESS
