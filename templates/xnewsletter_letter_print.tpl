<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$smarty.const._LANGCODE}>" lang="<{$smarty.const._LANGCODE}>">

<head>
    <title>' . $xoopsConfig['sitename'] . '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="AUTHOR" content="<{$xoopsConfig.sitename}>">
    <meta name="keywords" content="<{$xoops_meta_keywords}>">
    <meta name="COPYRIGHT" content="Copyright (c) 2012 by <{$xoopsConfig.sitename}>">
    <meta name="DESCRIPTION" content="<{$xoops_meta_description}>">
    <meta name="GENERATOR" content="XOOPS">
    <!-- Sheet Css -->
    <link rel="stylesheet" type="text/css" media="all" title="Style sheet"
          href="<{$smarty.const.XOOPS_URL}>/xoops.css">
    <link rel="stylesheet" type="text/css" media="all" title="Style sheet"
          href="<{$smarty.const.XOOPS_URL}>/themes/default/style.css">
    <link rel="stylesheet" type="text/css" media="all" title="Style sheet"
          href="<{$smarty.const.XOOPS_URL}>/modules/xnewsletter/assets/css/module.css">


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
        function footnoteLinks(containerID, targetID) {
            if (!document.getElementById || !document.getElementsByTagName || !document.createElement) return false;
            if (!document.getElementById(containerID) || !document.getElementById(targetID)) return false;
            var container = document.getElementById(containerID);
            var target = document.getElementById(targetID);
            var h2 = document.createElement('h2');
            addClass.apply(h2, ['printOnly']);
            var h2_txt = document.createTextNode('<?php echo "_MA_NW_LINKS"; ?>');
            h2.appendChild(h2_txt);
            var coll = container.getElementsByTagName('*');
            var ol = document.createElement('ol');
            addClass.apply(ol, ['printOnly']);
            var myArr = [];
            var thisLink;
            var num = 1;
            for (var i = 0; i < coll.length; i++) {
                if (coll[i].getAttribute('href') ||
                        coll[i].getAttribute('cite')) {
                    thisLink = coll[i].getAttribute('href') ? coll[i].href : coll[i].cite;
                    var note = document.createElement('sup');
                    addClass.apply(note, ['printOnly']);
                    var note_txt;
                    var j = inArray.apply(myArr, [thisLink]);
                    if (j || j === 0) { // if a duplirolee
                        // get the corresponding number from
                        // the array of used links
                        note_txt = document.createTextNode(j + 1);
                    } else {
                        // if not a duplirolee
                        var li = document.createElement('li');
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
            Array.prototype.push = function (item) {
                this[this.length] = item;
                return this.length;
            };
        }
        ;
        // ---------------------------------------------------------------------
        //                  function.apply (if unsupported)
        //           Courtesy of Aaron Boodman - http://youngpup.net
        // ---------------------------------------------------------------------
        if (!Function.prototype.apply) {
            Function.prototype.apply = function (oScope, args) {
                var sarg = [];
                var rtrn, call;
                if (!oScope) oScope = window;
                if (!args) args = [];
                for (var i = 0; i < args.length; i++) {
                    sarg[i] = "args[" + i + "]";
                }
                ;
                call = "oScope.__applyTemp__(" + sarg.join(",") + ");";
                oScope.__applyTemp__ = this;
                rtrn = eval(call);
                oScope.__applyTemp__ = null;
                return rtrn;
            };
        }
        ;
        function inArray(needle) {
            for (var i = 0; i < this.length; i++) {
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
            var contentCntnr = ['p', 'li', 'dd'];
            while (testChild.nodeType != 1) {
                testChild = testChild.previousSibling;
            }
            var tag = testChild.tagName.toLowerCase();
            var tagInArr = inArray.apply(contentCntnr, [tag]);
            if (!tagInArr && tagInArr !== 0) {
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
<h2><{$letter.letter_title}></h2>
<div style='padding:10px;border:1px solid black;'>
    <{$letter.letter_content_templated}>
</div>
</body>
</html>
