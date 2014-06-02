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

function xnewsletter_search($queryarray, $andor, $limit, $offset, $userid) {
    global $xoopsDB;

    $sql = "SELECT cat_id, cat_name, cat_submitter, cat_created";
    $sql.= " FROM {$xoopsDB->prefix("xnewsletter_cat")}";
    $sql.= " WHERE cat_created > 0";
    if ($userid != 0) {
        $sql .= " AND cat_submitter=" . intval($userid) . " ";
    }
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((cat_name LIKE '%{$queryarray[0]}%' OR cat_info LIKE '%{$queryarray[0]}%')";
        for ($i=1; $i < $count; ++$i) {
            $sql .= " {$andor} ";
            $sql .= "(cat_name LIKE '%{$queryarray[$i]}%' OR cat_info LIKE '%{$queryarray[0]}%')";
        }
        $sql .= ")";
    }
    $sql .= " ORDER BY cat_created DESC";
    $result = $xoopsDB->query($sql, $limit, $offset);
    $ret = array();
    $i = 0;
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]["image"] = "assets/images/icons/xn_search.png";
        $ret[$i]["link"] = "cat.php?cat_id=" . $myrow["cat_id"] . "";
        $ret[$i]["title"] = $myrow["cat_name"];
        $ret[$i]["time"] = $myrow["cat_created"];
        $ret[$i]["uid"] = $myrow["cat_submitter"];
        ++$i;
    }

    return $ret;
}
