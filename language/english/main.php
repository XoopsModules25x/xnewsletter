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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
// Main
define('_MA_XNEWSLETTER_INDEX', 'Home'); // NOT USED
define('_MA_XNEWSLETTER_TITLE', 'xNewsletter');
define('_MA_XNEWSLETTER_DESC', 'Newsletter module for Xoops');
define('_MA_XNEWSLETTER_WELCOME',
       '<h2>Welcome in our newsletter system</h2>We hope, we can keep you up to date with our newsletters. Feel free to subscribe to one or more of our newsletters. If you do not want a newsletter any more, than you can easily unsubscribe here. You also can unsubscribe in a simple way via a link in each of our newsletters.');
define('_MA_XNEWSLETTER_ACCOUNTS', 'Accounts');
define('_MA_XNEWSLETTER_CAT', 'Category');
define('_MA_XNEWSLETTER_SUBSCR', 'Subscribers');
define('_MA_XNEWSLETTER_CATSUBSCR', 'Subscriber Newsletter category'); // NOT USED
define('_MA_XNEWSLETTER_LETTER', 'Letter');
define('_MA_XNEWSLETTER_PROTOCOL', 'Protocol');
define('_MA_XNEWSLETTER_BMH', 'Bounced emails ');
define('_MA_XNEWSLETTER_ADMIN', 'Admin');
define('_MA_XNEWSLETTER_LETTER_CATS', 'Sent with newsletter');
define('_MA_XNEWSLETTER_SUBSCRIPTION_SEARCH', 'Search for subscriptions');
define('_MA_XNEWSLETTER_SUBSCRIPTION_SEARCH_EMAIL', 'Search or add subscriptions for this email: ');
define('_AM_XNEWSLETTER_SUBSCRIPTION_SEARCH_ADD', 'Search / Add subscriptions');
define('_MA_XNEWSLETTER_SUBSCRIPTION_EXIST', 'Existing subscriptions');
define('_MA_XNEWSLETTER_SUBSCRIPTION_EXIST_NONE', 'No subscriptions existing');
define('_MA_XNEWSLETTER_REGISTRATION_EXIST', 'Existing registrations of this email');
define('_MA_XNEWSLETTER_REGISTRATION_NONE', "The email %s isn't registered till now. For subscriptions we need further information. Please fill in registration form.<br>We want to inform you, that your ip-address will be saved for the purpose of transparency.");
define('_MA_XNEWSLETTER_REGISTRATION_ADD', 'If you want, you can add more than one person to one email, and later on you can make different subscriptions'); // NOT USED
define('_MA_XNEWSLETTER_SUBSCRIPTION_ADD', 'Add a new subscription');
define('_MA_XNEWSLETTER_SUBSCRIPTION_EDIT', 'Edit subscriptions');
define('_MA_XNEWSLETTER_SUBSCRIPTION_DELETE', 'Delete subscriptions');
define('_MA_XNEWSLETTER_SUBSCRIPTION_DELETE_SURE', 'Do you really want to delete this registration with all subscriptions?<br><br>If you want to (un)subscribe for one or more newsletters, please use the edit button!<br><br>');
define('_MA_XNEWSLETTER_SUBSCRIPTION_INFO_PERS', 'Your personal information');
define('_MA_XNEWSLETTER_SUBSCRIPTION_SELECT_CATS', 'Select your newsletter');
define('_MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL', 'Available newsletters');
define('_MA_XNEWSLETTER_SUBSCRIPTION_NO_CATS_AVAIL', 'There are no newsletters available');
define('_MA_XNEWSLETTER_SUBSCRIPTION_OK', 'Your selections have been registered successfully');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR', 'There have been error(s) while handling your (un)subscriptions');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOID', 'Error: no valid subscriber-id');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL', 'Error: no valid email address');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY', 'Error sending confirmation email');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR', 'Error saving personal information');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_INVALIDKEY', 'Error: no valid key type');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NODATAKEY', 'Error: no data to this key');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOVALIDKEY', 'Error: no valid key'); // NOT USED
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR', 'Error saving (un)subscription information');
define('_MA_XNEWSLETTER_SUBSCRIPTION_REG_OK', 'Registration of your personal information have been successfully.');
define('_MA_XNEWSLETTER_SUBSCRIPTION_PROT_SUBSCRIBE', "Subscription newsletter '%nl' successful");
define('_MA_XNEWSLETTER_SUBSCRIPTION_PROT_UNSUBSCRIBE', "Unsubscription newsletter '%nl' successful");
define('_MA_XNEWSLETTER_SUBSCRIPTION_PROT_NO_CHANGE', "No changes in selection newsletter '%nl'");
define('_MA_XNEWSLETTER_SUBSCRIPTION_PROT_DAT_QUITED_REMOVED', "Date quit from newsletter '%nl' successful removed");
define('_MA_XNEWSLETTER_SUBSCRIPTION_PROT_SENT_INFO', "Information mail sent to '%e'");
define('_MA_XNEWSLETTER_SUBSCRIPTION_QUITED', "<span style='color:red'>Attention: deactivated by the webmaster!</span>");
define('_MA_XNEWSLETTER_SUBSCRIPTION_QUITED_DETAIL', "<span style='color:red'>Attention: This subscription has been deactivated by the webmaster at %q as a result of bounced emails! If you want to reactivate the subscription, please let this newsletter checked.</span>");
define('_MA_XNEWSLETTER_UNSUBSCRIPTION_OK', "The email '%e' have been successfully unsubscribed from newsletter '%n'");
define('_MA_XNEWSLETTER_UNSUBSCRIPTION_ERROR', "Error while unsubscribe '%e' from newsletter '%n'");
define('_MA_XNEWSLETTER_SUBSCRIPTION_UPDATE_OK', 'Your Newsletter has changed');
define('_MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED', 'The registration is completed');
define('_MA_XNEWSLETTER_SUBSCRIPTIONSUBJECT', 'Confirm the receipt of the newsletter at ');
define('_MA_XNEWSLETTER_SUBSCRIPTION_SUBJECT_CHANGE', 'Information about changes of your newsletter at ');
define('_MA_XNEWSLETTER_SENDMAIL_REG_OK', "An email with confirmation code has been sent to the email address '%subscr_email'.");
define('_MA_XNEWSLETTER_DELETESUBJECT', 'Confirm to delete of newsletter from');
define('_MA_XNEWSLETTER_SUBSCRIPTION_REG_UPDATE_CLOSED', 'The registration data have been saved successful.');
define('_MA_XNEWSLETTER_SENDMAIL_UNREG_OK', 'An email to confirm your unsubscription have been sent.');
define('_MA_XNEWSLETTER_SUBSCRIPTION_UNFINISHED',
       "<span style='color:red'>Attention: the registration has not been confirm till now. Please click on the activating link in the e-mail we sent you. If you did not get this e-mail, please click <a href='%link'>here</a> to get this mail once more.</span>");
