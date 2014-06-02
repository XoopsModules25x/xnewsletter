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

include "admin_header.php";
xoops_cp_header();

//global $pathIcon,$indexAdmin;

// We recovered the value of the argument op in the URL$
$op = xNewsletter_CleanVars($_REQUEST, 'op', 'list', 'string');
$delete_att_1 = xNewsletter_CleanVars($_REQUEST, 'delete_attachment_1', 'none', 'string');
$delete_att_2 = xNewsletter_CleanVars($_REQUEST, 'delete_attachment_2', 'none', 'string');
$delete_att_3 = xNewsletter_CleanVars($_REQUEST, 'delete_attachment_3', 'none', 'string');
$delete_att_4 = xNewsletter_CleanVars($_REQUEST, 'delete_attachment_4', 'none', 'string');
$delete_att_5 = xNewsletter_CleanVars($_REQUEST, 'delete_attachment_5', 'none', 'string');

$letter_id = xNewsletter_CleanVars($_REQUEST, 'letter_id', 0, 'int');

if ($delete_att_1 != 'none') {
    $op = "delete_attachment";
    $id_del = 1;
} elseif ($delete_att_2 != 'none') {
    $op = "delete_attachment";
    $id_del = 2;
} elseif ($delete_att_3 != 'none') {
    $op = "delete_attachment";
    $id_del = 3;
} elseif ($delete_att_4 != 'none') {
    $op = "delete_attachment";
    $id_del = 4;
} elseif ($delete_att_5 != 'none') {
    $op = "delete_attachment";
    $id_del = 5;
} else {
    $id_del = 0;
}

