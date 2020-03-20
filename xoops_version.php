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

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");

use XoopsModules\Xnewsletter;

require_once __DIR__ . '/preloads/autoloader.php';

/** @var \XoopsModules\Xnewsletter\Helper $helper */
$helper = \XoopsModules\Xnewsletter\Helper::getInstance();

$mydirname = basename(__DIR__);
xoops_load('XoopsLists');

$modversion['version']             = 1.41;
$modversion['module_status']       = 'RC 1';
$modversion['release_date']        = '2019/12/22';
$modversion['name']                = _MI_XNEWSLETTER_NAME;
$modversion['description']         = _MI_XNEWSLETTER_DESC;
$modversion['author']              = 'Goffy, Alfred, Mamba';
$modversion['credits']             = 'XOOPS Development Team';
$modversion['license']             = 'GPL 2.0';
$modversion['help']                = 'page=help';
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['official']            = false;
$modversion['author_mail']         = 'webmaster@wedega.com';
$modversion['author_website_url']  = 'wedega.com';
$modversion['author_website_name'] = 'Webdesign Gabor';
$modversion['dirname']             = $mydirname;
$modversion['license']             = 'GNU GPL 2.0 see Licence';
$modversion['license_url']         = 'www.gnu.org/licenses/gpl-2.0.html/';
//$modversion['release']           = "1.3";
$modversion['demo_site_url']       = '';
$modversion['demo_site_name']      = '';
$modversion['forum_site_url']      = '';
$modversion['forum_site_name']     = '';
$modversion['module_website_url']  = 'wedega.com';
$modversion['module_website_name'] = 'Webdesign Gabor';
$modversion['release_info']        = '';
$modversion['release_file']        = XOOPS_URL . '/modules/' . $mydirname . '/docs/changelog.txt';
$modversion['manual']              = 'xnewsletter.txt';
$modversion['manual_file']         = XOOPS_URL . "/modules/{$mydirname}/docs/";
$modversion['min_php']             = '7.0';
$modversion['min_xoops']           = '2.5.10';
$modversion['min_admin']           = '1.1';
$modversion['min_db']              = ['mysql' => '5.5'];
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_XNEWSLETTER_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_XNEWSLETTER_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_XNEWSLETTER_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_XNEWSLETTER_SUPPORT, 'link' => 'page=support'],
    ['name' => _MI_XNEWSLETTER_INSTALL, 'link' => 'page=help2'],
];

// Admin things
$modversion['hasAdmin'] = 1;
// Admin system menu
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// Mysql file
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables
$modversion['tables'] = [
    'xnewsletter_accounts',
    'xnewsletter_cat',
    'xnewsletter_subscr',
    'xnewsletter_catsubscr',
    'xnewsletter_letter',
    'xnewsletter_protocol',
    'xnewsletter_attachment',
    'xnewsletter_mailinglist',
    'xnewsletter_bmh',
    'xnewsletter_import',
    'xnewsletter_task',
    'xnewsletter_template',
];

// Scripts to run upon installation or update
$modversion['onInstall'] = 'include/oninstall.php';
$modversion['onUpdate']  = 'include/onupdate.php';

// Comments
$modversion['hasComments'] = false;

// Menu
global $xoopsUser;

$modversion['hasMain'] = true;

$subcount          = 1;
$modversion['sub'] = [];

// check user rights
$grouppermHandler = xoops_getHandler('groupperm');
/** @var \XoopsModuleHandler $moduleHandler */
$moduleHandler = xoops_getHandler('module');
$memberHandler = xoops_getHandler('member');
$uid           = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
$groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : [0 => XOOPS_GROUP_ANONYMOUS];
$xoopsModule   = $moduleHandler->getByDirname('xnewsletter');

$showRead   = false;
$showEdit   = false;
$showCreate = false;
$showList   = false;

