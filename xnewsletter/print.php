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
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once "header.php";

include_once XOOPS_ROOT_PATH . "/header.php";

error_reporting(0);
$xoopsLogger->activated = false;

$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', _AM_XNEWSLETTER_LETTER_ACTION_PREVIEW);

// get letter_id
$letter_id = xNewsletter_CleanVars($_REQUEST, 'letter_id', 'list', 'string');
// check letter_id
if ($letter_id < 1) {
    redirect_header("letter.php", 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
}

// get letter templates path
$letterTemplatePath = XNEWSLETTER_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
if (!is_dir($letterTemplatePath)) {
    $letterTemplatePath = XNEWSLETTER_ROOT_PATH . '/language/english/templates/';
}

$content = '';
$letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
if ($letterObj && $letterObj->getVar('letter_template') != '') {
    $letterTemplate = "{$letterTemplatePath}{$letterObj->getVar('letter_template')}.tpl";

    $xoopsTpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
    $xoopsTpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME);
    $xoopsTpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME);
    $xoopsTpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
    $xoopsTpl->assign('content', $letterObj->getVar('letter_content', 'n'));
    $xoopsTpl->assign('unsubscribe_url', XOOPS_URL . '/modules/xNewsletter/');
    //$tpl->assign('catsubscr_id', '0');
    $xoopsTpl->assign('subscr_email', '');

    $letter_array = $letterObj->toArray();
    $letter_array['letter_content_templated'] = $xoopsTpl->fetch($letterTemplate);
    $letter_array['letter_created_timestamp'] = formatTimestamp($letterObj->getVar('letter_created'), $xnewsletter->getConfig('dateformat'));
    $letter_array['letter_submitter_name'] = XoopsUserUtility::getUnameFromId($letterObj->getVar('letter_submitter'));
    $xoopsTpl->assign('letter', $letter_array);

// IN PROGRESS
// IN PROGRESS
// IN PROGRESS

    $content .= "<h2>{$letterObj->getVar('letter_title')}</h2>";
    $content .= "<div style='clear:both;'><div style='padding:10px;border:1px solid black;'>";
    $content .= $xoopsTpl->fetch($letterTemplate);
}

if ($content == '') {
    redirect_header("letter.php", 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERCONTENT);
}

$xoopsTpl->assign('xoopsConfig', $xoopsConfig);
$xoopsTpl->assign('xoops_meta_keywords', $xoops_meta_keywords);
$xoopsTpl->assign('xoops_meta_description', $xoops_meta_description);

xNewsletter_printPage($content);

//******************************************************************
//*********************** Printfunktion ****************************
//******************************************************************
/**
 * @param $content
 */
