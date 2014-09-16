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
 *  Version : $Id $
 * ****************************************************************************
 */

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__FILE__) . '/common.php';

/**
 * @param $op
 * @param $letter_id
 * @param $xn_send_in_packages
 * @param $xn_send_in_packages_time
 *
 * @return bool
 */
function xnewsletter_createTasks($op, $letter_id, $xn_send_in_packages, $xn_send_in_packages_time) {
    global $xoopsUser, $xoopsDB;
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    $uid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid(): 0;

    // check data before creating task list
    if ($letter_id == 0) {
        redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID);
    }
    $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
    if (count($letterObj) == 0) {
        redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID);
    }

    // read categories
    $letter_cats = $letterObj->getVar('letter_cats');
    if ($letter_cats == '') {
        // no cats
        redirect_header('letter.php', 3, _MA_XNEWSLETTER_LETTER_NONEAVAIL);
    }

    if ($op == 'send_test') {
        //check for valid email for testing
        $letter_email_test = $letterObj->getVar('letter_email_test');
        if ($letter_email_test == '')
            redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_EMAIL);
    }

    // get emails of subscribers
    $recipients = array();
    if ($op == 'send_test') {
        $recipients[] = 0;
    } else {
        // read all subscribers
        $sql = "SELECT subscr_id, subscr_actkey ";
        $sql .= " FROM {$xoopsDB->prefix('xnewsletter_subscr')} INNER JOIN {$xoopsDB->prefix('xnewsletter_catsubscr')} ON subscr_id = catsubscr_subscrid ";
        $sql .= " WHERE subscr_activated=1 AND (((catsubscr_catid) IN (";
        $sql .= str_replace('|', ',', $letter_cats);
        $sql .= "))) GROUP BY subscr_id;";

        if (!$subscrs = $xoopsDB->query($sql)) die();

        while ($subscr = $xoopsDB->fetchArray($subscrs)) {
            $subscr_id = $subscr['subscr_id'];
            if ($op == 'resend_letter') {
                // read subscribers, where send failed
                $protocolCriteria = new CriteriaCompo();
                $protocolCriteria->add(new Criteria('protocol_letter_id', $letter_id));
                $protocolCriteria->add(new Criteria('protocol_subscriber_id', $subscr_id));
                $protocolCriteria->add(new Criteria('protocol_success', true));
                $protocolsCriteria = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);
                if ($protocolsCriteria > 0) $subscr_id = 0; // letter already successfully sent
            }
            if ($subscr_id > 0) {
                if ($subscr['subscr_actkey'] == '') {
                    $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
                    $subscr['subscr_actkey'] = xoops_makepass();
                    $subscrObj->setVar('subscr_actkey', $subscr['subscr_actkey']);
                    $xnewsletter->getHandler('subscr')->insert($subscrObj);
                    unset($subscrObj);
                }
                $recipients[] = $subscr['subscr_id'];
            }
        }
    }

    if (count($recipients) == 0) {
        redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_SUBSCR);
    } else {
        // creating task list
        $counter = 0;
        $task_starttime = time() - 1;
        foreach ($recipients as $subscr_id) {
            // calculate start time, if letter should be sent in packages
            if ($xn_send_in_packages > 0) {
                if ($counter == $xn_send_in_packages) {
                    $task_starttime = $task_starttime + 60 * $xn_send_in_packages_time;
                    $counter = 0;
                }
            }
            ++$counter;
            // create task list item
            $sql = "INSERT INTO `{$xoopsDB->prefix('xnewsletter_task')}`";
            $sql .= " (`task_letter_id`, `task_subscr_id`,  `task_starttime`, `task_submitter`, `task_created` )";
            $sql .= " VALUES ({$letter_id}, {$subscr_id}, {$task_starttime}, {$uid}, " . time() . ")";
            if (!$xoopsDB->queryF($sql)) {
                $protocolObj = $xnewsletter->getHandler('protocol')->create();
                $protocolObj->setVar('protocol_letter_id', $letter_id);
                $protocolObj->setVar('protocol_subscriber_id', $subscr_id);
                $protocolObj->setVar('protocol_status', _AM_XNEWSLETTER_TASK_ERROR_CREATE);
                    $protocolObj->setVar('protocol_status_str_id', _XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK);
                    $protocolObj->setVar('protocol_status_vars', array());
                $protocolObj->setVar('protocol_success', false);
                $protocolObj->setVar('protocol_submitter', $uid);
                $protocolObj->setVar('protocol_created', time());
                if (!$xnewsletter->getHandler('protocol')->insert($protocolObj)) {
                    echo $protocolObj->getHtmlErrors();
                }
                unset($protocolObj);
                return false;
            } elseif ($op != 'send_test') {
                // update letter
                $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
                $letterObj->setVar('letter_sender', $uid);
                $letterObj->setVar('letter_sent', time());
                $xnewsletter->getHandler('letter')->insert($letterObj);
            }
        }
    }
    return true;
}