if (is_object($xoopsModule)) {
    //    $catHandler  = xoops_getModuleHandler('cat', 'xnewsletter');
    $catHandler  = $helper->getHandler('Cat');
    $catCriteria = new \CriteriaCompo();
    $catCriteria->setSort('cat_id');
    $catCriteria->setOrder('ASC');
    $catObjs = $catHandler->getAll($catCriteria);

    foreach ($catObjs as $catObj) {
        if ($grouppermHandler->checkRight('newsletter_read_cat', $catObj->getVar('cat_id'), $groups, $xoopsModule->mid())) {
            $showRead = true;
        }
        if ($grouppermHandler->checkRight('newsletter_create_cat', $catObj->getVar('cat_id'), $groups, $xoopsModule->mid())) {
            $showEdit   = true;
            $showCreate = true;
        }
        if ($grouppermHandler->checkRight('newsletter_list_cat', $catObj->getVar('cat_id'), $groups, $xoopsModule->mid())) {
            $showList = true;
        }
    }
}

if (true === $showRead) {
    $modversion['sub'][$subcount]['name'] = _MI_XNEWSLETTER_SUBSCRIBE;
    $modversion['sub'][$subcount]['url']  = 'subscription.php';
    ++$subcount;
    $modversion['sub'][$subcount]['name'] = _MI_XNEWSLETTER_SUBSCRIPTION_SEARCH;
    $modversion['sub'][$subcount]['url']  = 'subscription.php?op=search_subscription';
    ++$subcount;
    $modversion['sub'][$subcount]['name'] = _MI_XNEWSLETTER_LIST;
    $modversion['sub'][$subcount]['url']  = 'letter.php?op=list_letters';
    ++$subcount;
}
//if (true === $showEdit) {}
if (true === $showCreate) {
    $modversion['sub'][$subcount]['name'] = _MI_XNEWSLETTER_CREATE;
    $modversion['sub'][$subcount]['url']  = 'letter.php?op=new_letter';
    ++$subcount;
}
if (true === $showList) {
    $modversion['sub'][$subcount]['name'] = _MI_XNEWSLETTER_LIST_SUBSCR;
    $modversion['sub'][$subcount]['url']  = 'letter.php?op=list_subscrs';
    ++$subcount;
}

// Templates
$modversion['templates'] = [
    // Admin
    ['file' => 'xnewsletter_admin_about.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_attachments.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_accounts.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_bmh.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_categories.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_catsubscribers.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_feedback.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_footer.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_header.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_import.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_index.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_letters.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_mailinglists.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_maintenance.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_protocols.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_subscribers.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_tasks.tpl', 'description' => '', 'type' => 'admin'],
    ['file' => 'xnewsletter_admin_templates.tpl', 'description' => '', 'type' => 'admin'],
    //User
    ['file' => $mydirname . '_header.tpl', 'description' => ''],
    ['file' => $mydirname . '_footer.tpl', 'description' => ''],
    ['file' => $mydirname . '_empty.tpl', 'description' => ''],
    ['file' => $mydirname . '_index.tpl', 'description' => ''],
    ['file' => $mydirname . '_subscription.tpl', 'description' => ''],
    ['file' => $mydirname . '_subscription_result.tpl', 'description' => ''],
    ['file' => $mydirname . '_subscription_list_subscriptions.tpl', 'description' => ''],
    ['file' => $mydirname . '_letter.tpl', 'description' => ''],
    ['file' => $mydirname . '_letter_print.tpl', 'description' => ''],
    ['file' => $mydirname . '_letter_preview.tpl', 'description' => ''],
    ['file' => $mydirname . '_letter_list_letters.tpl', 'description' => ''],
    ['file' => $mydirname . '_letter_list_subscrs.tpl', 'description' => ''],
    ['file' => $mydirname . '_protocol.tpl', 'description' => ''],
    // Common templates
    ['file' => $mydirname . '_common_breadcrumb.tpl', 'description' => ''],
];

unset($i);

