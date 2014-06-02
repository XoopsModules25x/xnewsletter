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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';

function b_xnewsletter_catsubscr($options) {
    global $xoopsUser;
    $xnewsletter = xNewsletterxNewsletter::getInstance();
    $myts = MyTextSanitizer::getInstance();

    $catsubscr = array();
    $type_block = $options[0];
    $nb_catsubscr = $options[1];
    $length_title = $options[2];

    $criteria = new CriteriaCompo();
    array_shift($options);
    array_shift($options);
    array_shift($options);

    switch ($type_block) {
        // For the block: catsubscr recents
        case "recent":
            $criteria->setSort("catsubscr_created");
            $criteria->setOrder("DESC");
            break;
        // For the block: catsubscr of today
        case "day":
            $criteria->add(new Criteria("catsubscr_created", strtotime(date("Y/m/d")), ">="));
            $criteria->add(new Criteria("catsubscr_created", strtotime(date("Y/m/d"))+86400, "<="));
            $criteria->setSort("catsubscr_created");
            $criteria->setOrder("ASC");
            break;
    }

    $criteria->setLimit($nb_catsubscr);
    $catsubscr_arr = $xnewsletter->getHandler('xNewsletter_catsubscr')->getall($criteria);
    foreach (array_keys($catsubscr_arr) as $i) {
        $cat_id = $catsubscr_arr[$i]->getVar("catsubscr_catid");
        if (in_array($cat_id, $options) || $options[0] == '0') {
            $subscr_id = $catsubscr_arr[$i]->getVar("catsubscr_subscrid");
            $subscr_arr = $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);
            $email = $subscr_arr->getVar("subscr_email");
            if ($length_title > 0 && strlen($email) > $length_title)
                $email = substr($email, 0, $length_title) . "...";
            $catsubscr[$i]["catsubscr_email"] = $email;

            $cat_arr = $xnewsletter->getHandler('xNewsletter_cat')->get($cat_id);
            $cat_name = $cat_arr->getVar("cat_name");
            if ($length_title > 0 && strlen($cat_name) > $length_title)
                $cat_name = substr($cat_name, 0, $length_title) . "...";
            $catsubscr[$i]["catsubscr_newsletter"] = $cat_name;
            $catsubscr[$i]["catsubscr_created"] = formatTimeStamp($catsubscr_arr[$i]->getVar("catsubscr_created"), "S");
        }
    }

    return $catsubscr;
}

function b_xnewsletter_catsubscr_edit($options) {
    global $xoopsUser;
    $xnewsletter = xNewsletterxNewsletter::getInstance();

    $form = "" . _MB_XNEWSLETTER_LETTER_DISPLAY . "\n";
    $form .= "<input type=\"hidden\" name=\"options[0]\" value=\"" . $options[0] . "\" />";
    $form .= "<input name=\"options[1]\" size=\"5\" maxlength=\"255\" value=\"" . $options[1] . "\" type=\"text\" />&nbsp;<br />";
    $form .= "" . _MB_XNEWSLETTER_LETTER_TITLELENGTH . " : <input name=\"options[2]\" size=\"5\" maxlength=\"255\" value=\"" . $options[2] . "\" type=\"text\" /><br /><br />";
    array_shift($options);
    array_shift($options);
    array_shift($options);
    $form .= "" . _MB_XNEWSLETTER_LETTER_CATTODISPLAY . "<br /><select name=\"options[]\" multiple=\"multiple\" size=\"5\">";
    $form .= "<option value=\"0\" " . (array_search(0, $options) === false ? "" : "selected=\"selected\"") . ">" ._MB_XNEWSLETTER_CATSUBSCR_ALLCAT . "</option>";

    $cat_criteria = new CriteriaCompo();
    $cat_criteria->setSort("cat_id");
    $cat_criteria->setOrder("ASC");
    $cat_arr = $xnewsletter->getHandler('xNewsletter_cat')->getall($cat_criteria);
    foreach (array_keys($cat_arr) as $i) {
        $form .= "<option value=\"" . $i . "\" " . (array_search($i, $options) === false ? "" : "selected=\"selected\"") . ">" . $cat_arr[$i]->getVar("cat_name") . "</option>";
    }
    $form .= "</select>";

    return $form;
}
