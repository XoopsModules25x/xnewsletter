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
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @param $options
 *
 * @return array
 */
function b_xnewsletter_letter($options) {
    global $xoopsUser;
    $myts = MyTextSanitizer::getInstance();
    $gperm_handler = xoops_getHandler('groupperm');
    $member_handler = xoops_getHandler('member');
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    $letter = array();
    $type_block = $options[0];
    $nb_letter = $options[1];
    $length_title = $options[2];

    array_shift($options);
    array_shift($options);
    array_shift($options);

    $letterCriteria = new CriteriaCompo();
    switch ($type_block) {
        // For the block: letter recents
        case "recent":
            $letterCriteria->setSort('letter_created');
            $letterCriteria->setOrder('DESC');
            break;
        // For the block: letter of today
        case "day":
            $letterCriteria->add(new Criteria('letter_created', strtotime(date('Y/m/d')), '>='));
            $letterCriteria->add(new Criteria('letter_created', strtotime(date('Y/m/d')) + 86400, '<='));
            $letterCriteria->setSort('letter_created');
            $letterCriteria->setOrder('ASC');
            break;
        // For the block: letter random
        case "random":
            $letterCriteria->setSort('RAND()');
            break;
    }

    $uid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
    if ($uid == 0) {
        $groups = array(XOOPS_GROUP_ANONYMOUS);
    } else {
        $groups = $member_handler->getGroupsByUser($uid) ;
    }

    $letterCriteria->setLimit($nb_letter);
    $letterObjs = $xnewsletter->getHandler('letter')->getAll($letterCriteria);
    foreach ($letterObjs as $letter_id => $letterObj) {
        $letter_cats = array();
        $letter_cats = explode('|', $letterObj->getVar('letter_cats'));
        $showCat = false;
        foreach ($letter_cats as $cat_id) {
            $showCat = $gperm_handler->checkRight('newsletter_read_cat', $cat_id, $groups, $xnewsletter->getModule()->mid());
            if ($showCat == true) {
                $letter[$letter_id]['letter_id'] = $letterObj->getVar('letter_id');
                $letter_title = $letterObj->getVar('letter_title');
                if ($length_title > 0 && strlen($letter_title) > $length_title) {
                    $letter_title = substr($letter_title, 0, $length_title) . '...';
                }
                $letter[$letter_id]['letter_title'] = $letter_title;
                // $letter[$letter_id]["letter_content"] = $letterObj->getVar("letter_content");
                // $letter[$letter_id]["letter_cats"] = $letterObj->getVar("letter_cats");
                // $letter[$letter_id]["letter_submitter"] = $letterObj->getVar("letter_submitter");
                $letter[$letter_id]['letter_created'] = formatTimestamp($letterObj->getVar('letter_created'), 'S');
                $letter[$letter_id]['href'] = XOOPS_URL . "/modules/{$xnewsletter->getModule()->dirname()}/letter.php?op=show_preview&letter_id={$letterObj->getVar('letter_id')}";
            }
        }
    }

    return $letter;
}

/**
 * @param $options
 *
 * @return string
 */
function b_xnewsletter_letter_edit($options) {
    $form = "" . _MB_XNEWSLETTER_LETTER_DISPLAY . "\n";
    $form .= "<input type=\"hidden\" name=\"options[0]\" value=\"{$options[0]}\" />";
    $form .= "<input name=\"options[1]\" size=\"5\" maxlength=\"255\" value=\"{$options[1]}\" type=\"text\" />";
    $form .= "<br />";
    $form .= "" . _MB_XNEWSLETTER_LETTER_TITLELENGTH . " : <input name=\"options[2]\" size=\"5\" maxlength=\"255\" value=\"{$options[2]}\" type=\"text\" />";
    $form .= "<br /><br />";
    array_shift($options);
    array_shift($options);
    array_shift($options);
    $form .= "<label name='lbl_cattodisplay'>" . _MB_XNEWSLETTER_LETTER_CATTODISPLAY . "</label>";
    $form .= "<br /><br />";

    return $form;
}