// Config
$i                                       = 1;
$modversion['config'][$i]['name']        = 'xnewsletter_editor';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_EDITOR';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_EDITOR_DESC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = 'dhtmltextarea';
$modversion['config'][$i]['options']     = \XoopsLists::getEditorList();
++$i;
$modversion['config'][$i]['name']        = 'template_editor';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_TEMPLATE_EDITOR';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_TEMPLATE_EDITOR_DESC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = 'textarea';
$modversion['config'][$i]['options']     = \XoopsLists::getEditorList();
++$i;
$modversion['config'][$i]['name']        = 'keywords';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_KEYWORDS';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_KEYWORDS_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '';
++$i;
//Uploads : max letter_attachments
$modversion['config'][$i]['name']        = 'xn_maxattachments';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_MAXATTACHMENTS';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_MAXATTACHMENTS_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '5';
++$i;
//Uploads : size letter_attachment
$modversion['config'][$i]['name']        = 'xn_maxsize';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_ATTACHMENT_MAXSIZE';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_ATTACHMENT_MAXSIZE_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '10485760'; // 1MByte
++$i;
//Uploads : mimetypes letter_attachment
$modversion['config'][$i]['name']        = 'xn_mimetypes';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_ATTACHMENT_MIMETYPES';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_ATTACHMENT_MIMETYPES_DESC';
$modversion['config'][$i]['formtype']    = 'select_multi';
$modversion['config'][$i]['valuetype']   = 'array';
$modversion['config'][$i]['default']     = [
    'application/pdf',
    'image/gif',
    'image/jpeg',
    'image/png',
];
$modversion['config'][$i]['options']     = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/msword',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.ms-excel',
    'ppt'  => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.ms-powerpoint',
    'swf'  => 'application/x-shockwave-flash',
    'tar'  => 'application/x-tar',
    'gz'   => 'application/x-gzip',
    'zip'  => 'application/zip',
    'bmp'  => 'image/bmp',
    'gif'  => 'image/gif',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg',
    'jpe'  => 'image/jpeg',
    'png'  => 'image/png',
    'tiff' => 'image/tiff',
    'tif'  => 'image/tif',
    'asc'  => 'text/plain',
    'txt'  => 'text/plain',
    'rtf'  => 'text/rtf',
];

++$i;
//Uploads : path attachments
$modversion['config'][$i]['name']        = 'xn_attachment_path';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_ATTACHMENT_PATH';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_ATTACHMENT_PATH_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '/xnewsletter/attachments/';
++$i;
$modversion['config'][$i]['name']        = 'adminperpage';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_ADMINPERPAGE';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_ADMINPERPAGE_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '15';
++$i;
$modversion['config'][$i] = [
    'name'        => 'dateformat',
    'title'       => '_MI_XNEWSLETTER_DATEFORMAT',
    'description' => '_MI_XNEWSLETTER_DATEFORMATDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _DATESTRING,
]; //'D, d-M-Y');
++$i;
$modversion['config'][$i]['name']        = 'welcome_message';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_WELCOME_MESSAGE';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_WELCOME_MESSAGE_DESC';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = _MI_XNEWSLETTER_WELCOME;
++$i;
$modversion['config'][$i]['name']        = 'advertise';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_ADVERTISE';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_ADVERTISE_DESC';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '';
++$i;
$modversion['config'][$i]['name']        = 'social_active';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_SOCIALACTIVE';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_SOCIALACTIVE_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = false;
++$i;
$modversion['config'][$i]['name']        = 'social_code';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_SOCIALCODE';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_SOCIALCODE_DESC';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '';
++$i;
$modversion['config'][$i]['name']        = 'xn_use_mailinglist';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_USE_MAILINGLIST';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_USE_MAILINGLIST_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = false;
++$i;
$modversion['config'][$i]['name']        = 'xn_use_salutation';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_USE_SALUTATION';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_USE_SALUTATION_DESC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = true;
++$i;
$modversion['config'][$i]['name']        = 'xn_groups_without_actkey';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_GROUPS_WITHOUT_ACTKEY';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_GROUPS_WITHOUT_ACTKEY_DESC';
$modversion['config'][$i]['formtype']    = 'group_multi';
$modversion['config'][$i]['valuetype']   = 'array';
$modversion['config'][$i]['default']     = '1';
++$i;
$modversion['config'][$i]['name']        = 'confirmation_time';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_CONFIRMATION_TIME';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_CONFIRMATION_TIME_DESC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 24;
$modversion['config'][$i]['options']     = [
    _MI_XNEWSLETTER_CONFIRMATION_TIME_0  => 0,
    _MI_XNEWSLETTER_CONFIRMATION_TIME_1  => 1,
    _MI_XNEWSLETTER_CONFIRMATION_TIME_6  => 6,
    _MI_XNEWSLETTER_CONFIRMATION_TIME_24 => 24,
    _MI_XNEWSLETTER_CONFIRMATION_TIME_48 => 48,
];
++$i;
$modversion['config'][$i]['name']        = 'xn_groups_change_other';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_GROUPS_CHANGE_OTHER';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_GROUPS_CHANGE_OTHER_DESC';
$modversion['config'][$i]['formtype']    = 'group_multi';
$modversion['config'][$i]['valuetype']   = 'array';
$modversion['config'][$i]['default']     = '1';
++$i;
$modversion['config'][$i]['name']        = 'xn_send_in_packages';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_SEND_IN_PACKAGES';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_SEND_IN_PACKAGES_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '0';
++$i;
$modversion['config'][$i]['name']        = 'xn_send_in_packages_time';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_SEND_IN_PACKAGES_TIME';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_SEND_IN_PACKAGES_TIME_DESC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = '60';
++$i;
$modversion['config'][$i]['name']        = 'xn_cron_protocol';
$modversion['config'][$i]['title']       = '_MI_XNEWSLETTER_CRON_PROTOCOL';
$modversion['config'][$i]['description'] = '_MI_XNEWSLETTER_CRON_PROTOCOL_DESC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
$modversion['config'][$i]['options']     = [
    _MI_XNEWSLETTER_CRON_PROTOCOL_0  => 0,
    _MI_XNEWSLETTER_CRON_PROTOCOL_1  => 1,
    _MI_XNEWSLETTER_CRON_PROTOCOL_2  => 2
];