/**
 * @param     $xn_send_in_packages
 * @param int $letter_id
 *
 * @return mixed|string
 */
function xnewsletter_executeTasks($xn_send_in_packages, $letter_id = 0) {
    require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/functions.php';
    require_once XNEWSLETTER_ROOT_PATH . '/class/class.xnewslettermailer.php';


    global $XoopsTpl, $xoopsDB, $xoopsUser;
    $xnewsletter = xnewsletterxnewsletter::getInstance();

    if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
        include_once(XOOPS_ROOT_PATH . '/class/template.php');
        $xoopsTpl = new XoopsTpl();
    }
    // get template path
    $template_path = XNEWSLETTER_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
    if (!is_dir($template_path)) $template_path = XNEWSLETTER_ROOT_PATH . '/language/english/templates/';
    if (!is_dir($template_path)) {
        return str_replace("%p", $template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH);
    }

    //get letters ready to send groups by letter_id
    $sql = "SELECT `task_letter_id` FROM {$xoopsDB->prefix('xnewsletter_task')}";
    if ($letter_id > 0) {
        $sql .= " WHERE (`task_letter_id`={$letter_id})";
    }
    $sql .= " GROUP BY `task_letter_id`";
    if (!$task_letters = $xoopsDB->query($sql)) {
        return _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID;
    }

    while ($task_letter = $xoopsDB->fetchArray($task_letters)) {
        $letter_id = $task_letter['task_letter_id'];
        $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
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
        $accountObj = $xnewsletter->getHandler('accounts')->get($letter_account);
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

        $letterTpl = new XoopsTpl();
        // letter data
        $letterTpl->assign('content', $letter_content);
        $letterTpl->assign('title', $letter_title); // new from v1.3
        // letter attachments as link
        $attachmentAslinkCriteria = new CriteriaCompo();
        $attachmentAslinkCriteria->add(new Criteria('attachment_letter_id', $letter_id));
        $attachmentAslinkCriteria->add(new Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASLINK));
        $attachmentAslinkCriteria->setSort('attachment_id');
        $attachmentAslinkCriteria->setOrder('ASC');
        $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentAslinkCriteria, true);
        foreach($attachmentObjs as $attachment_id => $attachmentObj) {
            $attachment_array = $attachmentObj->toArray();
            $attachment_array['attachment_url'] = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
            $attachment_array['attachment_link'] = XNEWSLETTER_URL . "/attachment.php?attachment_id={$attachment_id}";
            $letterTpl->append('attachments', $attachment_array);
        }
        // extra data
        $letterTpl->assign('date', time()); // new from v1.3
        $letterTpl->assign('xoops_url', XOOPS_URL); // new from v1.3
        $letterTpl->assign('xoops_langcode', _LANGCODE); // new from v1.3
        $letterTpl->assign('xoops_charset', _CHARSET); // new from v1.3

        // get emails of subscribers
        $recipients = array();
        $sql_tasklist = "SELECT `task_id`, `task_subscr_id` FROM {$xoopsDB->prefix('xnewsletter_task')}";
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
                    'firstname'         => _AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW,
                    'lastname'          => _AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW,
                    'subscr_sex'        => _AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW,
                    'subscriber_id'     => '0',
                    'catsubscr_id'      => '0',
                    'subscriber_actkey' => 'Test'
                    );
            } else {
                $sql_subscr = "SELECT * FROM {$xoopsDB->prefix('xnewsletter_subscr')}";
                $sql_subscr .= " WHERE `subscr_id`= {$subscr_id}";
                if (!$task_subscrs = $xoopsDB->query($sql_subscr)) {
                    return $task_subscrs->getErrors();
                }

                $subscr = $xoopsDB->fetchArray($task_subscrs);
                $recipients[] = array(
                    'task_id'           => $task_id,
                    'address'           => $subscr['subscr_email'],
                    'firstname'         => $subscr['subscr_firstname'],
                    'lastname'          => $subscr['subscr_lastname'],
                    'subscr_sex'        => $subscr['subscr_sex'],
                    'subscriber_id'     => $subscr['subscr_id'],
                    'subscriber_actkey' => $subscr['subscr_actkey']
                );
            }
            if ($xn_send_in_packages > 0 && count($recipients) == $xn_send_in_packages)
                break;
        }

        if (count($recipients) == 0) {
            return null;
        }

        // get letter attachments as attachment
        $attachmentAsattachmentCriteria = new CriteriaCompo();
        $attachmentAsattachmentCriteria->add(new Criteria('attachment_letter_id', $letter_id));
        $attachmentAsattachmentCriteria->add(new Criteria('attachment_mode', _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT));
        $attachmentAsattachmentCriteria->setSort('attachment_id');
        $attachmentAsattachmentCriteria->setOrder('ASC');
        $attachmentObjs = $xnewsletter->getHandler('attachment')->getObjects($attachmentAsattachmentCriteria, true);
        $attachmentsPath = array();
        foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
            $attachmentsPath[] = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . '/' . $attachmentObj->getVar('attachment_name');
        }

        $uid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid(): 0;
        $count_total = 0;
        $count_err = 0;

        try {
            if ($account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL) {
                $pop = new POP3();
                $pop->Authorise($account_server_out, $account_port_out, 30, $account_username, $account_password, 1);
            }

            //$mail = new PHPMailer();
            $mail = new XnewsletterMailer();

            $mail->CharSet = _CHARSET; //use xoops default character set

            if ($account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL) {
                //$mail->IsSendmail();	Fix Error
            }

            $mail->Username = $account_username; // SMTP account username
            $mail->Password = $account_password; // SMTP account password

            if ($account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3) {
                $mail->IsSMTP();
                //$mail->SMTPDebug = 2;
                $mail->Host = $account_server_out;
            }

            if ($account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP || $account_type == _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL) {
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
                $subscr_id = $recipient['subscriber_id'];
                // subscr data
                $letterTpl->assign('sex', $recipient['subscr_sex']);
                $letterTpl->assign('salutation', $recipient['subscr_sex']); // new from v1.3
                $letterTpl->assign('firstname', $recipient['firstname']);
                $letterTpl->assign('lastname', $recipient['lastname']);
                $letterTpl->assign('subscr_email', $recipient['address']);
                $letterTpl->assign('email', $recipient['address']); // new from v1.3
                // extra data
                $act = array(
                    XOOPS_URL,
                    $subscr_id,
                    $recipient['subscriber_actkey'],
                    $recipient['address']
                );
                $activationKey = base64_encode(implode('||', $act));
                $letterTpl->assign('unsubscribe_link', XOOPS_URL . "/modules/xnewsletter/subscription.php?op=unsub&email={$recipient['address']}&actkey={$activationKey}");
                $letterTpl->assign('unsubscribe_url', XOOPS_URL . "/modules/xnewsletter/subscription.php?op=unsub&email={$recipient['address']}&actkey={$activationKey}"); // new from v1.3

                preg_match('/db:([0-9]*)/', $letterObj->getVar('letter_template'), $matches);
                if(isset($matches[1]) && ($templateObj = $xnewsletter->getHandler('template')->get((int)$matches[1]))) {
                    // get template from database
                    $htmlBody = $letterTpl->fetchFromData($templateObj->getVar('template_content', 'n'));
                } else {
                    // get template from filesystem
                    $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
                    if (!is_dir($template_path)) $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
                    $template = $template_path . $letterObj->getVar('letter_template') . '.tpl';
                    $htmlBody = $letterTpl->fetch($template);
                }
                $textBody = xnewsletter_html2text($htmlBody); // new from v1.3
                //$textBody = mb_convert_encoding($textBody, 'ISO-8859-1', _CHARSET); // "text/plain; charset=us-ascii" [http://www.w3.org/Protocols/rfc1341/7_1_Text.html]

                $mail->AddAddress($recipient['address'], $recipient['firstname'] . ' ' . $recipient['lastname']);
                $mail->MsgHTML($htmlBody); // $mail->Body = $htmlBody;
                $mail->AltBody = $textBody;

                foreach ($attachmentsPath as $attachmentPath) {
                    if (file_exists($attachmentPath)) {
                        $mail->AddAttachment($attachmentPath);
                    }
                }
                ++$count_total;

                if ($mail->Send()) {
                    if ($subscr_id == 0) {
                        $protocol_status = _AM_XNEWSLETTER_SEND_SUCCESS_TEST . " (" . $recipient['address'] . ")"; // old style
                        $protocol_status_str_id = _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST; // new from v1.3
                        $protocol_status_vars = array('%recipient' => $recipient['address']); // new from v1.3
                    } else {
                        $protocol_status = _AM_XNEWSLETTER_SEND_SUCCESS; // old style
                        $protocol_status_str_id = _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND; // new from v1.3
                        $protocol_status_vars = array(); // new from v1.3
                    }
                    $protocol_success = true;
                } else {
                    $protocol_status = _AM_XNEWSLETTER_FAILED . "-> " . $mail->ErrorInfo; // old style
                    $protocol_status_str_id = _XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND; // new from v1.3
                    $protocol_status_vars = array('%error' => $mail->ErrorInfo); // new from v1.3

                    $protocol_success = false;
                    ++$count_err;
                }
                //create item in protocol for this email
                $text_clean = array('<strong>', '</strong>', '<br/>', '<br />');
                $protocol_status = str_replace($text_clean, '', $protocol_status);

                $mail->ClearAddresses();

                //delete item in table task
                $sql_delete = "DELETE FROM {$xoopsDB->prefix('xnewsletter_task')}";
                $sql_delete .= " WHERE `task_id`= {$recipient["task_id"]}";
                $result = $xoopsDB->queryF($sql_delete);

                $protocolObj = $xnewsletter->getHandler('protocol')->create();
                $protocolObj->setVar('protocol_letter_id', $letter_id);
                $protocolObj->setVar('protocol_subscriber_id', $subscr_id);
                $protocolObj->setVar('protocol_status', $protocol_status); // old style
                $protocolObj->setVar('protocol_status_str_id', $protocol_status_str_id); // new from v1.3
                $protocolObj->setVar('protocol_status_vars', $protocol_status_vars); // new from v1.3
                $protocolObj->setVar('protocol_success', $protocol_success);
                $protocolObj->setVar('protocol_submitter', $uid);
                $protocolObj->setVar('protocol_created', time());
                if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
                    // create protocol is ok
                } else {
                    echo $protocolObj->getHtmlErrors();
                }
                unset($protocolObj);
            }

            unset($mail);

        } catch (phpmailerException $e) {
// IN PROGRESS
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER . $e->errorMessage(); //error messages from PHPMailer
            ++$count_err;
            $protocol_success = false;
        } catch (Exception $e) {
// IN PROGRESS
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER . $e->getMessage(); //error messages from anything else!
            ++$count_err;
            $protocol_success = false;
        }
    }

    //create final protocol item
    if ($count_err > 0) {
// IN PROGRESS
        $protocol_status = xnewsletter_sprintf(_AM_XNEWSLETTER_SEND_ERROR_NUMBER, array('%e' => $count_err, '%t' => $count_total));
        $protocol_success = false;
    } else {
        $protocol_success = true;
        if ($count_total > 0) {
// IN PROGRESS
            $protocol_status = xnewsletter_sprintf(_AM_XNEWSLETTER_SEND_SUCCESS_NUMBER, array('%t' => $count_total));
        } else {
// IN PROGRESS
            $protocol_status = '';
        }
    }
    $protocolObj = $xnewsletter->getHandler('protocol')->create();
    $protocolObj->setVar('protocol_letter_id', $letter_id);
    $protocolObj->setVar('protocol_subscriber_id', 0);
    $protocolObj->setVar('protocol_status', $protocol_status);
// IN PROGRESS
    $protocolObj->setVar('protocol_success', $protocol_success);
    $protocolObj->setVar('protocol_submitter', $uid);
    $protocolObj->setVar('protocol_created', time());
    if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
        // create protocol is ok
    } else {
        echo $protocolObj->getHtmlErrors();
    }
    unset($protocolObj);

    return $protocol_status;
}