switch ($op) {
    case "delete_attachment":
        $attachment_id = xNewsletter_CleanVars($_REQUEST, 'attachment_'.$id_del, 'none', 'string');
        if ($attachment_id == 'none') redirect_header("letter.php", 3, _AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID);
        $attachmentObj = $xnewsletter->getHandler('xNewsletter_attachment')->get($attachment_id);
        $attachment_name = $attachmentObj->getVar("attachment_name");

        if ($xnewsletter->getHandler('xNewsletter_attachment')->delete($attachmentObj, true)) {
            //delete file
            $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . "/";
            unlink($uploaddir . $attachment_name);

            $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
            $letterObj->setVar("letter_title", $_REQUEST["letter_title"]);
            $letterObj->setVar("letter_content", $_REQUEST["letter_content"]);
            $letterObj->setVar("letter_template", $_REQUEST["letter_template"]);
            $letterObj->setVar("letter_cats", $_REQUEST["letter_cats"]);
            $letterObj->setVar("letter_account", $_REQUEST["letter_account"]);
            $letterObj->setVar("letter_email_test", $_REQUEST["letter_email_test"]);

            $form = $letterObj->getForm(false, true);
            $form->display();
        } else {
            echo $attachmentObj->getHtmlErrors();
        }
    break;

    case "show_preview":
        global $XoopsTpl;

        echo $indexAdmin->addNavigation("letter.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, 'letter.php?op=list', 'list');
        echo $indexAdmin->renderButton();

        $template_path = XOOPS_ROOT_PATH . '/modules/xNewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
        if (!is_dir($template_path)) $template_path = XOOPS_ROOT_PATH . '/modules/xNewsletter/language/english/templates/';

        $tpl = new XoopsTpl();

        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        $template = $template_path . $letterObj->getVar("letter_template") . ".tpl";
        $tpl->assign('sex', _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
        $tpl->assign('firstname', _AM_XNEWSLETTER_SUBSCR_FIRSTNAME);
        $tpl->assign('lastname', _AM_XNEWSLETTER_SUBSCR_LASTNAME);
        $tpl->assign('title', $letterObj->getVar('letter_title', 'n')); // new from v1.3
        $tpl->assign('content', $letterObj->getVar("letter_content", "n"));
        $tpl->assign('unsubscribe_url', XOOPS_URL . '/modules/xNewsletter/'); // new from v1.3
        //$tpl->assign('catsubscr_id', "0");
        $tpl->assign('subscr_email', '');

        echo "<h2>" . $letterObj->getVar("letter_title") . "</h2>";
        echo "<div style='clear:both'><div style='padding:10px;border:1px solid black'>";
        echo $tpl->fetch($template);
        echo "</div></div>";
    break;

    case "list":
    default:
        echo $indexAdmin->addNavigation('letter.php');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, 'letter.php?op=new_letter', 'add');
        echo $indexAdmin->renderButton();

        $limit = $GLOBALS['xoopsModuleConfig']['adminperpage'];
        $criteria = new CriteriaCompo();
        $criteria->setSort("letter_id");
        $criteria->setOrder("DESC");
        $numrows = $xnewsletter->getHandler('xNewsletter_letter')->getCount();
        $start = xNewsletter_CleanVars($_REQUEST, 'start', 0, 'int');
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $letter_arr = $xnewsletter->getHandler('xNewsletter_letter')->getall($criteria);
        if ($numrows > $limit) {
            include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
            $pagenav = new XoopsPageNav($numrows, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($numrows>0) {
            echo "<table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>"._AM_XNEWSLETTER_LETTER_ID."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_TITLE."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_TEMPLATE."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_CATS."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_ATTACHMENT."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_ACCOUNT."</th>
                    <th class='center width10'>"._AM_XNEWSLETTER_LETTER_EMAIL_TEST."</th>
                    <th class='center'>"._AM_XNEWSLETTER_PROTOCOL_LAST_STATUS."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_SUBMITTER."</th>
                    <th class='center'>"._AM_XNEWSLETTER_LETTER_CREATED."</th>
                    <th class='center width10'>"._AM_XNEWSLETTER_FORMACTION."</th>
                </tr>";

            $class = "odd";

            foreach (array_keys($letter_arr) as $i) {
                echo "<tr class='" . $class . "'>";
                $class = ($class == "even") ? "odd" : "even";
                echo "<td class='center'>" . $i."</td>";
                echo "<td class='center'>".$letter_arr[$i]->getVar("letter_title")."</td>";
                $verify_val = $letter_arr[$i]->getVar("letter_template") == '' ? "&nbsp;" : $letter_arr[$i]->getVar("letter_template");
                echo "<td class='center'>".$verify_val."</td>";
                $letter_cats = "";
                $j = 0;
                $cat_arr = explode("|" , $letter_arr[$i]->getVar("letter_cats"));
                foreach ($cat_arr as $cat) {
                    ++$j;
                    $cat_obj = $xnewsletter->getHandler('xNewsletter_cat')->get($cat);
                    if (count($cat_arr)>1) $letter_cats .= "($j) ";
                    if (is_object($cat_obj)) {
                        $letter_cats .= $cat_obj->getVar("cat_name") . "<br/>";
                    } else {
                        $letter_cats .= "Invalid cat_name";
                    }
                }
                $letter_cats = substr($letter_cats, 0, -5);
                echo "<td class='center'>".$letter_cats."</td>";

                $crit_att = new CriteriaCompo();
                $crit_att->add(new Criteria('attachment_letter_id', $letter_arr[$i]->getVar("letter_id")));
                $num_attachments = $xnewsletter->getHandler('xNewsletter_attachment')->getCount($crit_att);
                echo "<td class='center'>" . $num_attachments . "</td>";

                $crit_accounts = new CriteriaCompo();
                $crit_accounts->setSort("accounts_id");
                $crit_accounts->setOrder("ASC");
                $obj_account = $xnewsletter->getHandler('xNewsletter_accounts')->get($letter_arr[$i]->getVar("letter_account"));
                $letter_account = ( $obj_account ) ? $obj_account->getVar("accounts_name") : _NONE;
                echo "<td class='center'>" . $letter_account . "</td>";

                $letter_email_test = $letter_arr[$i]->getVar("letter_email_test");
                if ($letter_email_test=='') $letter_email_test = "&nbsp;";
                echo "<td class='center'>".$letter_email_test."</td>";

                //take last item protocol_subscriber_id=0 from table protocol as actual status
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('protocol_letter_id', $letter_arr[$i]->getVar("letter_id")));
                $criteria->add(new Criteria('protocol_subscriber_id', '0'));
                $criteria->setSort("protocol_id");
                $criteria->setOrder("DESC");
                $criteria->setLimit(1);
                $protocol_arr = $xnewsletter->getHandler('xNewsletter_protocol')->getall($criteria);
                $protocol_status = "";
                $protocol_letter_id = 0;
                foreach ($protocol_arr as $protocol) {
                    $protocol_status .= $protocol->getVar("protocol_status");
                    $protocol_letter_id = $protocol->getVar("protocol_letter_id");
                }
                echo "<td class='center'><a href=' protocol.php?op=list_letter&letter_id=".$protocol_letter_id."'>".$protocol_status."</a></td>";

                echo "<td class='center'>" . XoopsUser::getUnameFromId($letter_arr[$i]->getVar("letter_submitter"), "S") . "</td>";
                echo "<td class='center'>" . formatTimeStamp($letter_arr[$i]->getVar("letter_created"), "s") . "</td>";

                echo "<td class='center width10'>";
                echo "    <a href='letter.php?op=edit_letter&letter_id=" . $i . "'>
                  <img src=".XNEWSLETTER_ICONS_URL."/xn_edit.png alt='"._EDIT."' title='" . _EDIT . "' style='padding:1px' />
                </a>";
                echo "    <a href='letter.php?op=clone_letter&letter_id=".$i."'>
                  <img src=".XNEWSLETTER_ICONS_URL."/xn_clone.png alt='"._CLONE."' title='"._CLONE."' style='padding:1px' />
                </a>";
                echo "  <a href='letter.php?op=delete_letter&letter_id=".$i."'>
                  <img src=".XNEWSLETTER_ICONS_URL."/xn_delete.png alt='"._DELETE."' title='"._DELETE."'  style='padding:1px' />
                </a>";
                echo "  <a href='letter.php?op=show_preview&letter_id=".$i."'>
                  <img src=".XNEWSLETTER_ICONS_URL."/xn_preview.png alt='"._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW."' title='"._AM_XNEWSLETTER_LETTER_ACTION_PREVIEW."' style='padding:1px' />
                </a>";
                echo "  <a href='sendletter.php?op=send_test&letter_id=".$i."'>
                  <img src=".XNEWSLETTER_ICONS_URL."/xn_sendtest.png alt='"._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST."' title='"._AM_XNEWSLETTER_LETTER_ACTION_SENDTEST."' style='padding:1px' />
                </a>";
                echo "  <a href='sendletter.php?op=send_letter&letter_id=".$i."'>
                  <img src=".XNEWSLETTER_ICONS_URL."/xn_send.png alt='"._AM_XNEWSLETTER_LETTER_ACTION_SEND."' title='"._AM_XNEWSLETTER_LETTER_ACTION_SEND."' style='padding:1px' />
                </a>";
                echo "  <a href='sendletter.php?op=resend_letter&letter_id=".$i."'>
                    <img src=".XNEWSLETTER_ICONS_URL."/xn_resend.png alt='"._AM_XNEWSLETTER_LETTER_ACTION_RESEND."' title='"._AM_XNEWSLETTER_LETTER_ACTION_RESEND."' style='padding:1px' />
                  </a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table><br /><br />";
            echo "<br /><div class='center'>" . $pagenav . "</div><br />";
        } else {
            echo "<table class='outer width100' cellspacing='1'>
                    <tr>
                      <th class='center width2'>"._AM_XNEWSLETTER_LETTER_ID."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_TITLE."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_TEMPLATE."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_ATTACHMENT."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_ACCOUNT."</th>
                        <th class='center width10'>"._AM_XNEWSLETTER_LETTER_EMAIL_TEST."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_STATUS."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_SUBMITTER."</th>
                        <th class='center'>"._AM_XNEWSLETTER_LETTER_CREATED."</th>
                        <th class='center width10'>"._AM_XNEWSLETTER_FORMACTION."</th>
                    </tr>";
            echo "</table><br /><br />";
        }

    break;

    case "new_letter":
        echo $indexAdmin->addNavigation("letter.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, 'letter.php?op=list', 'list');
        echo $indexAdmin->renderButton();

        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->create();
        $form = $letterObj->getForm(false, true);
        $form->display();
    break;

    case "save_letter":

        if ( !$GLOBALS["xoopsSecurity"]->check() ) {
            redirect_header("letter.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
        }
        if ($letter_id > 0)
          $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        else
          $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->create();

        //Form letter_title
        $letterObj->setVar("letter_title", $_REQUEST["letter_title"]);
        //Form letter_content
        $letterObj->setVar("letter_content", $_REQUEST["letter_content"]);
        //Form letter_template
        $letterObj->setVar("letter_template", $_REQUEST["letter_template"]);
        //Form letter_cats
        $letter_cats = "";
        $cat_arr = isset($_REQUEST["letter_cats"]) ? $_REQUEST["letter_cats"] : "";
        if (is_array($cat_arr) && count($cat_arr) > 0) {
            foreach ($cat_arr as $cat) {
                $letter_cats .= $cat."|";
            }
            $letter_cats = substr($letter_cats, 0, -1);
        } else {
            $letter_cats = $cat_arr;
        }
        $letterObj->setVar("letter_cats", $letter_cats);

        //Form letter_account
        $letterObj->setVar("letter_account", $_REQUEST["letter_account"]);
        //Form letter_email_test
        $letterObj->setVar("letter_email_test", $_REQUEST["letter_email_test"]);
        //Form letter_submitter
        $letterObj->setVar("letter_submitter", xNewsletter_CleanVars($_REQUEST, "letter_submitter", 0, 'int'));
        //Form letter_created
        $letterObj->setVar("letter_created", xNewsletter_CleanVars($_REQUEST, 'letter_created', time(), 'int'));

        if ($xnewsletter->getHandler('xNewsletter_letter')->insert($letterObj)) {

            $letter_id = $letterObj->getVar("letter_id");
            //upload attachments
            $uploaded_files = array();
            include_once XOOPS_ROOT_PATH . "/class/uploader.php";
            $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . "/";
            //check upload_dir
            if (!is_dir($uploaddir)) {
                $indexFile = XOOPS_UPLOAD_PATH . "/index.html";
                mkdir($uploaddir, 0777);
                chmod($uploaddir, 0777);
                copy($indexFile, $uploaddir . "index.html");
            }

            for ($upl = 0 ;$upl < 5; ++$upl) {
                $uploader = new XoopsMediaUploader($uploaddir, $xnewsletter->getConfig('xn_mimetypes'), $xnewsletter->getConfig('xn_maxsize'), null, null);
                if ($uploader->fetchMedia(@$_POST['xoops_upload_file'][$upl])) {
                    //$uploader->setPrefix("xn_") ; keep original name
                    $uploader->fetchMedia($_POST['xoops_upload_file'][$upl]);
                    if (!$uploader->upload()) {
                        $errors = $uploader->getErrors();
                        redirect_header("javascript:history.go(-1)",3, $errors);
                    } else {
                        $uploaded_files[] = array("name" => $uploader->getSavedFileName(), "type" => $uploader->getMediaType());
                    }
                }

            }

            //create items in attachments
            foreach ($uploaded_files as $file) {
                $attachmentObj =& $xnewsletter->getHandler('xNewsletter_attachment')->create();
                //Form attachment_letter_id
                $attachmentObj->setVar("attachment_letter_id", $letter_id);
                //Form attachment_name
                $attachmentObj->setVar("attachment_name", $file["name"]);
                //Form attachment_type
                $attachmentObj->setVar("attachment_type", $file["type"]);
                //Form attachment_submitter
                $attachmentObj->setVar("attachment_submitter", $xoopsUser->uid());
                //Form attachment_created
                $attachmentObj->setVar("attachment_created", time());

                $xnewsletter->getHandler('xNewsletter_attachment')->insert($attachmentObj);
            }
            //create item in protocol
            $protocolObj =& $xnewsletter->getHandler('xNewsletter_protocol')->create();
            $protocolObj->setVar("protocol_letter_id", $letter_id);
            $protocolObj->setVar("protocol_subscriber_id", '0');
            $action = "";
            $action = isset($_REQUEST["letter_action"]) ? $_REQUEST["letter_action"] : 0;
            switch ($action) {
                case _AM_XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW:
                    $url = "letter.php?op=show_preview&letter_id=".$letter_id;
                    break;
                case _AM_XNEWSLETTER_LETTER_ACTION_VAL_SEND:
                    $url = "sendletter.php?op=send_letter&letter_id=".$letter_id;
                    break;
                case _AM_XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST:
                    $url = "sendletter.php?op=send_test&letter_id=".$letter_id;
                    break;
                default:
                    $url = "letter.php?op=list";
                    break;
            }
            $protocolObj->setVar("protocol_status", _AM_XNEWSLETTER_LETTER_ACTION_SAVED);
            $protocolObj->setVar("protocol_submitter", $xoopsUser->uid());
            $protocolObj->setVar("protocol_created", time());
            $protocolObj->setVar("protocol_success", 1);
            if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($protocolObj)) {
                //create protocol is ok
                redirect_header($url, 3, _AM_XNEWSLETTER_FORMOK);
            } else {
                echo "Error create protocol: " . $protocolObj->getHtmlErrors();
            }
        }

        echo "Error create letter: " . $letterObj->getHtmlErrors();
        break;

    case "edit_letter":
        echo $indexAdmin->addNavigation("letter.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, 'letter.php?op=new_letter', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, 'letter.php?op=list', 'list');
        echo $indexAdmin->renderButton();
        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        $form = $letterObj->getForm(false, true);
        $form->display();
    break;

    case "clone_letter":
        echo $indexAdmin->addNavigation("letter.php");
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWLETTER, 'letter.php?op=new_letter', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_LETTERLIST, 'letter.php?op=list', 'list');
        echo $indexAdmin->renderButton();
        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        $letterObj->setNew();
        $letterObj->setVar('letter_id', 0);
        $letterObj->setVar('letter_title', sprintf('cloned: %s', $letterObj->getVar('letter_title')));
        $form = $letterObj->getForm('letter.php', true);
        $form->display();
    break;

    case "delete_letter":
        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        if (isset($_REQUEST["ok"]) && $_REQUEST["ok"] == 1) {
            if ( !$GLOBALS["xoopsSecurity"]->check() ) {
                redirect_header("letter.php", 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }

            if ($xnewsletter->getHandler('xNewsletter_letter')->delete($letterObj)) {
                //delete protocol
                $sql = "DELETE FROM `".$xoopsDB->prefix("xnewsletter_protocol")."` WHERE `protocol_letter_id`=".$letter_id;
                $result = $xoopsDB->query($sql) || die("MySQL-Error: " . mysql_error());

                //delete attachments
                $crit_att = new CriteriaCompo();
                $crit_att->add(new Criteria("attachment_letter_id", $letter_id));
                $attachment_arr = $xnewsletter->getHandler('xNewsletter_attachment')->getall($crit_att);
                foreach (array_keys($attachment_arr) as $attachment_id) {
                    $attachmentObj = $xnewsletter->getHandler('xNewsletter_attachment')->get($attachment_id);
                    $attachment_name = $attachmentObj->getVar("attachment_name");
                    $xnewsletter->getHandler('xNewsletter_attachment')->delete($attachmentObj, true);
                    //delete file
                    $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . "/";
                    unlink($uploaddir . $attachment_name);
                }
                redirect_header("letter.php", 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $letterObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array("ok" => 1, "letter_id" => $letter_id, "op" => "delete_letter"), $_SERVER["REQUEST_URI"], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $letterObj->getVar("letter_title")));
        }
    break;
}
include "admin_footer.php";
