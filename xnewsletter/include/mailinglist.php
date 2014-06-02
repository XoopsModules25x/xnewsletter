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
 *  @license    GNU General Public License 2.0
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : 1 Thu 2012/12/06 12:57:01 :  Exp $
 * ****************************************************************************
 */
defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
include_once dirname(__FILE__) . '/common.php';

function subscribingMLHandler($type, $subscr_id, $mailinglist_id) {
    global $xoopsUser, $xoopsConfig;
    $xnewsletter = xNewsletterxNewsletter::getInstance();

    $subscrObj = $xnewsletter->getHandler('xNewsletter_subscr')->get($subscr_id);
    $subscr_email = $subscrObj->getVar("subscr_email");

    $mailinglistObj = $xnewsletter->getHandler('xNewsletter_mailinglist')->get($mailinglist_id);
    $mailinglist_email = $mailinglistObj->getVar("mailinglist_email");
    if ($type == 1) {
        $action_code = $mailinglistObj->getVar("mailinglist_subscribe");
    } else {
        $action_code = $mailinglistObj->getVar("mailinglist_unsubscribe");
    }
    $action_code = str_replace("{email}", $subscr_email, $action_code);

    echo "<br />type {$type}";
    echo "<br />subscr_id: {$subscr_id}";
    echo "<br />mailinglist_id: {$mailinglist_id}";
    echo "<br />action_code: {$action_code}";
    echo "<br />";

    require_once(XOOPS_ROOT_PATH . "/class/mail/phpmailer/class.phpmailer.php");
    require_once(XOOPS_ROOT_PATH . "/class/mail/phpmailer/class.pop3.php");
    require_once(XOOPS_ROOT_PATH . "/class/mail/phpmailer/class.smtp.php");

    //get emails of subscribers
    $recipients = array();
    $recipients[] = array(
        "address"=> $mailinglist_email,
        "firstname" => "",
        "lastname" => "",
        "subscr_sex" => "",
        "subscriber_id" => "0",
        "catsubscr_id" => "0"
        );

    $letter_id = 0;
    $senderUid = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;

    $subject = "";

    foreach ($recipients as $recipient) {
        $subscriber_id = $recipient["subscriber_id"];
        try {
            $xoopsMailer =& xoops_getMailer();
            $xoopsMailer->reset();
            //$xoopsMailer->setTemplateDir();
            $xoopsMailer->useMail();
            $xoopsMailer->setHTML(false);
            //$xoopsMailer->setTemplate('activate.tpl');
            $xoopsMailer->setToEmails($recipient["address"]);
            if (isset($xoopsConfig['adminmail'])) $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            if (isset($xoopsConfig['sitename'])) $xoopsMailer->setFromName($xoopsConfig['sitename']);
            //$xoopsMailer->setSubject($subject);
            $xoopsMailer->setBody($action_code);
            $xoopsMailer->send();

            $protocol_status = str_replace("%a", $action_code, _AM_XNEWSLETTER_SEND_SUCCESS_ML_DETAIL);
            $xoopsMailer->reset();
            $protocol_success = 1;
        } catch (Exception $e) {
            $protocol_status = _AM_XNEWSLETTER_SEND_ERROR_PHPMAILER . $xoopsMailer->getErrors(); //error messages
            $protocol_success = 0;
        }
        //create item in protocol for this email
        $text_clean = array("<strong>", "</strong>", "<br/>", "<br />");
        $protocol_status = str_replace($text_clean, "", $protocol_status);

        $protocolObj = $xnewsletter->getHandler('xNewsletter_protocol')->create();
        $protocolObj->setVar("protocol_letter_id", $letter_id);
        $protocolObj->setVar("protocol_subscriber_id", $subscriber_id);
        $protocolObj->setVar("protocol_status", $protocol_status);
        $protocolObj->setVar("protocol_success", 1);
        $protocolObj->setVar("protocol_submitter", $senderUid);
        $protocolObj->setVar("protocol_created", time());

        if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($protocolObj)) {
            //create protocol is ok
            $protocolObj2 = $xnewsletter->getHandler('xNewsletter_protocol')->create();
            $protocolObj2->setVar("protocol_letter_id", $letter_id);
            $protocolObj2->setVar("protocol_subscriber_id", $subscriber_id);
            $protocolObj2->setVar("protocol_status", _AM_XNEWSLETTER_SEND_SUCCESS_ML);
            $protocolObj2->setVar("protocol_success", 1);
            $protocolObj2->setVar("protocol_submitter", $senderUid);
            $protocolObj2->setVar("protocol_created", time());
            if ($xnewsletter->getHandler('xNewsletter_protocol')->insert($protocolObj2)) {
                return true;
            } else {
                return $protocolObj2->getHtmlErrors();
            }
        } else {
            return $protocolObj->getHtmlErrors();
        }
    }
}
