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

$indexFile = XOOPS_UPLOAD_PATH . "/index.html";
$blankFile = XOOPS_UPLOAD_PATH . "/blank.gif";

//Creation of folder "uploads" for the module to the site root
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/index.html");

//Creation of the file accounts in uploads directory
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter/accounts";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/accounts/index.html");

//Creation of the file cat in uploads directory
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter/cat";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/cat/index.html");

//Creation of the file subscr in uploads directory
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter/subscr";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/subscr/index.html");

//Creation of the file catsubscr in uploads directory
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter/catsubscr";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/catsubscr/index.html");

//Creation of the file letter in uploads directory
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter/letter";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
    chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/letter/index.html");

//Creation of the file protocol in uploads directory
$module_uploads = XOOPS_ROOT_PATH."/uploads/xNewsletter/protocol";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/protocol/index.html");

//Creation of the folder letter_attachment in uploads directory for files
$module_uploads = XOOPS_ROOT_PATH . "/uploads/xNewsletter/attachments/";
if(!is_dir($module_uploads))
    mkdir($module_uploads, 0777);
chmod($module_uploads, 0777);
copy($indexFile, XOOPS_ROOT_PATH . "/uploads/xNewsletter/attachments/index.html");
