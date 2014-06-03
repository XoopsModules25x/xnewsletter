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
include_once dirname(dirname(__FILE__)) . '/include/common.php';

/**
 * @param $options
 *
 * @return array
 */
function b_xnewsletter_letter($options) {
    global $xoopsUser;
    $myts = MyTextSanitizer::getInstance();
    $gperm_handler = xoops_gethandler('groupperm');
    $member_handler = xoops_gethandler('member');
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    $letter = array();
    $type_block = $options[0];
    $nb_letter = $options[1];
    $length_title = $options[2];

    $criteria = new CriteriaCompo();
    array_shift($options);
    array_shift($options);
    array_shift($options);

    switch ($type_block) {
        // For the block: letter recents
        case "recent":
            $criteria->setSort('letter_created');
            $criteria->setOrder("DESC");
            break;
        // For the block: letter of today
        case "day":
            $criteria->add(new Criteria('letter_created', strtotime(date('Y/m/d')), '>='));
            $criteria->add(new Criteria('letter_created', strtotime(date('Y/m/d')) + 86400, '<='));
            $criteria->setSort('letter_created');
            $criteria->setOrder('ASC');
            break;
        // For the block: letter random
        case "random":
            $criteria->setSort('RAND()');
            break;
    }

    $currentUid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
    if ($currentUid == 0) {
        $my_group_ids = array(XOOPS_GROUP_ANONYMOUS);
    } else {
        $my_group_ids = $member_handler->getGroupsByUser($currentUid) ;
    }

    $criteria->setLimit($nb_letter);
    $letter_arr = $xnewsletter->getHandler('xnewsletter_letter')->getall($criteria);
    foreach (array_keys($letter_arr) as $i) {
        $letter_cat_arr = array();
        $letter_cat_arr = explode('|', $letter_arr[$i]->getVar('letter_cats'));
        $showCat = false;
        foreach (array_keys($letter_cat_arr) as $cat_id) {
            $showCat = $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $my_group_ids, $xnewsletter->getModule()->mid());
            if ($showCat == true) {
                $letter[$i]['letter_id'] = $letter_arr[$i]->getVar('letter_id');
                $letter_title = $letter_arr[$i]->getVar('letter_title');
                if ($length_title > 0 && strlen($letter_title) > $length_title) {
                    $letter_title = substr($letter_title, 0, $length_title) . '...';
                }
                $letter[$i]['letter_title'] = $letter_title;
                // $letter[$i]["letter_content"] = $letter_arr[$i]->getVar("letter_content");
                // $letter[$i]["letter_cats"] = $letter_arr[$i]->getVar("letter_cats");
                // $letter[$i]["letter_submitter"] = $letter_arr[$i]->getVar("letter_submitter");
                $letter[$i]['letter_created'] = formatTimeStamp($letter_arr[$i]->getVar('letter_created'), 'S');
                $letter[$i]['href'] = XOOPS_URL . "/modules/{$xnewsletter->getModule()->dirname()}/letter.php?op=show_preview&letter_id={$letter_arr[$i]->getVar('letter_id')}";
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
