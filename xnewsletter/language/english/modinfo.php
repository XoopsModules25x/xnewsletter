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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
// Admin
define('_MI_XNEWSLETTER_NAME',"xNewsletter");
define('_MI_XNEWSLETTER_DESC',"Newsletter module for Xoops");
//Menu
define('_MI_XNEWSLETTER_ADMENU1',"Dashboard");
define('_MI_XNEWSLETTER_ADMENU2',"Accounts");
define('_MI_XNEWSLETTER_ADMENU3',"Categories");
define('_MI_XNEWSLETTER_ADMENU4',"Subscribers");
define('_MI_XNEWSLETTER_ADMENU5',"Categories-Subscribers");
define('_MI_XNEWSLETTER_ADMENU6',"Newsletters");
define('_MI_XNEWSLETTER_ADMENU7',"Attachments");
define('_MI_XNEWSLETTER_ADMENU8',"Protocols");
define('_MI_XNEWSLETTER_ADMENU9',"Mailinglists");
define('_MI_XNEWSLETTER_ADMENU10',"Bounced email handler");
define('_MI_XNEWSLETTER_ADMENU11',"Maintenance");
define('_MI_XNEWSLETTER_ADMENU12',"Import");
define('_MI_XNEWSLETTER_ADMENU13',"Task list");
define('_MI_XNEWSLETTER_ADMENU99',"About");
define('_MI_XNEWSLETTER_SUBSCRIBE',"(Un)Subscribe");
define('_MI_XNEWSLETTER_LIST',"Newsletter list");
define('_MI_XNEWSLETTER_LIST_SUBSCR',"List subscribers");
define('_MI_XNEWSLETTER_CREATE',"Create newsletter");
//Blocks
define('_MI_XNEWSLETTER_CATSUBSCR_BLOCK_RECENT',"Current registrations");
define('_MI_XNEWSLETTER_CATSUBSCR_BLOCK_DAY',"Today's registrations");
define('_MI_XNEWSLETTER_LETTER_BLOCK_RECENT',"Current Newsletters");
define('_MI_XNEWSLETTER_LETTER_BLOCK_DAY',"Today's Newsletters");
define('_MI_XNEWSLETTER_LETTER_BLOCK_RANDOM',"Random Newsletters");
//Config
define('_MI_XNEWSLETTER_EDITOR',"Editor");
define('_MI_XNEWSLETTER_KEYWORDS',"Keywords");
define('_MI_XNEWSLETTER_KEYWORDS_DESC',"Insert here the keywords (separate by comma)");
define('_MI_XNEWSLETTER_ADMINPERPAGE',"Number of list entries in administration pages");
define('_MI_XNEWSLETTER_ADMINPERPAGE_DESC',"Specifies how many items you want to display per page in the list.");
define('_MI_XNEWSLETTER_ADVERTISE',"Code of advertise");
define('_MI_XNEWSLETTER_ADVERTISE_DESC',"Insert here the code of advertisement");
define('_MI_XNEWSLETTER_SOCIALACTIVE',"View social networks?");
define('_MI_XNEWSLETTER_SOCIALACTIVE_DESC',"If you want to see the buttons of social networks, click on Yes");
define('_MI_XNEWSLETTER_SOCIALCODE',"Code of social networks");
define('_MI_XNEWSLETTER_SOCIALCODE_DESC',"Insert here the code of social networks");
define('_MI_XNEWSLETTER_ATTACHMENT_MAXSIZE',"Maximum file size");
define('_MI_XNEWSLETTER_ATTACHMENT_MAXSIZE_DESC',"Maximum file size for attachments");
define('_MI_XNEWSLETTER_ATTACHMENT_MIMETYPES',"Mime-types");
define('_MI_XNEWSLETTER_ATTACHMENT_MIMETYPES_DESC',"Allowed mime-types for attachments");
define('_MI_XNEWSLETTER_ATTACHMENT_PATH',"Upload-path");
define('_MI_XNEWSLETTER_ATTACHMENT_PATH_DESC',"Define path, where uploaded attachments will be saved ( folders after {XOOPS_ROOT_PATH}/uploads ), <b>with slash at beginning</b> and <b>with trailing slash</b>.");
define('_MI_XNEWSLETTER_USE_MAILINGLIST',"Use additional feature mailing lists");
define('_MI_XNEWSLETTER_USE_MAILINGLIST_DESC',"If you have existing mailing lists, you can synchronize and deregister a newsletter with a mailing list. <b>xNewsletter cannot create Mailing List </b>. If you enable this feature, you'll get an additional tab on the administration page of xNewsletter.");
define('_MI_XNEWSLETTER_GROUPS_WITHOUT_ACTKEY',"Groups (un)subscribing without confirmation email");
define('_MI_XNEWSLETTER_GROUPS_WITHOUT_ACTKEY_DESC',"Define the groups, which can make (un)subscriptions to a newsletter directly, without sending back a confirmation email");
define('_MI_XNEWSLETTER_GROUPS_CHANGE_OTHER',"Groups with permission to change subscription of other persons");
define('_MI_XNEWSLETTER_GROUPS_CHANGE_OTHER_DESC',"Define the groups, which can edit the subscriptions of other persons. Deleting the registration is not possible. This groups need also the permission to list the subscribers of a newsletter category. It is recommended to give this groups also the permission to create newsletters.");
define('_MI_XNEWSLETTER_USE_SALUTATION',"Use field salutation");
define('_MI_XNEWSLETTER_USE_SALUTATION_DESC',"Please decide, whether you want use salutations like 'Mr.', 'Mrs.',...");
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES',"Send e-mails in packages");
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES_DESC',"Number of e-mails, which should be sent in one package. 0 means, that all e-mails always be sent immediately. You can use this option only, if you can start cronjobs with external programs.");
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES_TIME',"Time period for sending e-mails in packages");
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES_TIME_DESC',"Time period in minutes, when the next package should be sent. Only used, if 'Send e-mails in packages' is bigger than 0.");
define('_MI_XNEWSLETTER_UPGRADEFAILED',"Error while updating module");
// version 1.2
define('_MI_XNEWSLETTER_SUBSCRINFO_BLOCK',"Info Newsletter");
define('_MI_XNEWSLETTER_SUBSCRINFO_TEXT_BLOCK',"If you want to be informed in time, then subscribe to our newsletter");
// version 1.3
define('_MI_XNEWSLETTER_WELCOME_MESSAGE',"Welcome message");
define('_MI_XNEWSLETTER_WELCOME_MESSAGE_DESC',"Html format");
define('_MI_XNEWSLETTER_WELCOME',"<h2>Welcome in our newsletter system</h2>We hope, we can keep you up to date with our newsletters. Feel free to subscribe to one or more of our newsletters. If you do not want a newsletter any more, than you can easily unsubscribe here. You also can unsubscribe in a simple way via a link in each of our newsletters.");
define('_MI_XNEWSLETTER_DATEFORMAT', "Timestamp");
define('_MI_XNEWSLETTER_DATEFORMATDSC', "Default Timestamp for module front end. <br />More info here: <a href='http://www.php.net/manual/en/function.date.php'>http://www.php.net/manual/en/function.date.php</a>");
//
define('_MI_XNEWSLETTER_ADMENU_TEMPLATES',"Templates");
define('_MI_XNEWSLETTER_EDITOR_DESC',"");
define('_MI_XNEWSLETTER_TEMPLATE_EDITOR',"Templates editor");
define('_MI_XNEWSLETTER_TEMPLATE_EDITOR_DESC',"");
