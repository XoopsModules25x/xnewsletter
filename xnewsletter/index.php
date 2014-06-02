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

$currentFile = basename(__FILE__);
include_once "header.php";

$op = xNewsletter_CleanVars($_REQUEST, 'op', 'welcome', 'string');

switch ($op) {
    case "welcome" :
    default :
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_index.tpl";
        include XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new xNewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $xoopsTpl->assign('welcome_message', $xnewsletter->getConfig('welcome_message'));
        $xoopsTpl->assign('xnewsletter_content', _MA_XNEWSLETTER_WELCOME); // this definition is not removed for backward compatibility issues
        break;

    case "show_preview" :
    case "show_letter_preview" :
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_letter_preview.tpl";
        include XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new xNewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $xoopsTpl->assign('welcome_message', $xnewsletter->getConfig('welcome_message'));

        $template_path = XNEWSLETTER_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
        if (!is_dir($template_path)) {
            $template_path = XNEWSLETTER_ROOT_PATH . '/language/english/templates/';
        }

        // get letter_id
        $letter_id = xNewsletter_CleanVars($_REQUEST, 'letter_id', 0, 'int');
        // get letter object
        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        $letterTemplate = $template_path . $letterObj->getVar('letter_template') . ".tpl";

        $xoopsTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
        $xoopsTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME);
        $xoopsTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME);
        $xoopsTpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
        $xoopsTpl->assign('content', $letterObj->getVar('letter_content', 'n'));
        $xoopsTpl->assign('unsubscribe_url', XOOPS_URL . '/modules/xNewsletter/');
        $xoopsTpl->assign('catsubscr_id', '0');
        $xoopsTpl->assign('subscr_email', '');

        $letter_array = $letterObj->toArray();
        $letter_array['letter_content_templated'] = $xoopsTpl->fetch($letterTemplate);
        $letter_array['letter_created_timestamp'] = formatTimestamp($letterObj->getVar('letter_created'), $xnewsletter->getConfig('dateformat'));
        $letter_array['letter_submitter_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
        $xoopsTpl->assign('letter', $letter_array);
        break;

    case "list_letter" :
exit("IN_PROGRESS: use op=list_letters instead of op=list_letter");
break;
    case "list_letters" :
        $xoopsOption['template_main'] = "{$xnewsletter->getModule()->dirname()}_index_list_letters.tpl";
        include XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new xNewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_LIST, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $xoopsTpl->assign('welcome_message', $xnewsletter->getConfig('welcome_message'));

        // get letters array
        $criteria = new CriteriaCompo();
        $criteria->setSort("letter_id");
        $criteria->setOrder("DESC");
        $letterObjs = $xnewsletter->getHandler('xNewsletter_letter')->getAll($criteria);

        if ($xnewsletter->getHandler('xNewsletter_letter')->getCount() > 0) {
            // get newsletters available for current user
            $gperm_handler =& xoops_gethandler('groupperm');
            $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);

            $letters_array = array();
            foreach ($letterObjs as $letterObj) {
                $letter_array = $letterObj->toArray();
                $letter_array['letter_created_timestamp'] = formatTimestamp($letterObj->getVar('letter_created'), $xnewsletter->getConfig('dateformat'));
                $letter_array['letter_submitter_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
                $catsAvailableCount = 0;
                $cats_string = '';
                $cat_ids = explode('|' , $letterObj->getVar('letter_cats'));
                foreach ($cat_ids as $cat_id) {
                    $catObj = $xnewsletter->getHandler('xNewsletter_cat')->get($cat_id);
                    if ($gperm_handler->checkRight('newsletter_read_cat', $catObj->getVar('cat_id'), $groups, $xnewsletter->getModule()->mid())) {
                        $catsAvailableCount++;
                        unset($letter_array['letter_cats']);
                        $letter_array['letter_cats'][] = $catObj->toArray();
                    }
                    unset($catObj);
                }
                if ($catsAvailableCount > 0) {
                    $letters_array[] = $letter_array;
                }
            }
            if (count($letters_array) == 0) {
                redirect_header("index.php", 3, _MA_XNEWSLETTER_LETTER_NONEAVAIL);
            }
            $xoopsTpl->assign('letters', $letters_array);
        } else {
            redirect_header("index.php", 3, _MA_XNEWSLETTER_LETTER_NONEAVAIL);
        }
        break;
}

include 'footer.php';
