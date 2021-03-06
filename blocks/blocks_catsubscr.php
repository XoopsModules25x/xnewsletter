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

use XoopsModules\Xnewsletter;

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once dirname(__DIR__) . '/include/common.php';

/**
 * @param $options
 *
 * @return array
 */
function b_xnewsletter_catsubscr($options)
{
    global $xoopsUser;
    $helper = Xnewsletter\Helper::getInstance();
    $myts   = \MyTextSanitizer::getInstance();

    $catsubscrArray = [];
    $type_block     = $options[0];
    $nb_catsubscr   = $options[1];
    $length_title   = $options[2];

    array_shift($options);
    array_shift($options);
    array_shift($options);

    $catsubscrCriteria = new \CriteriaCompo();
    switch ($type_block) {
        // For the block: catsubscr recents
        case 'recent':
            $catsubscrCriteria->setSort('catsubscr_created');
            $catsubscrCriteria->setOrder('DESC');
            break;
        // For the block: catsubscr of today
        case 'day':
            $catsubscrCriteria->add(new \Criteria('catsubscr_created', strtotime(date('Y/m/d')), '>='));
            $catsubscrCriteria->add(new \Criteria('catsubscr_created', strtotime(date('Y/m/d')) + 86400, '<='));
            $catsubscrCriteria->setSort('catsubscr_created');
            $catsubscrCriteria->setOrder('ASC');
            break;
    }

    $catsubscrCriteria->setLimit($nb_catsubscr);
    $catsubscrObjs = $helper->getHandler('Catsubscr')->getAll($catsubscrCriteria);
    foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
        $cat_id = $catsubscrObj->getVar('catsubscr_catid');
        if (in_array($cat_id, $options) || '0' == $options[0]) {
            $subscr_id = $catsubscrObj->getVar('catsubscr_subscrid');
            $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
            $email     = $subscrObj->getVar('subscr_email');
            if ($length_title > 0 && mb_strlen($email) > $length_title) {
                $email = mb_substr($email, 0, $length_title) . '...';
            }
            $catsubscrArray[$catsubscr_id]['catsubscr_email'] = $email;

            $catObj   = $helper->getHandler('Cat')->get($cat_id);
            $cat_name = $catObj->getVar('cat_name');
            if ($length_title > 0 && mb_strlen($cat_name) > $length_title) {
                $cat_name = mb_substr($cat_name, 0, $length_title) . '...';
            }
            $catsubscrArray[$catsubscr_id]['catsubscr_newsletter'] = $cat_name;
            $catsubscrArray[$catsubscr_id]['catsubscr_created']    = formatTimestamp($catsubscrObj->getVar('catsubscr_created'), 'S');
        }
    }

    return $catsubscrArray;
}

/**
 * @param $options
 *
 * @return string
 */
function b_xnewsletter_catsubscr_edit($options)
{
    global $xoopsUser;
    $helper = Xnewsletter\Helper::getInstance();

    $form = '' . _MB_XNEWSLETTER_LETTER_DISPLAY . "\n";
    $form .= '<input type="hidden" name="options[0]" value="' . $options[0] . '">';
    $form .= '<input name="options[1]" size="5" maxlength="255" value="' . $options[1] . '" type="text">&nbsp;<br>';
    $form .= '' . _MB_XNEWSLETTER_LETTER_TITLELENGTH . ' : <input name="options[2]" size="5" maxlength="255" value="' . $options[2] . '" type="text"><br><br>';
    array_shift($options);
    array_shift($options);
    array_shift($options);
    $form .= '' . _MB_XNEWSLETTER_LETTER_CATTODISPLAY . '<br><select name="options[]" multiple="multiple" size="5">';
    $form .= '<option value="0" ' . (!in_array(0, $options, true) ? '' : 'selected="selected"') . '>' . _MB_XNEWSLETTER_CATSUBSCR_ALLCAT . '</option>';

    $catCriteria = new \CriteriaCompo();
    $catCriteria->setSort('cat_id');
    $catCriteria->setOrder('ASC');
    $catObjs = $helper->getHandler('Cat')->getAll($catCriteria);
    foreach ($catObjs as $cat_id => $catObj) {
        $form .= '<option value="' . $cat_id . '" ' . (!in_array($cat_id, $options, true) ? '' : 'selected="selected"') . '>' . $catObj->getVar('cat_name') . '</option>';
    }
    $form .= '</select>';

    return $form;
}
