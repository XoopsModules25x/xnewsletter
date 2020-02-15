<?php

/* This is a sample callback function for PHPMailer-BMH (Bounce Mail Handler).
 * This callback function will echo the results of the BMH processing.
 */

/* Callback (action) function
 * @param int     $msgnum        the message number returned by Bounce Mail Handler
 * @param string  $bounce_type   the bounce type: 'antispam','autoreply','concurrent','content_reject','command_reject','internal_error','defer','delayed'        => array('remove'=>0,'bounce_type'=>'temporary'),'dns_loop','dns_unknown','full','inactive','latin_only','other','oversize','outofoffice','unknown','unrecognized','user_reject','warning'
 * @param string  $email         the target email address
 * @param string  $subject       the subject, ignore now
 * @param string  $xheader       the XBounceHeader from the mail
 * @param boolean $remove        remove status, 1 means removed, 0 means not removed
 * @param string  $rule_no       Bounce Mail Handler detect rule no.
 * @param string  $rule_cat      Bounce Mail Handler detect rule category.
 * @param int     $totalFetched  total number of messages in the mailbox
 * @return boolean
 */

use XoopsModules\Xnewsletter;

require_once __DIR__ . '/admin_header.php';

/**
 * @param      $msgnum
 * @param      $bounce_type
 * @param      $email
 * @param      $subject
 * @param      $xheader
 * @param      $remove
 * @param bool $rule_no
 * @param bool $rule_cat
 * @param int  $totalFetched
 *
 * @return bool
 */
function callbackAction(
    $msgnum,
    $bounce_type,
    $email,
    $subject,
    $xheader,
    $remove,
    $rule_no = false,
    $rule_cat = false,
    $totalFetched = 0)
{
    global $xoopsUser;
    $helper = Xnewsletter\Helper::getInstance();

    if ('0000' != $rule_no) {
        $bmhObj = $helper->getHandler('Bmh')->create();
        $bmhObj->setVar('bmh_rule_no', $rule_no);
        $bmhObj->setVar('bmh_rule_cat', $rule_cat);
        $bmhObj->setVar('bmh_bouncetype', $bounce_type);
        //$verif_bmh_remove = ($remove === true || $remove == '1') ? '1' : '0';
        $bmhObj->setVar('bmh_remove', $remove);
        $bmhObj->setVar('bmh_email', $email);
        $bmhObj->setVar('bmh_subject', $subject);
        $bmhObj->setVar('bmh_measure', '0');
        $bmhObj->setVar('bmh_submitter', $xoopsUser->uid());
        $bmhObj->setVar('bmh_created', time());

        if (!$helper->getHandler('Bmh')->insert($bmhObj)) {
            $GLOBALS['xoopsTpl']->assign('error', $bmhObj->getHtmlErrors());
            return false;
        }
        //echo $msgnum . ': '  . $rule_no . ' | '  . $rule_cat . ' | '  . $bounce_type . ' | '  . $remove . ' | ' . $email . ' | '  . $subject . ' | '  . $xheader . "<br>\n";
    }

    return true;
}