unset($i);

// Blocks
$modversion['blocks'][] = [
    'file'        => 'blocks_subscrinfo.php',
    'name'        => _MI_XNEWSLETTER_SUBSCRINFO_BLOCK,
    'description' => '',
    'show_func'   => 'b_xnewsletter_subscrinfo',
    'edit_func'   => '',
    'template'    => 'xnewsletter_subscrinfo_block.tpl',
    'can_clone'   => true,
    'options'     => '',
];

$modversion['blocks'][] = [
    'file'        => 'blocks_catsubscr.php',
    'name'        => _MI_XNEWSLETTER_CATSUBSCR_BLOCK_RECENT,
    'description' => '',
    'show_func'   => 'b_xnewsletter_catsubscr',
    'edit_func'   => 'b_xnewsletter_catsubscr_edit',
    'template'    => 'xnewsletter_catsubscr_block_recent.tpl',
    'can_clone'   => true,
    'options'     => 'recent|5|0|0',
];

$modversion['blocks'][] = [
    'file'        => 'blocks_catsubscr.php',
    'name'        => _MI_XNEWSLETTER_CATSUBSCR_BLOCK_DAY,
    'description' => '',
    'show_func'   => 'b_xnewsletter_catsubscr',
    'edit_func'   => 'b_xnewsletter_catsubscr_edit',
    'template'    => 'xnewsletter_catsubscr_block_day.tpl',
    'can_clone'   => true,
    'options'     => 'day|5|0|0',
];

$modversion['blocks'][] = [
    'file'        => 'blocks_letter.php',
    'name'        => _MI_XNEWSLETTER_LETTER_BLOCK_RECENT,
    'description' => '',
    'show_func'   => 'b_xnewsletter_letter',
    'edit_func'   => 'b_xnewsletter_letter_edit',
    'template'    => 'xnewsletter_letter_block_recent.tpl',
    'can_clone'   => true,
    'options'     => 'recent|5|0|0',
];

$modversion['blocks'][] = [
    'file'        => 'blocks_letter.php',
    'name'        => _MI_XNEWSLETTER_LETTER_BLOCK_DAY,
    'description' => '',
    'show_func'   => 'b_xnewsletter_letter',
    'edit_func'   => 'b_xnewsletter_letter_edit',
    'template'    => 'xnewsletter_letter_block_day.tpl',
    'can_clone'   => true,
    'options'     => 'day|5|0|0',
];

$modversion['blocks'][] = [
    'file'        => 'blocks_letter.php',
    'name'        => _MI_XNEWSLETTER_LETTER_BLOCK_RANDOM,
    'description' => '',
    'show_func'   => 'b_xnewsletter_letter',
    'edit_func'   => 'b_xnewsletter_letter_edit',
    'template'    => 'xnewsletter_letter_block_random.tpl',
    'can_clone'   => true,
    'options'     => 'random|5|0|0',
];