define('_MA_XNEWSLETTER_PLEASE_LOGIN', "The email address %s belongs to a registered user. <br> please <a href='" . XOOPS_URL . "/user.php?xoops_redirect=/modules/xnewsletter/subscription.php'>login</a> to change the data.");
define('_MA_XNEWSLETTER_LETTER_NONEAVAIL', 'No newsletters available for the moment');
//1.2.2
define('_MA_XNEWSLETTER_ACCOUNTS_NONEAVAIL', 'No email-accounts available for the moment');
//1.3
define('_MD_XNEWSLETTER_SUBSCRIBE', '(Un)Subscribe');
define('_MD_XNEWSLETTER_LIST', 'Newsletter list');
define('_MD_XNEWSLETTER_LIST_SUBSCR', 'List subscribers');
define('_MD_XNEWSLETTER_LETTER_CREATE', 'Create newsletter');
define('_MD_XNEWSLETTER_LETTER_EDIT', 'Edit newsletter');
define('_MD_XNEWSLETTER_LETTER_DELETE', 'Delete newsletter');
define('_MD_XNEWSLETTER_LETTER_COPY', 'Copy/clone newsletter');
define('_MD_XNEWSLETTER_LETTER_PREVIEW', 'Preview');
define('_MD_XNEWSLETTER_SUBSCRIPTION_EDIT', 'Edit subscription');
define('_MD_XNEWSLETTER_SUBSCRIPTION_DELETE', 'Delete subscription');
define('_MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL_DESC', 'Select or unselect the newsletter you want subscribe/unsubscribe');
define('_MD_XNEWSLETTER_PROTOCOL', 'Protocol');
define('_MD_XNEWSLETTER_OK', 'Ok');
define('_MD_XNEWSLETTER_WARNING', 'Warning');
define('_MD_XNEWSLETTER_ERROR', 'Error');
define('_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_KEYEXPIRED', 'Error: expired key, please repeat subscription');
define('_MA_XNEWSLETTER_RESENDMAIL_REG_OK', "An email with confirmation code has been resent to the email address '%subscr_email'.");
//1.4
define('_MA_XNEWSLETTER_NOTEMPLATE_ONLINE', "There are no email templates available!");
define('_MA_XNEWSLETTER_SUBSCRIPTION_SENDINFO', "Subscription information");
define('_MA_XNEWSLETTER_SUBSCRIPTION_SENDINFO_OK', "Subscription information have been sent to requested email. Please check your emails");
define('_MA_XNEWSLETTER_SUBSCRIPTION_SENDINFO_ERROR', "Error when sending subscription information to requested email!");
