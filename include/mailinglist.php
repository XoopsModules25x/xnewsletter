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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : 1 Thu 2012/12/06 12:57:01 :  Exp $
 * ****************************************************************************
 */

use XoopsModules\Xnewsletter;

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once __DIR__ . '/common.php';

/**
 * @param int $type
 * @param int $subscr_id
 * @param $mailinglist_id
 *
 * @return bool|null
 */
function subscribingMLHandler($type, $subscr_id, $mailinglist_id)
{
    global $xoopsUser, $xoopsConfig;
    $helper = XoopsModules\Xnewsletter\Helper::getInstance();

    // get subscriber
    $subscrObj    = $helper->getHandler('Subscr')->get($subscr_id);
    $subscr_email = $subscrObj->getVar('subscr_email');

    // get mailinglist
    $mailinglistObj          = $helper->getHandler('Mailinglist')->get($mailinglist_id);
    $mailinglist_listname    = $mailinglistObj->getVar('mailinglist_listname');
    $mailinglist_email       = $mailinglistObj->getVar('mailinglist_email');
    $mailinglist_subscribe   = $mailinglistObj->getVar('mailinglist_subscribe');
    $mailinglist_unsubscribe = $mailinglistObj->getVar('mailinglist_unsubscribe');
    $mailinglist_system      = (int)$mailinglistObj->getVar('mailinglist_system');
    $mailinglist_target      = $mailinglistObj->getVar('mailinglist_target');
    $mailinglist_pwd         = $mailinglistObj->getVar('mailinglist_pwd');
    $mailinglist_notifyowner = $mailinglistObj->getVar('mailinglist_notifyowner');

    $senderUid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;

    if ($mailinglist_system === _XNEWSLETTER_MAILINGLIST_TYPE_DEFAULT_VAL) {
        // this should not happen
        $protocolObj = $helper->getHandler('Protocol')->create();
        $protocolObj->setVar('protocol_letter_id', 0);
        $protocolObj->setVar('protocol_subscriber_id', $subscr_id);
        $protocolObj->setVar('protocol_status', 'Error adding mailing list: invalid mailing list type');
        $protocolObj->setVar('protocol_success', false);
        $protocolObj->setVar('protocol_submitter', $senderUid);
        $protocolObj->setVar('protocol_created', time());

        if ($helper->getHandler('Protocol')->insert($protocolObj)) {
            return true;
        }
        return $protocolObj->getHtmlErrors();
    }
    if ($mailinglist_system === _XNEWSLETTER_MAILINGLIST_TYPE_MAJORDOMO_VAL) {

        if (_XNEWSLETTER_MAILINGLIST_SUBSCRIBE == $type) {
            $action_code = $mailinglist_subscribe;
        } else {
            $action_code = $mailinglist_unsubscribe;
        }
        $action_code = str_replace('{email}', $subscr_email, $action_code);
        $action_code = str_replace('{nameofmylist}', $mailinglist_listname, $action_code);

        require_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.phpmailer.php';
        require_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.pop3.php';
        require_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.smtp.php';

        try {
            $xoopsMailer = xoops_getMailer();
            $xoopsMailer->reset();
            //$xoopsMailer->setTemplateDir();
            $xoopsMailer->useMail();
            $xoopsMailer->setHTML(false);
            //$xoopsMailer->setTemplate('activate.tpl');
            $xoopsMailer->setToEmails($mailinglist_email);
            if (isset($xoopsConfig['adminmail'])) {
                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            }
            if (isset($xoopsConfig['sitename'])) {
                $xoopsMailer->setFromName($xoopsConfig['sitename']);
            }
            //$xoopsMailer->setSubject($subject);
            $xoopsMailer->setBody($action_code);
            $xoopsMailer->send();

            $protocol_status = str_replace('%a', $action_code, _AM_XNEWSLETTER_SEND_SUCCESS_ML_DETAIL);
            $xoopsMailer->reset();
            $protocol_success = true;
        } catch (\Exception $e) {
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER . $xoopsMailer->getErrors(); //error messages
            $protocol_success = false;
            $helper->addLog($e);
        }

        //create item in protocol for this email
        $text_clean = ['<strong>', '</strong>', '<br/>', '<br>'];
        $protocol_status = str_replace($text_clean, '', $protocol_status);

        $protocolObj = $helper->getHandler('Protocol')->create();
        $protocolObj->setVar('protocol_letter_id', 0);
        $protocolObj->setVar('protocol_subscriber_id', $subscr_id);
        $protocolObj->setVar('protocol_status', $protocol_status);
        $protocolObj->setVar('protocol_success', $protocol_success);
        $protocolObj->setVar('protocol_submitter', $senderUid);
        $protocolObj->setVar('protocol_created', time());

        if ($helper->getHandler('Protocol')->insert($protocolObj)) {
            return true;
        }
        return $protocolObj->getHtmlErrors();
    }

    if ($mailinglist_system === _XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL) {

        $action_code = $mailinglist_target;
        if (substr($action_code, -1) !== '/') {
            $action_code .= '/';
        }
        $action_code .= 'mailman/admin/';
        $action_code .= $mailinglist_listname;
        $action_code .= '/members/';

        if (_XNEWSLETTER_MAILINGLIST_SUBSCRIBE == $type) {
            $action_code .= 'add?subscribe_or_invite=0&send_welcome_msg_to_this_batch=0&notification_to_list_owner=' . $mailinglist_notifyowner;
            $action_code .= '&subscribees_upload=';
        } else {
            $action_code .= 'remove?send_unsub_ack_to_this_batch=0&send_unsub_notifications_to_list_owner=' . $mailinglist_notifyowner;
            $action_code .= '&unsubscribees_upload=';
        }
        $action_code .= $subscr_email;
        $action_code .= '&adminpw=' .  $mailinglist_pwd;
        //echo "action_code:".$action_code;

        try {
            $req = curl_init();
            curl_setopt($req, CURLOPT_URL, $action_code);
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);

            $res_curl = curl_exec($req);

            if(!curl_errno($req)){
                $status = (int)curl_getinfo($req, CURLINFO_HTTP_CODE);
                if ($status === 200) {
                    $protocol_status = str_replace('%a', $subscr_email, _AM_XNEWSLETTER_SEND_SUCCESS_ML_DETAIL);
                    $protocol_status = str_replace('%m', $mailinglist_listname, $protocol_status);
                    $protocol_success = true;
                } else {
                    $protocol_success = false;
                    $protocol_status = 'Curl error: ' . curl_error($req);
                }
            } else {
                $protocol_success = false;
                $protocol_status = 'Curl error: ' . curl_error($req);
            }
        } catch (\Exception $e) {
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER; //error messages
            $protocol_success = false;
            $helper->addLog($e);
        }
        curl_close($req);
        //create item in protocol for this email
        $text_clean = ['<strong>', '</strong>', '<br/>', '<br>'];
        $protocol_status = str_replace($text_clean, '', $protocol_status);

        $protocolObj = $helper->getHandler('Protocol')->create();
        $protocolObj->setVar('protocol_letter_id', 0);
        $protocolObj->setVar('protocol_subscriber_id', $subscr_id);
        $protocolObj->setVar('protocol_status', $protocol_status);
        $protocolObj->setVar('protocol_success', $protocol_success);
        $protocolObj->setVar('protocol_submitter', $senderUid);
        $protocolObj->setVar('protocol_created', time());

        if ($helper->getHandler('Protocol')->insert($protocolObj)) {
            return true;
        }
        return $protocolObj->getHtmlErrors();
    }
    return null;
}

function getActioncode ($mailinglist_id = 0) {
    $helper = XoopsModules\Xnewsletter\Helper::getInstance();

    // get mailinglist
    $mailinglistObj          = $helper->getHandler('Mailinglist')->get($mailinglist_id);
    $mailinglist_listname    = $mailinglistObj->getVar('mailinglist_listname');
    $mailinglist_system      = (int)$mailinglistObj->getVar('mailinglist_system');
    $mailinglist_target      = $mailinglistObj->getVar('mailinglist_target');
    $mailinglist_pwd         = $mailinglistObj->getVar('mailinglist_pwd');

    if ($mailinglist_system === _XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL) {

        $action_code = $mailinglist_target;
        if (substr($action_code, -1) !== '/') {
            $action_code .= '/';
        }
        $action_code .= 'mailman/admin/';
        $action_code .= $mailinglist_listname;
        $action_code .= '/members/list?adminpw=' .  $mailinglist_pwd;

        return $action_code;
    }
    return null;
}
