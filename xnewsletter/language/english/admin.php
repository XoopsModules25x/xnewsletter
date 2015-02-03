<?php
/**
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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
//General
define('_AM_XNEWSLETTER_FORMOK', "Registered successfully");
define('_AM_XNEWSLETTER_FORMDELOK', "Deleted successfully");
define('_AM_XNEWSLETTER_FORMDELNOTOK', "Error while deleting");
define('_AM_XNEWSLETTER_FORMSUREDEL', "Are you sure you want to delete: <span class='bold red'>%s</span>");
define('_AM_XNEWSLETTER_FORMSUREDEL_LIST', "Are you sure you want to delete all protocol items of: <span class='bold red'>%s</span>");
define('_AM_XNEWSLETTER_FORMSURERENEW', "Are you sure you want renew: <span class='bold red'>%s</span>");
define('_AM_XNEWSLETTER_FORMUPLOAD', "Upload");
define('_AM_XNEWSLETTER_FORMIMAGE_PATH', "File presents in %s");
define('_AM_XNEWSLETTER_FORMACTION', "Action");
define('_AM_XNEWSLETTER_ERROR_NO_VALID_ID', "Error: no valid id!");
define('_AM_XNEWSLETTER_OK', "Successful");
define('_AM_XNEWSLETTER_FAILED', "failed");
define('_AM_XNEWSLETTER_SAVE', "Save");
define('_AM_XNEWSLETTER_DETAILS', "Show details");
define('_AM_XNEWSLETTER_SEARCH', "Search");
define('_AM_XNEWSLETTER_SEARCH_EQUAL', "=");
define('_AM_XNEWSLETTER_SEARCH_CONTAINS', "contains");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPMAIL', "php mail()");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPSENDMAIL', "php sendmail()");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3', "pop before smtp");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP', "smtp");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL', "gmail");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_NOTREQUIRED', "Not required");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_NAME', "My account name");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_YOURNAME', "John Doe");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL', "name@yourdomain.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_USERNAME', "username");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_PWD', "password");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN', "pop3.yourdomain.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN', "110");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT', "mail.yourdomain.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT', "25");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN', "imap.yourdomain.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN', "143");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT', "mail.yourdomain.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT', "25");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_USERNAME', "yourusername@gmail.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN', "imap.gmail.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN', "993");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_IN', "tls");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT', "smtp.gmail.com");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT', "465");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_OUT', "ssl");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK', "Check the settings");
define('_AM_XNEWSLETTER_LETTER_ACTION', "Action after saving");
define('_AM_XNEWSLETTER_LETTER_ACTION_SAVED', "Saved");
define('_AM_XNEWSLETTER_LETTER_ACTION_NO', "No action");
define('_AM_XNEWSLETTER_LETTER_ACTION_COPYNEW', "Copy and new");
define('_AM_XNEWSLETTER_LETTER_ACTION_PREVIEW', "Show preview");
define('_AM_XNEWSLETTER_LETTER_ACTION_SEND', "Send newsletter to all subscribers");
define('_AM_XNEWSLETTER_LETTER_ACTION_RESEND', "Resend newsletter to subscribers, where sending failed");
define('_AM_XNEWSLETTER_LETTER_ACTION_SENDTEST', "Send newsletter for testing");
define('_AM_XNEWSLETTER_LETTER_EMAIL_TEST', "E-mail for testing newsletter");
define('_AM_XNEWSLETTER_LETTER_EMAIL_ALTBODY', "To view the message, please use an HTML compatible email viewer!");
define('_AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID', "Error deleting attachment (invalid attachment id)");
define('_AM_XNEWSLETTER_SEND_SUCCESS', "Newsletter sent");
define('_AM_XNEWSLETTER_SEND_SUCCESS_TEST', "Newsletter sent for test");
define('_AM_XNEWSLETTER_SEND_SUCCESS_NUMBER', "Sending %t newsletter(s) successfully");
define('_AM_XNEWSLETTER_SEND_SUCCESS_ML', "Handle mailing list successfully");
define('_AM_XNEWSLETTER_SEND_SUCCESS_ML_DETAIL', "Sending '%a' to mailing list successfully");
define('_AM_XNEWSLETTER_SEND_ERROR_NUMBER', "Error sending newsletter: %e of %t newsletters not sent");
define('_AM_XNEWSLETTER_SEND_ERROR_PHPMAILER', "Error phpmailer: ");
define('_AM_XNEWSLETTER_SEND_ERROR_NO_EMAIL', "Error: No e-mail-address available");
define('_AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID', "Error: No valid newsletter selected");
define('_AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH', "Error: template path '%p' not found");
define('_AM_XNEWSLETTER_SEND_SURE_SENT', "This newsletter was already sent to all subscribers.<br />Do you really want to send this newsletter again to all subscribers?");
define('_AM_XNEWSLETTER_SEND_ERROR_NO_SUBSCR', "Error: No valid subscriptions for the selected newsletter(s) found");
//Index
define('_AM_XNEWSLETTER_LETTER', "Newsletter Statistics");
define('_AM_XNEWSLETTER_THEREARE_ACCOUNTS', "There are <span class='bold'>%s</span> Email accounts in the Database");
define('_AM_XNEWSLETTER_THEREARE_CAT', "There are <span class='bold'>%s</span> Categories in the Database");
define('_AM_XNEWSLETTER_THEREARE_SUBSCR', "There are <span class='bold'>%s</span> Subscribers in the Database");
define('_AM_XNEWSLETTER_THEREARE_CATSUBSCR', "There are <span class='bold'>%s</span> Categories-Subscribers in the Database");
define('_AM_XNEWSLETTER_THEREARE_LETTER', "There are <span class='bold'>%s</span> Newsletter in the Database");
define('_AM_XNEWSLETTER_THEREARE_PROTOCOL', "There are <span class='bold'>%s</span> Protocol in the Database");
define('_AM_XNEWSLETTER_THEREARE_ATTACHMENT', "There are <span class='bold'>%s</span> Attachment in the Database");
define('_AM_XNEWSLETTER_THEREARE_MAILINGLIST', "There are <span class='bold'>%s</span> Mailinglist in the Database");
define('_AM_XNEWSLETTER_THEREARE_BMH', "There are <span class='bold'>%s</span> Bounce Mails in the Database");
define('_AM_XNEWSLETTER_THEREARE_TASK', "There are <span class='bold'>%s</span> Task in the Database");
//Buttons
define('_AM_XNEWSLETTER_NEWACCOUNTS', "Add New Email account");
define('_AM_XNEWSLETTER_ACCOUNTSLIST', "List Email accounts");
define('_AM_XNEWSLETTER_ACCOUNTSWAIT', "Pending Email accounts");
define('_AM_XNEWSLETTER_NEWCAT', "Add New Category");
define('_AM_XNEWSLETTER_CATLIST', "List Categories");
define('_AM_XNEWSLETTER_CATWAIT', "Pending Categories");
define('_AM_XNEWSLETTER_NEWSUBSCR', "Add New Subscriber");
define('_AM_XNEWSLETTER_SUBSCRLIST', "List Subscribers");
define('_AM_XNEWSLETTER_SUBSCRWAIT', "Pending Subscribers");
define('_AM_XNEWSLETTER_NEWCATSUBSCR', "Add New Categories-Subscribers");
define('_AM_XNEWSLETTER_CATSUBSCRLIST', "List Categories-Subscribers");
define('_AM_XNEWSLETTER_CATSUBSCRWAIT', "Pending Categories-Subscribers");
define('_AM_XNEWSLETTER_NEWLETTER', "Add New Newsletter");
define('_AM_XNEWSLETTER_LETTERLIST', "List Newsletters");
define('_AM_XNEWSLETTER_LETTERWAIT', "Pending Newsletters");
define('_AM_XNEWSLETTER_LETTER_DELETE_ALL', "Delete protocol of this newsletter");
define('_AM_XNEWSLETTER_NEWPROTOCOL', "Add New Protocol");
define('_AM_XNEWSLETTER_PROTOCOLLIST', "List Protocol");
define('_AM_XNEWSLETTER_PROTOCOLWAIT', "Pending Protocol");
define('_AM_XNEWSLETTER_NEWATTACHMENT', "Add New Attachment");
define('_AM_XNEWSLETTER_ATTACHMENTLIST', "List Attachment");
define('_AM_XNEWSLETTER_ATTACHMENTWAIT', "Pending Attachment");
define('_AM_XNEWSLETTER_NEWMAILINGLIST', "Add New Mailinglist");
define('_AM_XNEWSLETTER_MAILINGLISTLIST', "List Mailinglist");
define('_AM_XNEWSLETTER_MAILINGLISTWAIT', "Pending Mailinglist");
define('_AM_XNEWSLETTER_RUNBMH', "Run Bounced email handler");
define('_AM_XNEWSLETTER_BMHLIST', "List Bounced email handlers");
define('_AM_XNEWSLETTER_BMHWAIT', "Pending Bounced email handlers");
define('_AM_XNEWSLETTER_ACCOUNTS_ADD', "Add an Email account");
define('_AM_XNEWSLETTER_ACCOUNTS_EDIT', "Edit an Email account");
define('_AM_XNEWSLETTER_ACCOUNTS_DELETE', "Delete an Email account");
define('_AM_XNEWSLETTER_ACCOUNTS_ID', "ID");
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE', "Type");
define('_AM_XNEWSLETTER_ACCOUNTS_NAME', "Name");
define('_AM_XNEWSLETTER_ACCOUNTS_YOURNAME', "Your name");
define('_AM_XNEWSLETTER_ACCOUNTS_YOURMAIL', "Your mail");
define('_AM_XNEWSLETTER_ACCOUNTS_USERNAME', "Username");
define('_AM_XNEWSLETTER_ACCOUNTS_PASSWORD', "Password");
define('_AM_XNEWSLETTER_ACCOUNTS_INCOMING', "Incoming");
define('_AM_XNEWSLETTER_ACCOUNTS_SERVER_IN', "Server incoming");
define('_AM_XNEWSLETTER_ACCOUNTS_PORT_IN', "Port in");
define('_AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_IN', "Secure type in");
define('_AM_XNEWSLETTER_ACCOUNTS_OUTGOING', "Outgoing");
define('_AM_XNEWSLETTER_ACCOUNTS_SERVER_OUT', "Server outgoing");
define('_AM_XNEWSLETTER_ACCOUNTS_PORT_OUT', "Port out");
define('_AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_OUT', "Secure type out");
define('_AM_XNEWSLETTER_ACCOUNTS_DEFAULT', "Default email account");
define('_AM_XNEWSLETTER_ACCOUNTS_BOUNCE_INFO', "Additional info for Bounced emails handling");
define('_AM_XNEWSLETTER_ACCOUNTS_USE_BMH', "Use Bounced emails handling");
define('_AM_XNEWSLETTER_ACCOUNTS_INBOX', "Mailbox to check for Bounced emails");
define('_AM_XNEWSLETTER_ACCOUNTS_HARDBOX', "Use this mailbox as 'hard box'");
define('_AM_XNEWSLETTER_ACCOUNTS_HARDBOX_DESC', "The mailbox name must start with 'INBOX.'. You can select a standard folder in your mailbox (e.g. INBOX.Trash) or create your own special folders like 'hard' and 'soft'. If you type in a new folder name, the folder will be created (this function is not available for gmail-accounts).");
define('_AM_XNEWSLETTER_ACCOUNTS_MOVEHARD', "Move Bounced email in 'hard box'");
define('_AM_XNEWSLETTER_ACCOUNTS_SOFTBOX', "Use this mailbox as 'soft box'");
define('_AM_XNEWSLETTER_ACCOUNTS_MOVESOFT', "Move Bounced email in 'soft box'");
define('_AM_XNEWSLETTER_ACCOUNTS_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_ACCOUNTS_CREATED', "Created on");
define('_AM_XNEWSLETTER_ACCOUNTS_ERROR_OPEN_MAILBOX', "Error open mailbox! Please check your settings!");
define('_AM_XNEWSLETTER_SAVE_AND_CHECK', "Save and check settings");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_OK', "successful  ");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED', "failed  ");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_SKIPPED', "skipped");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK', "Check result");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_INFO', "Additional info");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX', "Open mailbox ");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS', "Open folders ");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH', "Bounced email handler ");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_INBOX', "Mailbox");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_HARDBOX', "Hardbox");
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_SOFTBOX', "Softbox");
define('_AM_XNEWSLETTER_CAT_ADD', "Add a category");
define('_AM_XNEWSLETTER_CAT_EDIT', "Edit a category");
define('_AM_XNEWSLETTER_CAT_DELETE', "Delete a category");
define('_AM_XNEWSLETTER_CAT_ID', "ID");
define('_AM_XNEWSLETTER_CAT_NAME', "Newsletter name");
define('_AM_XNEWSLETTER_CAT_INFO', "Description");
define('_AM_XNEWSLETTER_CAT_GPERMS_CREATE', "Permissions to create");
define('_AM_XNEWSLETTER_CAT_GPERMS_CREATE_DESC', "<br /><span style='font-weight:normal'>- Create new newsletters<br />- Edit, delete, send of own newsletters</span>");
define('_AM_XNEWSLETTER_CAT_GPERMS_ADMIN', "Permission to admin");
define('_AM_XNEWSLETTER_CAT_GPERMS_ADMIN_DESC', "<br /><span style='font-weight:normal'>Edit, delete, send of all newsletters of this category</span>");
define('_AM_XNEWSLETTER_CAT_GPERMS_READ', "Permissions to read/subscribe");
define('_AM_XNEWSLETTER_CAT_GPERMS_LIST', "Permissions to see list of subscribers");
define('_AM_XNEWSLETTER_CAT_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_CAT_CREATED', "Created on");
define('_AM_XNEWSLETTER_CAT_MAILINGLIST', "Mailing list");
define('_AM_XNEWSLETTER_SUBSCR_ADD', "Add a Subscriber");
define('_AM_XNEWSLETTER_SUBSCR_EDIT', "Edit a Subscriber");
define('_AM_XNEWSLETTER_SUBSCR_DELETE', "Delete a Subscriber");
define('_AM_XNEWSLETTER_SUBSCR_ID', "ID");
define('_AM_XNEWSLETTER_SUBSCR_EMAIL', "Email");
define('_AM_XNEWSLETTER_SUBSCR_FIRSTNAME', "First name");
define('_AM_XNEWSLETTER_SUBSCR_LASTNAME', "Last name");
define('_AM_XNEWSLETTER_SUBSCR_UID', "Member name");
define('_AM_XNEWSLETTER_SUBSCR_SEX', "Salutation");
define('_AM_XNEWSLETTER_SUBSCR_SEX_EMPTY', "");
define('_AM_XNEWSLETTER_SUBSCR_SEX_MALE', "Mr.");
define('_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE', "Mrs.");
define('_AM_XNEWSLETTER_SUBSCR_SEX_FAMILY', "Family");
define('_AM_XNEWSLETTER_SUBSCR_SEX_COMP', "Company");
define('_AM_XNEWSLETTER_SUBSCR_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_SUBSCR_CREATED', "Created on");
define('_AM_XNEWSLETTER_SUBSCR_ACTIVATED', "activated?");
define('_AM_XNEWSLETTER_SUBSCR_SHOW_ALL', "Show all");
define('_AM_XNEWSLETTER_CATSUBSCR_ADD', "Add a subscriber to a category");
define('_AM_XNEWSLETTER_CATSUBSCR_EDIT', "Edit a a subscriber to a category");
define('_AM_XNEWSLETTER_CATSUBSCR_DELETE', "Delete a subscriber to a category");
define('_AM_XNEWSLETTER_CATSUBSCR_ID', "ID");
define('_AM_XNEWSLETTER_CATSUBSCR_CATID', "Newsletter");
define('_AM_XNEWSLETTER_CATSUBSCR_SUBSCRID', "Subscribers");
define('_AM_XNEWSLETTER_CATSUBSCR_QUITED', "Unsubscribed");
define('_AM_XNEWSLETTER_CATSUBSCR_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_CATSUBSCR_CREATED', "Created on");
define('_AM_XNEWSLETTER_CATSUBSCR_SUREDELETE', "Do you really want to delete<br />'%s'<br />from<br />'%c' ?");
define('_AM_XNEWSLETTER_CATSUBSCR_QUIT_NONE', "None");
define('_AM_XNEWSLETTER_CATSUBSCR_QUIT_NOW', "Quit now");
define('_AM_XNEWSLETTER_CATSUBSCR_QUIT_REMOVE', "Remove quit date");
define('_AM_XNEWSLETTER_LETTER_ADD', "Add a newsletter");
define('_AM_XNEWSLETTER_LETTER_EDIT', "Edit a newsletter");
define('_AM_XNEWSLETTER_LETTER_DELETE', "Delete a newsletter");
define('_AM_XNEWSLETTER_LETTER_ID', "ID");
define('_AM_XNEWSLETTER_LETTER_TITLE', "Title");
define('_AM_XNEWSLETTER_LETTER_CONTENT', "Content");
define('_AM_XNEWSLETTER_LETTER_TEMPLATE', "Template");
define('_AM_XNEWSLETTER_LETTER_CATS', "Categories");
define('_AM_XNEWSLETTER_LETTER_ATTACHMENT', "Attachments");
define('_AM_XNEWSLETTER_LETTER_STATUS', "Status");
define('_AM_XNEWSLETTER_LETTER_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_LETTER_CREATED', "Created on");
define('_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL', "Available email accounts");
define('_AM_XNEWSLETTER_LETTER_ACCOUNT', "Email account");
define('_AM_XNEWSLETTER_LETTER_MAILINGLIST', "Use mailing list");
define('_AM_XNEWSLETTER_LETTER_MAILINGLIST_NO', "None");
define('_AM_XNEWSLETTER_ATTACHMENT_ADD', "Add an attachment");
define('_AM_XNEWSLETTER_ATTACHMENT_EDIT', "Edit an attachment");
define('_AM_XNEWSLETTER_ATTACHMENT_DELETE', "Delete an attachment");
define('_AM_XNEWSLETTER_ATTACHMENT_ID', "ID");
define('_AM_XNEWSLETTER_ATTACHMENT_LETTER_ID', "Letter-ID");
define('_AM_XNEWSLETTER_ATTACHMENT_NAME', "Name");
define('_AM_XNEWSLETTER_ATTACHMENT_TYPE', "File type");
define('_AM_XNEWSLETTER_ATTACHMENT_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_ATTACHMENT_CREATED', "Created on");
define('_AM_XNEWSLETTER_PROTOCOL_ADD', "Add a Protocol");
define('_AM_XNEWSLETTER_PROTOCOL_EDIT', "Edit a Protocol");
define('_AM_XNEWSLETTER_PROTOCOL_DELETE', "Delete a Protocol");
define('_AM_XNEWSLETTER_PROTOCOL_ID', "ID");
define('_AM_XNEWSLETTER_PROTOCOL_LETTER_ID', "Newsletter-ID");
define('_AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID', "Subscriber-ID");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS', "Status");
define('_AM_XNEWSLETTER_PROTOCOL_SUCCESS', "Success");
define('_AM_XNEWSLETTER_PROTOCOL_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_PROTOCOL_CREATED', "Created on");
define('_AM_XNEWSLETTER_PROTOCOL_LAST_STATUS', "Last status");
define('_AM_XNEWSLETTER_PROTOCOL_MISC', "Misc protocol items");
define('_AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL', "No email of recipient found");
define('_AM_XNEWSLETTER_MAILINGLIST_ADD', "Add a Mailinglist");
define('_AM_XNEWSLETTER_MAILINGLIST_EDIT', "Edit a Mailinglist");
define('_AM_XNEWSLETTER_MAILINGLIST_DELETE', "Delete a Mailinglist");
define('_AM_XNEWSLETTER_MAILINGLIST_ID', "ID");
define('_AM_XNEWSLETTER_MAILINGLIST_NAME', "Name");
define('_AM_XNEWSLETTER_MAILINGLIST_EMAIL', "Email");
define('_AM_XNEWSLETTER_MAILINGLIST_EMAIL_DESC', "Email, where subscription code should be sending to");
define('_AM_XNEWSLETTER_MAILINGLIST_LISTNAME', "Listname");
define('_AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE', "Subscribe code");
define('_AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE_DESC', "{email} will be replaced by the email of the subscriber");
define('_AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE', "Unsubscribe code");
define('_AM_XNEWSLETTER_MAILINGLIST_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_MAILINGLIST_CREATED', "Created on");
define('_AM_XNEWSLETTER_BOUNCETYPE', "Bounce type");
define('_AM_XNEWSLETTER_BMH_EDIT', "Edit a Bounced email handler");
define('_AM_XNEWSLETTER_BMH_DELETE', "Delete a Bounced email handler");
define('_AM_XNEWSLETTER_BMH_ID', "ID");
define('_AM_XNEWSLETTER_BMH_RULE_NO', "Rule no");
define('_AM_XNEWSLETTER_BMH_RULE_CAT', "Rule cat");
define('_AM_XNEWSLETTER_BMH_BOUNCETYPE', "Bounce type");
define('_AM_XNEWSLETTER_BMH_REMOVE', "Removed");
define('_AM_XNEWSLETTER_BMH_EMAIL', "Email");
define('_AM_XNEWSLETTER_BMH_SUBJECT', "Subject");
define('_AM_XNEWSLETTER_BMH_MEASURE', "Measure");
define('_AM_XNEWSLETTER_BMH_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_BMH_CREATED', "Created on");
define('_AM_XNEWSLETTER_BMH_MEASURE_PENDING', "Pending");
define('_AM_XNEWSLETTER_BMH_MEASURE_NOTHING', "Do nothing");
define('_AM_XNEWSLETTER_BMH_MEASURE_QUIT', "Quit this subscriber temporary");
define('_AM_XNEWSLETTER_BMH_MEASURE_DELETE', "Delete this subscriber");
define('_AM_XNEWSLETTER_BMH_MEASURE_QUITED', "Subscriber temporary quit");
define('_AM_XNEWSLETTER_BMH_MEASURE_DELETED', "Subscriber deleted");
define('_AM_XNEWSLETTER_BMH_MEASURE_ALREADY_DELETED', "Subscriber already deleted! Action not possible!");
define('_AM_XNEWSLETTER_BMH_MEASURE_DELETE_SURE', "Do you really want to delete this registration with all subscriptions?<br /><br />Reactivating by the subscriber will not be possible later!<br /><br />");
define('_AM_XNEWSLETTER_BMH_ERROR_NO_SUBSCRID', "There is nor existing registration for the this email!");
define('_AM_XNEWSLETTER_BMH_ERROR_NO_ACTIVE', "Bounced email handler isn't activated in any email account");
define('_AM_XNEWSLETTER_BMH_RSLT', "Result of checking mailbox %b<br />Messages read: %r<br />Action taken: %a<br />No action taken: %n<br />Moved: %m<br />Deleted: %d<br /><br /><br />");
define('_AM_XNEWSLETTER_BMH_SUCCESSFUL', "Bounced email handler successfully finished");
define('_AM_XNEWSLETTER_BMH_MEASURE_ALL', "Show all");
define('_AM_XNEWSLETTER_BMH_MEASURE_SHOW_NONE', "No Bounced email items for measure '%s' available");
define('_AM_XNEWSLETTER_MAINTENANCE_CAT', "Category");
define('_AM_XNEWSLETTER_MAINTENANCE_DESCR', "Description");
define('_AM_XNEWSLETTER_MAINTENANCE_PARAM', "Parameter");
define('_AM_XNEWSLETTER_MAINTENANCE_ERROR', "Error while running maintenance");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEDATE', "Delete all registrations without confirmation, where registration was before this date.<br />Attention: there is no undo possible! Please check date before!");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEUSER', "Should these <b>%s</b> unconfirmed registrations with date before %s really deleted.<br />Attention: there is no undo possible!");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOCOL', "Delete all protocols and reset tables");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOK', "Table protocol maintained.");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETENOTHING', "No action necessary.");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEUSEROK', "%s Users have been deleted");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR', "Delete subscriptions to newsletter without an existing registration");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK', "%s subscriptions have been deleted");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_NODATA', "No invalid data in table catsubsr found");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML', "Compare data from newsletter cats with mailing lists and correct invalid data");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_OK', "%s wrong data in mailing list have been corrected");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_NODATA', "No invalid data mailing list found");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL', "Compare data from newsletter cats with newsletters and correct invalid data");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_OK', "%s wrong data in newsletters have been corrected");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_NODATA', "No invalid data newsletters found");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT', "Delete table import and reset table");
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT_OK', "Table import maintained.");
define('_AM_XNEWSLETTER_IMPORT_SEARCH', "Search available email addresses to import");
define('_AM_XNEWSLETTER_IMPORT_PRESELECT_CAT', "Preselect category");
define('_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL', "Available plug-ins");
define('_AM_XNEWSLETTER_IMPORT_CONTINUE', "Continue");
define('_AM_XNEWSLETTER_IMPORT_AFTER_READ', "Action after reading the data");
define('_AM_XNEWSLETTER_IMPORT_READ_CHECK', "Show data to check");
define('_AM_XNEWSLETTER_IMPORT_CHECK_LIMIT', "Limit email addresses to import");
define('_AM_XNEWSLETTER_IMPORT_CHECK_LIMIT_PACKAGE', "Limit E-Mail-Addresses per processing step");
define('_AM_XNEWSLETTER_IMPORT_NOLIMIT', "No limit");
define('_AM_XNEWSLETTER_IMPORT_READ_IMPORT', "Import data at once without a check");
define('_AM_XNEWSLETTER_IMPORT_SHOW', "Show %s to %l of %n available E-Mail-Addresses");
define('_AM_XNEWSLETTER_IMPORT_NODATA', "No data found");
define('_AM_XNEWSLETTER_IMPORT_EMAIL_EXIST', "Email already registered");
define('_AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST', "Subscription already exist");
define('_AM_XNEWSLETTER_IMPORT_NOIMPORT', "-- No import --");
define('_AM_XNEWSLETTER_IMPORT_EXEC', "Import email addresses as preselected");
define('_AM_XNEWSLETTER_IMPORT_RESULT_SKIP', "Import email addresses %e skipped");
define('_AM_XNEWSLETTER_IMPORT_RESULT_FAILED', "Import email addresses %e failed");
define('_AM_XNEWSLETTER_IMPORT_RESULT_REG_OK', "Registration successful");
define('_AM_XNEWSLETTER_IMPORT_RESULT_SUBSCR_OK', "Subscription to category successful");
define('_AM_XNEWSLETTER_IMPORT_SKIP_EXISTING', "Skip existing subscriptions");
define('_AM_XNEWSLETTER_IMPORT_FINISHED', "Processing %p of %t email addresses successful finished");
define('_AM_XNEWSLETTER_IMPORT_INFO', "Add all users of a group to a newsletter");
define('_AM_XNEWSLETTER_IMPORT_CSV_OPT', "Options for CSV-file");
define('_AM_XNEWSLETTER_IMPORT_CSV_FILE', "CSV-file:");
define('_AM_XNEWSLETTER_IMPORT_CSV_DELIMITER', "Delimiter:");
define('_AM_XNEWSLETTER_IMPORT_CSV_HEADER', "CSV-file with header");
define('_AM_XNEWSLETTER_IMPORT_CSV', "One column ( email ) or four columns ( email | sex | first name | last name )<br />see sample1col.csv and sample4col.csv in /plug-ins");
define('_AM_XNEWSLETTER_IMPORT_XOOPSUSER', "Options to import/synchronise XoopsUsers");
define('_AM_XNEWSLETTER_IMPORT_XOOPSUSER_GROUPS', "Select groups");
define('_AM_XNEWSLETTER_NEWTASK', "Add New task");
define('_AM_XNEWSLETTER_TASKLIST', "List task");
define('_AM_XNEWSLETTER_TASK_ADD', "Add a task");
define('_AM_XNEWSLETTER_TASK_EDIT', "Edit a task");
define('_AM_XNEWSLETTER_TASK_DELETE', "Delete a task");
define('_AM_XNEWSLETTER_TASK_ID', "ID");
define('_AM_XNEWSLETTER_TASK_LETTER_ID', "Letter");
define('_AM_XNEWSLETTER_TASK_SUBSCR_ID', "Subscriber");
define('_AM_XNEWSLETTER_TASK_STATUS', "Status");
define('_AM_XNEWSLETTER_TASK_STARTTIME', "Starttime");
define('_AM_XNEWSLETTER_TASK_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_TASK_CREATED', "Created on");
define('_AM_XNEWSLETTER_TASK_ERROR_CREATE', "Error creating item in task list");
define('_AM_XNEWSLETTER_TASK_NO_DATA', "No tasks waiting");
//Error NoFrameworks
define('_AM_XNEWSLETTER_NOFRAMEWORKS', "Error: You don't have the Frameworks \"admin module\". Please install this Frameworks");
define('_AM_XNEWSLETTER_MAINTAINEDBY', "is maintained by the");
define('_AM_XNEWSLETTER_SEND_ERROR_NO_LETTERCONTENT', "No text available for printing");
define('_AM_XNEWSLETTER_FORMSEARCH_SUBSCR_EXIST', "Search existing subscription of an email address");
define('_AM_XNEWSLETTER_SUBSCR_NO_CATSUBSCR', "For this email address, no subscription to newsletter categories are available");
//version 1.2
define('_AM_XNEWSLETTER_IMPORT_ERROR_NO_PLUGIN', "Error: required file 'plugins/%p.php' not found!");
define('_AM_XNEWSLETTER_IMPORT_ERROR_NO_FUNCTION', "Error: required function 'xnewsletter_plugin_getdata_%f' doesn't exist!");
//version 1.3
//General
define('_AM_XNEWSLETTER_GROUPS_EDIT','Edit group');
//
define('_AM_XNEWSLETTER_THEREARE_TEMPLATE',"There are <span class='bold'>%s</span> Templates in the Database");
//
define('_AM_XNEWSLETTER_IMPORT_END', "End");
define('_AM_XNEWSLETTER_IMPORT_AFTER_READ_DESC','');
//
define('_AM_XNEWSLETTER_LETTER_CONTENT_DESC', 'Html format');
define('_AM_XNEWSLETTER_LETTER_EMAIL_TEST_DESC', 'One or more e-mails separated by semicolon &#39;;&#39;');
//
define('_AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW', 'Mr.');
define('_AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW', 'John');
define('_AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW', 'Doe');
define('_AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW', 'username@example.com');
//
define('_AM_XNEWSLETTER_TEMPLATE_ADD', "Add a template");
define('_AM_XNEWSLETTER_TEMPLATE_EDIT', "Edit a template");
define('_AM_XNEWSLETTER_TEMPLATE_DELETE', "Delete a template");
define('_AM_XNEWSLETTER_TEMPLATE_ID', "ID");
define('_AM_XNEWSLETTER_TEMPLATE_TITLE', "Title");
define('_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION', "Description");
define('_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION_DESC', '');
define('_AM_XNEWSLETTER_TEMPLATE_CONTENT', "Content");
define('_AM_XNEWSLETTER_TEMPLATE_CONTENT_DESC', '
    Html format
    <br />
    This module uses the Xoops <a href="http://www.smarty.net/">Smarty template engine</a> to render the email letter.
    <br /><br />
    Available smarty-vars are:
    <ul>
    <li>&lt;{$salutation}&gt; or &lt;{$sex}&gt;: the subscriber &quot;Salutation&quot; (subscr_sex) field</li>
    <li>&lt;{$firstname}&gt;: the subscriber &quot;First name&quot; (subscr_firstname) field</li>
    <li>&lt;{$lastname}&gt;: the subscriber &quot;Last name&quot; (subscr_lastname) field</li>
    <li>&lt;{$email}&gt; or &lt;{$subscr_email}&gt;: the subscriber &quot;Email&quot; (subscr_email) field</li>
    </ul>
    <ul>
    <li>&lt;{$letter_id}&gt;: the newsletter &quot;id&quot; (letter_id) field</li>
    <li>&lt;{$title}&gt;: the newsletter &quot;Title&quot; (letter_title) field</li>
    <li>&lt;{$content}&gt;: the newsletter &quot;Content&quot; (letter_content) field</li>
    <li>&lt;{$attachments}&gt;: attachments array
        <br />
        <span style="font-size:0.9em">
        e.g.:
        <br>
        &lt;ul&gt;
        <br>
        &lt;{foreach item="attachment" from=$attachments}&gt;
        <br>
        &lt;li&gt;&lt;a href="&lt;{$attachment.attachment_url}&gt;"&gt;&lt;{$attachment.attachment_name}&gt;&lt;/a&gt;&lt;/li&gt;
        <br>
        &lt;{/foreach}&gt;
        <br>
        &lt;/ul&gt;
        <br>
        will output the linked attachments list
        </span>
    </li>
    </ul>
    <ul>
    <li>&lt;{$date}&gt;: the sending date as timestamp integer
        <br />
        <span style="font-size:0.9em">
        e.g.:
        <br>
        &lt;{$date|date_format:"%Y/%m/%d"}&gt; will output the date formatted as ' . date("Y/m/d") . '
        </span>
        </li>
    <li>&lt;{$unsubscribe_url}&gt;: the unsubscribe url</li>
    </ul>
    <ul>
    <li>&lt;{$xoops_url}&gt;: the site main url (e.g. http://localhost/)</li>
    <li>&lt;{$xoops_langcode}&gt;: the site langcode (e.g. en)</li>
    <li>&lt;{$xoops_charset}&gt;: the site cherset (e.g. UTF-8)</li>
    </ul>');
define('_AM_XNEWSLETTER_TEMPLATE_SUBMITTER', "Submitter");
define('_AM_XNEWSLETTER_TEMPLATE_CREATED', "Created on");
define('_AM_XNEWSLETTER_LETTER_CLONED', "cloned: %s");
define('_AM_XNEWSLETTER_CAT_INFO_DESC', "");
define('_AM_XNEWSLETTER_TEXTOPTIONS', "Text options");
define('_AM_XNEWSLETTER_TEXTOPTIONS_DESC', "Description text options");
define('_AM_XNEWSLETTER_ALLOWHTML', "Allow HTML tags");
define('_AM_XNEWSLETTER_ALLOWSMILEY', "Allow Smiley icons");
define('_AM_XNEWSLETTER_ALLOWXCODE', "Allow XOOPS codes");
define('_AM_XNEWSLETTER_ALLOWIMAGES', "Allow images");
define('_AM_XNEWSLETTER_ALLOWBREAK', "Use XOOPS line break conversion");
define('_AM_XNEWSLETTER_LETTER_ACTION_PRINT', "Print");
define('_AM_XNEWSLETTER_LETTER_SENDER', "First sender");
define('_AM_XNEWSLETTER_LETTER_SENT', "Sent on");
define('_AM_XNEWSLETTER_THEREARE_NOT_ACCOUNTS', "<span color='#FF0000'>Warning: there are no email accounts, create an email account before.</span>");
define('_AM_XNEWSLETTER_LETTER_SIZE', "Size");
define('_AM_XNEWSLETTER_LETTER_EMAIL_SIZE', "Estimated size");
define('_AM_XNEWSLETTER_LETTER_EMAIL_SIZE_DESC', "");
define('_AM_XNEWSLETTER_LETTER_ATTACHMENT_SIZE', "Size");
define('_AM_XNEWSLETTER_LETTER_ATTACHMENT_TOTALSIZE', "Attachments size");
//
define('_AM_XNEWSLETTER_ATTACHMENT_SIZE', "Size");
define('_AM_XNEWSLETTER_ATTACHMENT_MODE', "Attached as");
define('_AM_XNEWSLETTER_ATTACHMENT_MODE_ASATTACHMENT', "Attachment");
define('_AM_XNEWSLETTER_ATTACHMENT_MODE_ASLINK', "Link");
define('_AM_XNEWSLETTER_ATTACHMENT_MODE_AUTO', "Auto");
//
define('_AM_XNEWSLETTER_ACTIONS_ACTIVATE', "Activate");
define('_AM_XNEWSLETTER_ACTIONS_UNACTIVATE', "Unactivate");
define('_AM_XNEWSLETTER_ACTIONS_EXEC', "Exec");
define('_AM_XNEWSLETTER_FORMACTIVATEOK', "Activated successfully");
define('_AM_XNEWSLETTER_FORMUNACTIVATEOK', "Unactivated successfully");
//
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_EMPTY','');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_SAVED', 'Saved');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK', 'Error creating item in task list');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST', 'Newsletter sent for test (%recipient)');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND', 'Newsletter sent');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND', 'Newsletter send failed -> %error');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_PHPMAILER',"Error phpmailer -> %error");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND_COUNT',"Error sending newsletter: %error_count of %total_count newsletters not sent");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_COUNT',"Sending %total_count newsletter(s) successfully");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_MAILINGLIST', "Handle mailing list successfully");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_MAILINGLIST', "Sending '%action_code' to mailing list successfully");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_CRON', "Cron: %result_exec");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_SKIP_IMPORT', "Import email addresses %subscr_email skipped");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_IMPORT', "Import email addresses %subscr_email failed");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_IMPORT', "%result_text Subscription to category successful");
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_EXIST_IMPORT', "%result_text Subscription already exist");

define('_AM_XNEWSLETTER_PROTOCOL_CREATED_FILTER_FROM', "Protocol created form date");
define('_AM_XNEWSLETTER_PROTOCOL_CREATED_FILTER_TO', "Protocol created to date");
define('_AM_XNEWSLETTER_PROTOCOLLIST_BY_LETTER', "List by letter");
define('_AM_XNEWSLETTER_PROTOCOL_SHOW_ALL', "Show all");
define('_AM_XNEWSLETTER_PROTOCOL_DELETE_ALL', "Delete all protocols");

define('_AM_XNEWSLETTER_TASK_CREATED_FILTER_FORM', "Task created form date");
define('_AM_XNEWSLETTER_TASK_CREATED_FILTER_TO', "Task created to date");
define('_AM_XNEWSLETTER_TASK_STARTTIME_FILTER_FROM', "Task starttime form date");
define('_AM_XNEWSLETTER_TASK_STARTTIME_FILTER_TO', "Task starttime to date");
define('_AM_XNEWSLETTER_TASK_ACTIONS_EXECUTE', "Execute now");
define('_AM_XNEWSLETTER_TASK_SHOW_ALL', "Show all");
define('_AM_XNEWSLETTER_TASK_CONFIGS', "Config &quot;Send in pakages&quot;");
define('_AM_XNEWSLETTER_TASK_DELETE_ALL', "Delete all tasks");
define('_AM_XNEWSLETTER_TASK_RUN_CRON_NOW', "Execute now &quot;cron.php&quot;");
define('_AM_XNEWSLETTER_FORMSURERUNCRONNOW', "Are you sure you want to execute: <span class='bold red'>&quot;cron.php&quot;</span>");


//Buttons
define('_AM_XNEWSLETTER_NEWTEMPLATE', "Add New Template");
define('_AM_XNEWSLETTER_TEMPLATELIST', "List Template");