function xNewsletter_printPage($content) {
    global $xoopsConfig, $xoopsModule, $xoops_meta_keywords, $xoops_meta_description;
    $myts =& MyTextSanitizer::getInstance();
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo _LANGCODE; ?>" lang="<?php echo _LANGCODE; ?>">
<?php
    echo "<head>\n";
    echo '<title>' . $xoopsConfig['sitename'] . '</title>\n';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />\n';
    echo '<meta name="AUTHOR" content="' . $xoopsConfig['sitename'] . '" />\n';
    echo '<meta name="keywords" content="' . $xoops_meta_keywords . '" />\n';
    echo '<meta name="COPYRIGHT" content="Copyright (c) 2012 by ' . $xoopsConfig['sitename'] . '" />\n';
    echo '<meta name="DESCRIPTION" content="' . $xoops_meta_description . '" />\n';
    echo '<meta name="GENERATOR" content="XOOPS" />\n';
    echo '<!-- Sheet Css -->';
    echo '<link rel="stylesheet" type="text/css" media="all" title="Style sheet" href="' . XOOPS_URL . '/xoops.css" />\n';
    echo '<link rel="stylesheet" type="text/css" media="all" title="Style sheet" href="' . XOOPS_URL . '/themes/default/style.css" />\n';
    echo '<link rel="stylesheet" type="text/css" media="all" title="Style sheet" href="' . XOOPS_URL . '/modules/xnewsletter/assets/css/module.css" />\n';

?>
    <script type="text/javascript">
    // <![CDATA[
    /*------------------------------------------------------------------------------
    Function:       footnoteLinks()
    Author:         Aaron Gustafson (aaron at easy-designs dot net)
    Creation Date:  8 May 2005
    Version:        1.3
    Homepage:       http://www.easy-designs.net/code/footnoteLinks/
    License:        Creative Commons Attribution-ShareAlike 2.0 License
                    http://creativecommons.org/licenses/by-sa/2.0/
    Note:           This version has reduced functionality as it is a demo of
                    the script's development
    ------------------------------------------------------------------------------*/
    function footnoteLinks(containerID,targetID) {
        if (!document.getElementById ||
            !document.getElementsByTagName ||
            !document.createElement) return false;
        if (!document.getElementById(containerID) ||
            !document.getElementById(targetID)) return false;
          var container = document.getElementById(containerID);
          var target    = document.getElementById(targetID);
          var h2        = document.createElement('h2');
          addClass.apply(h2,['printOnly']);
          var h2_txt    = document.createTextNode('<?php echo "_MA_NW_LINKS"; ?>');
          h2.appendChild(h2_txt);
          var coll = container.getElementsByTagName('*');
          var ol   = document.createElement('ol');
          addClass.apply(ol,['printOnly']);
          var myArr = [];
          var thisLink;
          var num = 1;
          for (var i=0; i<coll.length; i++) {
            if ( coll[i].getAttribute('href') ||
                coll[i].getAttribute('cite') ) {
                thisLink = coll[i].getAttribute('href') ? coll[i].href : coll[i].cite;
                var note = document.createElement('sup');
                addClass.apply(note,['printOnly']);
                var note_txt;
                var j = inArray.apply(myArr,[thisLink]);
                if (j || j===0) { // if a duplirolee
                    // get the corresponding number from
                    // the array of used links
                    note_txt = document.createTextNode(j+1);
                } else {
                    // if not a duplirolee
                    var li     = document.createElement('li');
                    var li_txt = document.createTextNode(thisLink);
                    li.appendChild(li_txt);
                    ol.appendChild(li);
                    myArr.push(thisLink);
                    note_txt = document.createTextNode(num);
                    num++;
                }
                note.appendChild(note_txt);
                if (coll[i].tagName.toLowerCase() == 'blockquote') {
                    var lastChild = lastChildContainingText.apply(coll[i]);
                    lastChild.appendChild(note);
                } else {
                    coll[i].parentNode.insertBefore(note, coll[i].nextSibling);
                }
            }
        }
      target.appendChild(h2);
      target.appendChild(ol);

      return true;
    }
    // ]]>
    </script>
    <script type="text/javascript">
    // <![CDATA[
    /*------------------------------------------------------------------------------
    Excerpts from the jsUtilities Library
    Version:        2.1
    Homepage:       http://www.easy-designs.net/code/jsUtilities/
    License:        Creative Commons Attribution-ShareAlike 2.0 License
                    http://creativecommons.org/licenses/by-sa/2.0/
    Note:           If you change or improve on this script, please let us know.
    ------------------------------------------------------------------------------*/
    if (Array.prototype.push == null) {
        Array.prototype.push = function(item) {
            this[this.length] = item;

            return this.length;
        };
    };
    // ---------------------------------------------------------------------
    //                  function.apply (if unsupported)
    //           Courtesy of Aaron Boodman - http://youngpup.net
    // ---------------------------------------------------------------------
    if (!Function.prototype.apply) {
        Function.prototype.apply = function(oScope, args) {
            var sarg = [];
            var rtrn, call;
            if (!oScope) oScope = window;
            if (!args) args = [];
            for (var i = 0; i < args.length; i++) {
                sarg[i] = "args["+i+"]";
            };
            call = "oScope.__applyTemp__(" + sarg.join(",") + ");";
            oScope.__applyTemp__ = this;
            rtrn = eval(call);
            oScope.__applyTemp__ = null;

            return rtrn;
        };
    };
    function inArray(needle) {
        for (var i=0; i < this.length; i++) {
            if (this[i] === needle) {
                return i;
            }
        }

    return false;
    }
    function addClass(theClass) {
        if (this.className != '') {
            this.className += ' ' + theClass;
        } else {
            this.className = theClass;
        }
    }
    function lastChildContainingText() {
        var testChild = this.lastChild;
        var contentCntnr = ['p','li','dd'];
        while (testChild.nodeType != 1) {
            testChild = testChild.previousSibling;
        }
        var tag = testChild.tagName.toLowerCase();
        var tagInArr = inArray.apply(contentCntnr, [tag]);
        if (!tagInArr && tagInArr!==0) {
            testChild = lastChildContainingText.apply(testChild);
        }

        return testChild;
    }
    // ]]>
    </script>
    <style type="text/css" media="screen">
        .printOnly {
        display: none;
    }
    </style>
    </head>
    <body bgcolor="#ffffff" text="#000000" onload="window.print()">
<?php
    echo $content;
    echo '</div>\n';
    echo '</body>\n';
    echo '</html>\n';
}
