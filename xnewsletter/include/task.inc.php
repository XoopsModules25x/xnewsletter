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

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
include_once dirname(__FILE__) . '/common.php';

function xNewsletter_createTasks($op, $letter_id, $xn_send_in_packages, $xn_send_in_packages_time) {
    global $xoopsUser, $xoopsDB;
    $xnewsletter = xNewsletterxNewsletter::getInstance();

    $submitter = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid(): 0;

    //check data before creating task list
    if ($letter_id == 0) {
        redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID);
    }

    $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
    if (count($letterObj) == 0) {
        redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID);
    }

    //read categories
    $letter_cats = $letterObj->getVar('letter_cats');
    if ($letter_cats == '') {
        //no cats
        redirect_header('letter.php', 3, _MA_XNEWSLETTER_LETTER_NONEAVAIL);
    }

    if ($op == 'send_test') {
        //check for valid email for testing
        $letter_email_test = $letterObj->getVar('letter_email_test');
        if ($letter_email_test == '')
            redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_EMAIL);
    }

    //get emails of subscribers
    $recipients = array();
    if ($op == 'send_test') {
        $recipients[] = 0;
    } else {
        //read all subscribers
        $sql = "SELECT subscr_id, subscr_actkey ";
        $sql .= " FROM {$xoopsDB->prefix('mod_xnewsletter_subscr')} INNER JOIN {$xoopsDB->prefix('mod_xnewsletter_catsubscr')} ON subscr_id = catsubscr_subscrid ";
        $sql .= " WHERE subscr_activated=1 AND (((catsubscr_catid) In (";
        $sql .= str_replace('|', ',', $letter_cats);
        $sql .= "))) GROUP BY subscr_id;";

        $subscribers = $xoopsDB->query($sql) or die();

        while ($subscriber = $xoopsDB->fetchArray($subscribers)) {
            $subscr_id = $subscriber["subscr_id"];
            if ($op == 'resend_letter') {
                //read subscribers, where send failed
                $protocol_criteria = new CriteriaCompo();
                $protocol_criteria->add(new Criteria('protocol_letter_id', $letter_id));
                $protocol_criteria->add(new Criteria('protocol_subscriber_id', $subscr_id));
                $protocol_criteria->add(new Criteria('protocol_success', 1));
                $protocolCount = $xnewsletter->getHandler('xNewsletter_protocol')->getCount($protocol_criteria);
                if ($protocolCount > 0) $subscr_id = 0; //letter already successfully sent
            }
            if ($subscr_id > 0) {
                if ($subscriber['subscr_actkey'] == '') {
                    $u = $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);
                    $subscriber['subscr_actkey'] = xoops_makepass();
                    $u->setVar('subscr_actkey', $subscriber['subscr_actkey']);
                    $xnewsletter->getHandler('xNewsletter_subscr')->insert($u);
                    unset($u);
                }
                $recipients[] = $subscriber['subscr_id'];
            }
        }
    }

    if (count($recipients) == 0) {
        redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_SUBSCR);
    } else {
        //creating task list
        $counter = 0;
        $task_starttime = time() - 1;
        foreach ($recipients as $subscriber_id) {
            //calculate start time, if letter should be sent in packages
            if ($xn_send_in_packages > 0) {
                if ($counter == $xn_send_in_packages) {
                    $task_starttime = $task_starttime + 60 * $xn_send_in_packages_time;
                    $counter = 0;
                }
            }
            $counter++;
            // create task list item
            $sql = "INSERT INTO `{$xoopsDB->prefix('mod_xnewsletter_task')}`";
            $sql .= " (`task_letter_id`, `task_subscr_id`,  `task_starttime`, `task_submitter`, `task_created` )";
            $sql .= " VALUES ({$letter_id}, {$subscriber_id}, {$task_starttime}, {$submitter}, " . time() . ")";
            if (!$xoopsDB->queryF($sql)) {
                $protocolObj = $xnewsletter->getHandler('xNewsletter_protocol')->create();
                $protocolObj->setVar('protocol_letter_id', $letter_id);
                $protocolObj->setVar('protocol_subscriber_id', $subscriber_id);
                $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_TASK_ERROR_CREATE);
                $protocolObj->setVar('protocol_success', 0);
                $protocolObj->setVar('protocol_submitter', $submitter);
                $protocolObj->setVar('protocol_created', time());
                if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($protocolObj)) {
                    // create protocol is ok
                } else {
                    echo $protocolObj->getHtmlErrors();
                }
                unset($protocolObj);
            }
        }
    }
return true;
}

function xNewsletter_executeTasks($xn_send_in_packages, $letter_id = 0) {
    require_once XOOPS_ROOT_PATH . "/modules/xNewsletter/include/functions.php";
    require_once XOOPS_ROOT_PATH . "/class/mail/phpmailer/class.phpmailer.php";
    require_once XOOPS_ROOT_PATH . "/class/mail/phpmailer/class.pop3.php";
    require_once XOOPS_ROOT_PATH . "/class/mail/phpmailer/class.smtp.php";

    global $XoopsTpl, $xoopsDB, $xoopsUser;
    $xnewsletter = xNewsletterxNewsletter::getInstance();

    if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
        include_once(XOOPS_ROOT_PATH . "/class/template.php");
        $xoopsTpl = new XoopsTpl();
    }
    //get template path
    $template_path = XNEWSLETTER_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
    if (!is_dir($template_path)) $template_path = XNEWSLETTER_ROOT_PATH . '/language/english/templates/';
    if (!is_dir($template_path)) {
        return str_replace("%p", $template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH);
    }

    //get letters ready to send groups by letter_id
    $sql = "SELECT `task_letter_id` FROM {$xoopsDB->prefix('mod_xnewsletter_task')}";
    if ($letter_id > 0) {
        $sql .= " WHERE (`task_letter_id`={$letter_id})";
    }
    $sql .= " GROUP BY `task_letter_id`";
    if (!$task_letters = $xoopsDB->query($sql)) {
        return _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID;
    }

    while ($task_letter = $xoopsDB->fetchArray($task_letters)) {
        $letter_id = $task_letter["task_letter_id"];
        $letterObj = $xnewsletter->getHandler('xNewsletter_letter')->get($letter_id);
        if (count($letterObj) == 0) {
            return _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID;
        }

        // read categories
        $letter_cats = $letterObj->getVar('letter_cats');
        if ($letter_cats == '') {
            //no cats
            return _MA_XNEWSLETTER_LETTER_NONEAVAIL;
        }

        // read data of account
        $letter_account = $letterObj->getVar('letter_account');
        if ($letter_account == '' && $letter_account == 0) {
            return _MA_XNEWSLETTER_ACCOUNTS_NONEAVAIL;
        }
        $accountObj = $xnewsletter->getHandler('xNewsletter_accounts')->get($letter_account);
        $account_type = $accountObj->getVar('accounts_type');
        $account_yourname = $accountObj->getVar('accounts_yourname');
        $account_yourmail = $accountObj->getVar('accounts_yourmail');
        $account_username = $accountObj->getVar('accounts_username');
        $account_password = $accountObj->getVar('accounts_password');
        $account_server_out = $accountObj->getVar('accounts_server_out');
        $account_port_out = $accountObj->getVar('accounts_port_out');
        $account_securetype_out = $accountObj->getVar('accounts_securetype_out');

        // create basic mail body
        $letter_title 	= $letterObj->getVar('letter_title');
        $letter_content = $letterObj->getVar('letter_content', 'n');

        $tpl = new XoopsTpl();
        $tpl->assign('content', $letter_content);
        $tpl->assign('title', $letter_title); // new from v1.3

        // read template
        $letter_template = $letterObj->getVar('letter_template');

        // get emails of subscribers
        $recipients = array();
        $sql_tasklist = "SELECT `task_id`, `task_subscr_id` FROM {$xoopsDB->prefix('mod_xnewsletter_task')}";
        $sql_tasklist .= " WHERE ((`task_letter_id`= {$letter_id}) AND (`task_starttime` < " . time() . "))";
        if (!$task_letters = $xoopsDB->query($sql_tasklist)) {
            return $task_letters->getErrors();
        }
        $recipients = array();
        while ($task_letter = $xoopsDB->fetchArray($task_letters)) {
            $subscr_id = $task_letter['task_subscr_id'];
            $task_id = $task_letter['task_id'];
            if ($subscr_id == 0) {
                $recipients[] = array(
                    'task_id'           => $task_id,
                    'address'           => $letterObj->getVar('letter_email_test'),
                    'firstname'         => 'John',
                    'lastname'          => 'Doe',
                    'subscr_sex'        => 'Mr.',
                    'subscriber_id'     => '0',
                    'catsubscr_id'      => '0',
                    'subscriber_actkey' => 'Test'
                    );
            } else {
                $sql_subscr = "SELECT * FROM {$xoopsDB->prefix('mod_xnewsletter_subscr')}";
                $sql_subscr .= " WHERE `subscr_id`= {$subscr_id}";
                if (!$task_subscrs = $xoopsDB->query($sql_subscr)) {
                    return $task_subscrs->getErrors();
                }

                $subscriber = $xoopsDB->fetchArray($task_subscrs);
                $recipients[] = array(
                    'task_id'           => $task_id,
                    'address'           => $subscriber['subscr_email'],
                    'firstname'         => $subscriber['subscr_firstname'],
                    'lastname'          => $subscriber['subscr_lastname'],
                    'subscr_sex'        => $subscriber['subscr_sex'],
                    'subscriber_id'     => $subscriber['subscr_id'],
                    'subscriber_actkey' => $subscriber['subscr_actkey']
                );
            }
            if ($xn_send_in_packages > 0 && count($recipients) == $xn_send_in_packages)
                break;
        }

        if (count($recipients) == 0) {
            return null;
        }

        //read attachments
        $attachment_criteria = new CriteriaCompo();
        $attachment_criteria->add(new Criteria('attachment_letter_id', $letter_id));
        $attachment_criteria->setSort('attachment_id');
        $attachment_criteria->setOrder('ASC');
        $attachmentCount = $xnewsletter->getHandler('xNewsletter_attachment')->getCount($attachment_criteria);
        if ($attachmentCount > 0) {
            $attachmentObjs = $xnewsletter->getHandler('xNewsletter_attachment')->getall($attachment_criteria);
            foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
                $uploaddir = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path');
                if (substr($uploaddir, -1) != "/") {
                    //check, whether path seperator is existing
                    $uploaddir .= "/";
                }
                $uploaddir .= $letter_id . "/";
                $attachments[] = $uploaddir . $attachmentObj->getVar("attachment_name");
            }
        } else {
            $attachments = array();
        }

        $senderuid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid(): 0;
        $count_total = 0;
        $count_err = 0;

        try {
            if ($account_type == _AM_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL) {
                $pop = new POP3();
                $pop->Authorise($account_server_out, $account_port_out, 30, $account_username, $account_password, 1);
            }

            $mail = new PHPMailer();

            $mail->CharSet = _CHARSET; //use xoops default character set

            if ($account_type == _AM_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL) {
                //$mail->IsSendmail();	Fix Error
            }

            $mail->Username = $account_username; // SMTP account username
            $mail->Password = $account_password; // SMTP account password

            if ($account_type == _AM_ACCOUNTS_TYPE_VAL_POP3) {
                $mail->IsSMTP();
                //$mail->SMTPDebug = 2;
                $mail->Host = $account_server_out;
            }

            if ($account_type == _AM_ACCOUNTS_TYPE_VAL_SMTP || $account_type == _AM_ACCOUNTS_TYPE_VAL_GMAIL) {
                $mail->Port = $account_port_out; // set the SMTP port
                $mail->Host = $account_server_out; //sometimes necessary to repeat
            }

            if ($account_securetype_out != '') {
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $account_securetype_out; // sets the prefix to the server
            }

            $mail->SetFrom($account_yourmail, $account_yourname);
            $mail->AddReplyTo($account_yourmail, $account_yourname);
            $mail->Subject = html_entity_decode($letter_title, ENT_QUOTES);

            foreach ($recipients as $recipient) {
                $subscriber_id = $recipient['subscriber_id'];

                $tpl->assign('sex', $recipient['subscr_sex']);
                $tpl->assign('firstname', $recipient['firstname']);
                $tpl->assign('lastname', $recipient['lastname']);
                $tpl->assign('subscr_email', $recipient['address']);
                $activationKey = base64_encode(XOOPS_URL . "||{$subscriber_id}||{$recipient['subscriber_actkey']}||{$recipient['address']}");
                $tpl->assign('unsubscribe_link', XOOPS_URL . "/modules/xNewsletter/subscription.php?op=unsub&email={$recipient['address']}&actkey={$activationKey}");

                $htmlBody = $tpl->fetch($template_path . $letter_template . '.tpl');
                //$textBody = _AM_XNEWSLETTER_LETTER_EMAIL_ALTBODY;
                //$textBody = strip_tags($htmlBody . "\n" . $link);
                $textBody = xNewsletter_html2text($htmlBody); // new from v1.3

                $mail->AddAddress($recipient['address'], $recipient['firstname'] . " " . $recipient['lastname']);
                $mail->MsgHTML($htmlBody); // $mail->Body = $htmlBody;
                $mail->AltBody = $textBody;

                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $mail->AddAttachment($attachment);
                        echo "<br>att exist:" . $attachment;
                    } else {
                        echo "<br>att not exist:" . $attachment;
                    }
                }
                $count_total ++;
                if ( $mail->Send()) {
                    if ($subscriber_id == 0) {
                        $protocol_status = _AM_XNEWSLETTER_SEND_SUCCESS_TEST . " (" . $recipient["address"] . ")";
                    } else {
                        $protocol_status = _AM_XNEWSLETTER_SEND_SUCCESS;
                    }
                    $protocol_success = "1";
                } else {
                    $protocol_status = _AM_XNEWSLETTER_FAILED . "-> " . $mail->ErrorInfo;
                    $protocol_success = "0";
                    $count_err ++;
                }
                //create item in protocol for this email
                $text_clean = array("<strong>", "</strong>", "<br/>", "<br />");
                $protocol_status = str_replace($text_clean, "", $protocol_status);

                $mail->ClearAddresses();

                //delete item in table task
                $sql_delete = "DELETE FROM {$xoopsDB->prefix('mod_xnewsletter_task')}";
                $sql_delete .= " WHERE `task_id`= {$recipient["task_id"]}";
                $result = $xoopsDB->queryF($sql_delete);

                $protocolObj = $xnewsletter->getHandler('xNewsletter_protocol')->create();
                $protocolObj->setVar('protocol_letter_id', $letter_id);
                $protocolObj->setVar('protocol_subscriber_id', $subscriber_id);
                $protocolObj->setVar('protocol_status', $protocol_status);
                $protocolObj->setVar('protocol_success', $protocol_success);
                $protocolObj->setVar('protocol_submitter', $senderuid);
                $protocolObj->setVar('protocol_created', time());
                if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($protocolObj)) {
                    // create protocol is ok
                } else {
                    echo $protocolObj->getHtmlErrors();
                }
                unset($protocolObj);
            }

            unset($mail);

        } catch (phpmailerException $e) {
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER . $e->errorMessage(); //error messages from PHPMailer
            $count_err ++;
            $protocol_success = "0";
        } catch (Exception $e) {
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER . $e->getMessage(); //error messages from anything else!
            $count_err ++;
            $protocol_success = "0";
        }
    }

    //create final protocol item
    if ($count_err > 0) {
        $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_NUMBER;
        $protocol_status = str_replace("%e", $count_err, $protocol_status);
        $protocol_status = str_replace("%t", $count_total, $protocol_status);
        $protocol_success = 0;
    } else {
        $protocol_success = 1;
        if ($count_total > 0) {
            $protocol_status = _AM_XNEWSLETTER_SEND_SUCCESS_NUMBER;
            $protocol_status = str_replace("%t", $count_total, $protocol_status);
        } else {
            $protocol_status = '';
        }
    }
    $protocolObj = $xnewsletter->getHandler('xNewsletter_protocol')->create();
    $protocolObj->setVar('protocol_letter_id', $letter_id);
    $protocolObj->setVar('protocol_subscriber_id', 0);
    $protocolObj->setVar('protocol_status', $protocol_status);
    $protocolObj->setVar('protocol_success', $protocol_success);
    $protocolObj->setVar('protocol_submitter', $senderuid);
    $protocolObj->setVar('protocol_created', time());
    if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($protocolObj)) {
        // create protocol is ok
    } else {
        echo $protocolObj->getHtmlErrors();
    }
    unset($protocolObj);

    return $protocol_status;
}
