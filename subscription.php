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
 *  Version : $Id: subscription.php 12559 2014-06-02 08:10:39Z beckmi $
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . "/header.php";

$op            = XoopsRequest::getString('op', 'list_subscriptions');
$activationKey = XoopsRequest::getString('actkey', '');
$subscr_id     = XoopsRequest::getInt('subscr_id', 0);
$subscr_email  = ($op != 'unsub') ? XoopsRequest::getString('subscr_email', '') : '';
$ip            =  xoops_getenv('REMOTE_ADDR');

if ($op == 'save_subscription' || $activationKey != '') {
    $xoopsOption['template_main'] = 'xnewsletter_subscription_result.tpl';
} else {
    $xoopsOption['template_main'] = 'xnewsletter_subscription.tpl';
}
if (isset($_REQUEST['addnew'])) {
    $op = 'addnew_subscription';
}
if ($activationKey && $op != 'delete_subscription_confirmed' && $op != 'unsub') {
    $op = 'save_subscription';
}
if ($op == 'delete_subscription_confirmed') {
    $op = 'delete_subscription';
}
if ($op == 'unsub') {
    $op = 'list_subscriptions';
    $xoopsOption['template_main'] = 'xnewsletter_subscription.tpl';
    $_SESSION['redirect_mail'] = XoopsRequest::getString('email', '');
    $_SESSION['unsub'] = '1';
}

//to avoid errors in debug when xn_groups_change_other
$subscr_sex = '';
$subscr_firstname = '';
$subscr_lastname = '';

$count_ok = 0;
$count_err = 0;
$actionProts_ok = []; // IN PROGRESS: remove from here
$actionProts_error = []; // IN PROGRESS: remove from here

switch ($op) {
    case "resend_subscription" :
        $actionProts_ok = [];
        $actionProts_error = [];
        $xoopsOption['template_main'] = 'xnewsletter_subscription_result.tpl';
        include_once XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new XnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // resend the email with the confirmation code
        $subscr_id = XoopsRequest::getInt('subscr_id', 0);
        $subscrCriteria = new CriteriaCompo();
        $subscrCriteria->add(new Criteria('subscr_id', $subscr_id));
        $subscrCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
        if ($subscrCount > 0) {
            $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
            $subscr_email = $subscrObj->getVar('subscr_email');
            $xoopsMailer = xoops_getMailer();
            $xoopsMailer->reset();
            $xoopsMailer->setTemplateDir();
            $xoopsMailer->useMail();
            $xoopsMailer->setTemplate('activate.tpl');
            $xoopsMailer->setToEmails($subscr_email);
            if (isset($xoopsConfig['adminmail'])) $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            if (isset($xoopsConfig['sitename'])) $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->assign('SEX', $subscrObj->getVar('subscr_sex'));
            $xoopsMailer->assign('EMAIL', $subscr_email);
            $xoopsMailer->assign('FIRSTNAME', $subscrObj->getVar('subscr_firstname'));
            $xoopsMailer->assign('LASTNAME', $subscrObj->getVar('subscr_lastname'));
            $xoopsMailer->assign('IP', $ip);
            $activationKey = base64_encode(XOOPS_URL . "||addnew||{$subscr_id}||{$subscrObj->getVar("subscr_actkey")}||{$subscr_email}");
            $xoopsMailer->assign('ACTLINK', XOOPS_URL . "/modules/xnewsletter/{$currentFile}?actkey={$activationKey}");
            $subject = _MA_XNEWSLETTER_SUBSCRIPTIONSUBJECT . $GLOBALS['xoopsConfig']['sitename'];
            $xoopsMailer->setSubject($subject);
            if (!$xoopsMailer->send()) {
                $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . "<br />" . $xoopsMailer->getErrors();
            }
            $actionProts_ok[] = str_replace ("%nl", $cat_name, _MA_XNEWSLETTER_SENDMAIL_REG_OK);
        } else {
            // IN PROGRESS
            redirect_header($currentFile, 5, "IN PROGRESS: error");
        }
        $xoopsTpl->assign('actionProts_ok', $actionProts_ok);
        $xoopsTpl->assign('actionProts_error', $actionProts_error);
        break;

    case "save_subscription" :
        include_once XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new XnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // check right to subscribe directly
        $submitterUid = is_object($xoopsUser) ? (int) $xoopsUser->getVar('uid') : 0;
        $allowedWithoutActivationKey = false;
        if ($submitterUid > 0) {
            // user logged in
            $submitter_email = $xoopsUser->email();
            foreach ($xoopsUser->getGroups() as $group) {
                if (in_array($group, $xnewsletter->getConfig('xn_groups_without_actkey')) || XOOPS_GROUP_ADMIN == $group) {
                    $allowedWithoutActivationKey = true;
                    break;
                }
            }
        } else {
            // user not logged in
            // NOP
        }

        if (!$activationKey) {
            // activation key doesn't exist
            if (!$GLOBALS["xoopsSecurity"]->check()) {
                redirect_header($currentFile, 3, implode(",", $GLOBALS["xoopsSecurity"]->getErrors()));
            }
            if ($subscr_email == '') {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
            }
            if (!xnewsletter_checkEmail($subscr_email))
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);


            // read current selections and create code for actkey
            $cat_selections = [];
            $code_selections = '';
            $catCriteria = new CriteriaCompo();
            $catCriteria->setSort('cat_id');
            $catCriteria->setOrder('ASC');
            $catObjs = $xnewsletter->getHandler('cat')->getAll($catCriteria);

            foreach ($catObjs as $cat_id => $catObj) {
                // create selection code: cat_id - cat selected - old catsubcr_id - old catsubscr_quited
                $code_selections .= ($code_selections == '') ? '' : '|';
                $code_selections .= $cat_id . "-";
                $code_selections .= (isset($_REQUEST["letter_cats_{$cat_id}"])) ? '1' : '0';
                $code_selections .= "-";
                $old_catsubcr_id = XoopsRequest::getInt("letter_cats_old_catsubcr_id_{$cat_id}", 0);
                $code_selections .= $old_catsubcr_id;
                $code_selections .= "-";
                $old_catsubcr_quited = XoopsRequest::getInt("letter_cats_old_catsubscr_quited_{$cat_id}", 0);
                $code_selections .= $old_catsubcr_quited;
            }

            // save subscriber first
            if ($subscr_id > 0) {
                $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
                $saveType = 'update';
            } else {
                $subscrObj = $xnewsletter->getHandler('subscr')->create();
                $saveType = 'addnew';
            }

            if ($subscr_id < 1 || $allowedWithoutActivationKey) {
                // form subscr_email
                $subscrObj->setVar('subscr_email', $subscr_email);
                // form subscr_uid
                $subscr_uid = 0;
                $sql = "SELECT `uid` FROM {$xoopsDB->prefix('users')}";
                $sql .= " WHERE (`email`='{$subscr_email}')";
                $sql .= " LIMIT 1";
                if ($user = $xoopsDB->query($sql)) {
                    $row_user = $xoopsDB->fetchRow($user);
                    $subscr_uid = $row_user[0];
                }
                $subscrObj->setVar('subscr_uid', (int) $subscr_uid);
                // form subscr_submitter
                $subscrObj->setVar('subscr_submitter', $submitterUid);
            }

            $subscr_actkey = xoops_makepass();
            $subscrObj->setVar('subscr_actkey', $subscr_actkey);
            // form subscr_created
            //$subscrObj->setVar("subscr_created", time()); //kann eigentlich immer gespeichert werden
            //$subscrObj->setVar("subscr_ip", $ip); //kann eigentlich immer gespeichert werden
            //Nicht speichern, sondern nur beim anlegen und 1. Bestaetigen
            if ($subscr_id < 1) {
                $subscrObj->setVar('subscr_created', time());
                $subscrObj->setVar('subscr_ip', $ip);
                $subscrObj->setVar('subscr_activated', 0);
            }

            if ($activationKey || $allowedWithoutActivationKey) {
                // subscr_firstname
                $subscrObj->setVar('subscr_firstname', XoopsRequest::getString('subscr_firstname', ''));
                // subscr_lastname
                $subscrObj->setVar('subscr_lastname',  XoopsRequest::getString('subscr_lastname', ''));
                // subscr_sex
                $subscrObj->setVar('subscr_sex', XoopsRequest::getString('subscr_sex', ''));
                // subscr_actoptions
                $subscrObj->setVar('subscr_actoptions', '');
            } else {
                //format subscr_actoptions: selected_newsletters||firstname||lastname||sex
                $code_options = [];
                $code_options[0] = $code_selections;
                $code_options[1] = XoopsRequest::getString('subscr_firstname', '');
                $code_options[2] = XoopsRequest::getString('subscr_lastname', '');
                $code_options[3] = XoopsRequest::getString('subscr_sex', '');
                $code_options[4] = time();
                $code_options[5] = $ip;
                $subscrObj->setVar('subscr_actoptions', serialize($code_options));
            }

            if ($xnewsletter->getHandler('subscr')->insert($subscrObj)) {
                if ($subscr_id < 1) {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_OK;
                } else {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_UPDATE_OK;
                }
                $subscr_id = $subscrObj->getVar('subscr_id');

                if ($allowedWithoutActivationKey) {
                    $isValid = true;
                    $cat_selections = explode('|', $code_selections);
                } else {
                    $isValid = false;
                    $xoopsMailer = xoops_getMailer();
                    $xoopsMailer->reset();
                    $xoopsMailer->setTemplateDir();
                    $xoopsMailer->useMail();
                    if ($saveType == 'update') {
                        $xoopsMailer->setTemplate('update.tpl');
                    } else {
                        $xoopsMailer->setTemplate('activate.tpl');
                    }
                    $xoopsMailer->setToEmails($subscr_email);
                    if (isset($xoopsConfig['adminmail'])) $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                    if (isset($xoopsConfig['sitename'])) $xoopsMailer->setFromName($xoopsConfig['sitename']);
                    $xoopsMailer->assign('SEX', $subscrObj->getVar("subscr_sex"));
                    $xoopsMailer->assign('EMAIL', $subscr_email);
                    $xoopsMailer->assign('FIRSTNAME', $subscrObj->getVar("subscr_firstname"));
                    $xoopsMailer->assign('LASTNAME', $subscrObj->getVar("subscr_lastname"));
                    $xoopsMailer->assign('IP', $ip);
                    $activationKey = base64_encode(XOOPS_URL . "||{$saveType}||{$subscr_id}||{$subscr_actkey}||{$subscr_email}");
                    $xoopsMailer->assign('ACTLINK', XOOPS_URL . "/modules/xnewsletter/{$currentFile}?actkey={$activationKey}");
                    $subject = _MA_XNEWSLETTER_SUBSCRIPTIONSUBJECT . $GLOBALS['xoopsConfig']['sitename'];
                    $xoopsMailer->setSubject($subject);
                    if (!$xoopsMailer->send()) {
                        redirect_header($currentFile, 10, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . "<br />" . $xoopsMailer->getErrors());
                    }
                    $actionProts_ok[] = _MA_XNEWSLETTER_SENDMAIL_REG_OK;
                }
            } else {
                redirect_header($currentFile, 20, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR . "<br />" . $subscrObj->getHtmlErrors());
            }
        } else {
            // activation key exist
            $activationKey_array = explode('||', base64_decode($activationKey));
            $isValid = false;
            if ($activationKey_array[0] == XOOPS_URL) { // from here
                if (trim($activationKey_array[1]) != '') { // savetype ok
                    if ((int) $activationKey_array[2] > 0) { // user is ok
                        if (trim($activationKey_array[3]) != '') {
                            $isValid = true;
                        }
                    }
                }
            }

            if (!$isValid) {
                redirect_header($currentFile, 5, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_INVALIDKEY);
            } else {
                $saveType = trim($activationKey_array[1]);
                $subscr_id = (int) $activationKey_array[2];
                $subscr_actkey = trim($activationKey_array[3]);

                //check given data with table subscr
                $subscrCriteria = new CriteriaCompo();
                $subscrCriteria->add(new Criteria('subscr_id', $subscr_id));
                $subscrCriteria->add(new Criteria('subscr_actkey', $subscr_actkey));
                $subscrCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
                if ($subscrCount == 0)
                    redirect_header($currentFile, 5, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NODATAKEY);

                //read data from table subscr
                $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
                $actoptions = unserialize(trim($subscrObj->getVar('subscr_actoptions', 'N')));
                //format subscr_actoptions:selected_newsletters||firstname||lastname||sex||date||ip
                $cat_selections = explode('|', trim($actoptions[0]));
                $subscr_firstname = trim($actoptions[1]);
                $subscr_lastname = trim($actoptions[2]);
                $subscr_sex = trim($actoptions[3]);
                if ((int) $actoptions[4] < time() - 86400) { //Zeit checken -> 24 Stunden ??
                    //Zeit abgelaufen
                    $subscrObj->setVar('subscr_actkey', '');
                    $subscrObj->setVar('subscr_actoptions', '');
                    $xnewsletter->getHandler('subscr')->insert($subscrObj);
                    redirect_header($currentFile, 5, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NODATAKEY);
                }
            }
        }

        if ($isValid) {
            // update xnewsletter_subscr
            $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
            if (!$allowedWithoutActivationKey) {
                if ($subscr_actkey != $subscrObj->getVar('subscr_actkey')) {
                    redirect_header($currentFile, 2, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOVALIDKEY);
                }
            }
            if ($subscrObj->getVar('subscr_activated') == 0) {
                $subscrObj->setVar('subscr_created', time());
                $subscrObj->setVar('subscr_ip', $ip);
                $subscrObj->setVar('subscr_activated', 1);
            }
            $subscrObj->setVar('subscr_actkey', '');
            $subscrObj->setVar('subscr_actoptions', '');

            if ($activationKey) {
                $subscrObj->setVar('subscr_sex', $subscr_sex);
                $subscrObj->setVar('subscr_firstname', $subscr_firstname);
                $subscrObj->setVar('subscr_lastname', $subscr_lastname);
            }
            if (!$xnewsletter->getHandler('subscr')->insert($subscrObj)) {
                redirect_header($currentFile, 2, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR);
            } else {
                if ($saveType == 'addnew') {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED;
                } else {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_UPDATE_CLOSED;
                }
            }
            // handle current selections
            foreach ($cat_selections as $sel) {
                $selection = [];
                if ($sel == '') $sel = '0-0-0-0';
                $selection = explode('-', $sel);
                $cat_id = $selection[0];
                $catsubcr = $selection[1];
                $catsubcr_id_old = (int) $selection[2];
                $catsubcr_quited_old = (int) $selection[3];
                $catObj = $xnewsletter->getHandler('cat')->get($cat_id);
                $cat_mailinglist = $catObj->getVar('cat_mailinglist');
                $cat_name = $catObj->getVar('cat_name');
                if ($catsubcr == '1' && $catsubcr_id_old == 0) {
                    // subscribe
                    $catsubscrObj = $xnewsletter->getHandler('catsubscr')->create();
                    //Form catsubscr_catid
                    $catsubscrObj->setVar("catsubscr_catid", $cat_id);
                    //Form catsubscr_subscrid
                    $catsubscrObj->setVar("catsubscr_subscrid", $subscr_id);
                    //Form catsubscr_submitter
                    $catsubscrObj->setVar("catsubscr_submitter", $submitterUid);
                    //Form catsubscr_submitter
                    $catsubscrObj->setVar("catsubscr_created", time());
                    if ($xnewsletter->getHandler('catsubscr')->insert($catsubscrObj)) {
                        $count_ok++;
                        if ($catsubcr_id_old > 0) {
                            $actionProts_ok[] = str_replace("%nl", $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_NO_CHANGE);
                        } else {
                            $actionProts_ok[] = str_replace("%nl", $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_SUBSCRIBE);
                        }
                        //handle mailinglists
                        if ($cat_mailinglist > 0) {
                            require_once XOOPS_ROOT_PATH . "/modules/xnewsletter/include/mailinglist.php";
                            subscribingMLHandler(1, $subscr_id, $cat_mailinglist);
                        }
                    } else {
                        $count_err++;
                        $actionProts_error[]= _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR . $catsubscrObj->getHtmlErrors();
                    }
                } elseif ($catsubcr == '0' && $catsubcr_id_old > 0) {
                    // unsubscribe / delete old subscription
                    $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($catsubcr_id_old);
                    if ($xnewsletter->getHandler('catsubscr')->delete($catsubscrObj, true)) {
                        //handle mailinglists
                        if ($cat_mailinglist > 0) {
                            require_once XOOPS_ROOT_PATH . "/modules/xnewsletter/include/mailinglist.php";
                            subscribingMLHandler(0, $subscr_id, $cat_mailinglist);
                        }
                    } else {
                        $count_err++;
                        $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR . $catsubscrObj->getHtmlErrors();
                    }

                    if ($count_err > 0) {
                        redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELNOTOK);
                    }
                    $actionProts_ok[] = str_replace ("%nl", $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_UNSUBSCRIBE);
                } elseif ($catsubcr_id_old > 0 && $catsubcr_quited_old > 0) {
                    // newsletter stay selected, but catsubscr_quited will be removed
                    $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($catsubcr_id_old);
                    //Form catsubscr_quited
                    $catsubscrObj->setVar('catsubscr_quited', '0');

                    if ($xnewsletter->getHandler('catsubscr')->insert($catsubscrObj)) {
                        $count_ok++;
                        $actionProts_ok[] = str_replace ("%nl", $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_DAT_QUITED_REMOVED);
                    } else {
                        $count_err++;
                        $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR . $catsubscrObj->getHtmlErrors();
                    }
                } elseif ($catsubcr_id_old > 0) {
                    // newsletter still subscribed
                    $actionProts_ok[] = str_replace ("%nl", $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_NO_CHANGE);
                } else {
                    // nothing to do
                }
            }
        }



        if (isset($submitter_email) && ($submitter_email != '') && ($submitter_email != $subscr_email)) {
            //send infomail to subscriber, because current user is not the subscriber
            if ($subscr_sex == '' && $subscr_firstname == '' && $subscr_lastname == '') {
                $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
                $subscr_sex = $subscrObj->getVar('subscr_sex');
                $subscr_firstname = $subscrObj->getVar('subscr_firstname');
                $subscr_lastname = $subscrObj->getVar('subscr_lastname');
            }
            $xoopsMailer = xoops_getMailer();
            $xoopsMailer->reset();
            $xoopsMailer->setTemplateDir();
            $xoopsMailer->useMail();
            $xoopsMailer->setHTML();
            $xoopsMailer->setTemplate('info_change.tpl');
            $xoopsMailer->setToEmails($subscr_email);
            if (isset($xoopsConfig['adminmail'])) $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            if (isset($xoopsConfig['sitename'])) $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->assign('SEX', $subscr_sex);
            $xoopsMailer->assign('EMAIL', $subscr_email);
            $xoopsMailer->assign('FIRSTNAME', $subscr_firstname);
            $xoopsMailer->assign('LASTNAME', $subscr_lastname);

            $xoopsMailer->assign('IP', $ip);
            $actlink = XOOPS_URL . "/modules/xnewsletter/{$currentFile}?subscr_email={$subscr_email}";
            $xoopsMailer->assign('ACTLINK', $actlink);
            $user_link = XOOPS_URL . "/userinfo.php?uid=" . $xoopsUser->uid();
            $user_name = $xoopsUser->name();
            $xoopsMailer->assign('USERLINK', $user_link);
            $xoopsMailer->assign('USERNAME', $user_name);
            $subject_change = _MA_XNEWSLETTER_SUBSCRIPTION_SUBJECT_CHANGE . $GLOBALS['xoopsConfig']['sitename'];
            $xoopsMailer->setSubject($subject_change);
            if ($xoopsMailer->send()) {
                $actionProts_ok[] = str_replace("%e", $subscr_email, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_SENT_INFO);
            } else {
                redirect_header($currentFile, 10, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . "<br />" . $xoopsMailer->getErrors());
            }
        }

        $xoopsTpl->assign('actionProts_ok', $actionProts_ok);

        if ($count_err > 0) {
            $xoopsTpl->assign('subscription_result', _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
            $xoopsTpl->assign('actionProts_error', $actionProts_error);
        } else {
            $xoopsTpl->assign('subscription_result', _MA_XNEWSLETTER_SUBSCRIPTION_OK);
        }
        break;



    case "add_subscription" :
    case "create_subscription" :
        $xoopsOption['template_main'] = 'xnewsletter_subscription.tpl';
        include_once XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new XnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get create subscr form
        if ($subscr_email != '') {
            //existing email
            if (!xnewsletter_checkEmail($subscr_email)) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
            }
        } elseif (is_object($xoopsUser) && isset($xoopsUser)) {
            //take actual xoops user
            $subscr_email = $xoopsUser->email();
        } else {
            $subscr_email = '';
        }
        $subscrObj = $xnewsletter->getHandler('subscr')->create();
        $subscrObj->setVar('subscr_email', $subscr_email);
        $subscrForm = $subscrObj->getForm();
        $xoopsTpl->assign('xnewsletter_content', $subscrForm->render());
        break;



    case "edit_subscription" :
        $xoopsOption['template_main'] = 'xnewsletter_subscription.tpl';
        include_once XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new XnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, XNEWSLETTER_URL . '/subscription.php?op=list_subscriptions');
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIPTION_EDIT, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get edit subscr form
        $subscr_id = XoopsRequest::getInt('subscr_id', 0);
        if ($subscr_id <= 0) {
            redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOID);
        }
        $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
        $subscrForm = $subscrObj->getForm();
        $xoopsTpl->assign('xnewsletter_content', $subscrForm->render());
        break;



    case "delete_subscription" :
        include_once XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new XnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, XNEWSLETTER_URL . '/subscription.php?op=list_subscriptions');
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIPTION_DELETE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        if (!$activationKey) {
            if ($subscr_id < 1) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOID);
            }
        }

        if ((isset($_POST['ok']) && $_POST['ok'] == true) || $activationKey) {
            $count_err = 0;
            $actionProts_error = '';

            // check right to unsubscribe directly
            $submitterUid = is_object($xoopsUser) ? (int) $xoopsUser->getVar('uid') : 0;
            if ($submitterUid == 0) {
                // user not logged in
                $allowedWithoutActivationKey = false;
            } else {
                // user logged in
                $allowedWithoutActivationKey = false;
                foreach ($xoopsUser->getGroups() as $group) {
                    if (in_array($group, $xnewsletter->getConfig('xn_groups_without_actkey')) || XOOPS_GROUP_ADMIN == $group) {
                        $allowedWithoutActivationKey = true;
                        break;
                    }
                }
            }

            if ($activationKey || $allowedWithoutActivationKey) {
                // got actkey or user is allowed to delete without actkey
                $isValid = false;
                if ($activationKey) {
                    $activationKey_array = explode('||', base64_decode($activationKey));
                    $subscr_id = (int) $activationKey_array[1];
                    $subscr_actkey = trim($activationKey_array[2]);
                    $subscr_email = trim($activationKey_array[3]);
                    // check activation key
                    if (($activationKey_array[0] == XOOPS_URL) && ((int) $activationKey_array[1] > 0) && (trim($activationKey_array[2]) != ''))
                        $isValid = true;
                } elseif ($allowedWithoutActivationKey) {
                    $isValid = true;
                } else {
                    $isValid = false;
                }

                if ($isValid) {
                    $subscrCriteria = new CriteriaCompo();
                    $subscrCriteria->add(new Criteria('subscr_email', $subscr_email));
                    $subscrCriteria->add(new Criteria('subscr_id', $subscr_id));
                    if ($activationKey)
                        $subscrCriteria->add(new Criteria('subscr_actkey', $subscr_actkey));
                    $subscrCriteria->setLimit(1);
                    $subscrCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);

                    if ($subscrCount != 1) {
                        redirect_header($currentFile, 2, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
                    }

                    $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
/*
                    $sql = "SELECT subscr_id";
                    $sql.= " FROM {$xoopsDB->prefix("xnewsletter_subscr")}";
                    $sql.= " WHERE (subscr_email='{$subscr_email}' AND subscr_id={$subscr_id}";
                    if ($activationKey)
                        $sql .= " AND subscr_actkey='{$code}'";
                    $sql .= ") LIMIT 1;";
                    $subscr_id_test = 0;
                    if ($user = $xoopsDB->query($sql)) {
                        $row_user = $xoopsDB->fetchRow($user);
                        $subscr_id_test = $row_user[0];
                    }
                    if ($subscr_id != $subscr_id_test) {
                        redirect_header($currentFile, 2, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
                    }
                    $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
*/
                    // delete subscriber
                    if (!$xnewsletter->getHandler('subscr')->delete($subscrObj, true)) {
                        $actionProts_error = $subscrObj->getHtmlErrors() . "<br/><br/><br/>";
                        $count_err++;
                    }
                    //delete subscription
                    $catsubscrCriteria = new CriteriaCompo();
                    $catsubscrCriteria->add(new Criteria('catsubscr_subscrid', $subscr_id));
                    $catsubscrCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
                    if ($catsubscrCount > 0) {
                        $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
                        foreach (array_keys($catsubscrObjs) as $cat) {
                            $catsubscrObj = $xnewsletter->getHandler('catsubscr')->get($catsubscrObjs[$cat]->getVar("catsubscr_id"));
                            $catObj = $xnewsletter->getHandler('cat')->get($catsubscrObjs[$cat]->getVar("catsubscr_catid"));
                            $cat_mailinglist = $catObj->getVar("cat_mailinglist");

                            if ($xnewsletter->getHandler('catsubscr')->delete($catsubscrObj, true)) {
                                //handle mailinglists
                                if ($cat_mailinglist > 0) {
                                    require_once XOOPS_ROOT_PATH . "/modules/xnewsletter/include/mailinglist.php";
                                    subscribingMLHandler(0, $subscr_id, $cat_mailinglist);
                                }
                            } else {
                                $actionProts_error .= $catsubscrObj->getHtmlErrors();
                                $count_err++;
                            }
                        }
                    }
                } else {
                    redirect_header($currentFile, 2, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
                }


                if ($count_err == 0) {
                    redirect_header('index.php', 3, _AM_XNEWSLETTER_FORMDELOK);
                } else {
                    echo $actionProts_error;
                }
            } else {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('subscr.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }

                $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
                $subscr_actkey = xoops_makepass();
                $subscrObj->setVar('subscr_actkey', $subscr_actkey);
                if (!$xnewsletter->getHandler('subscr')->insert($subscrObj)) {
                    redirect_header($currentFile, 2, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
                }
                if (!$allowedWithoutActivationKey) {
                    $xoopsMailer = xoops_getMailer();
                    $xoopsMailer->reset();
                    $xoopsMailer->setTemplateDir();
                    $xoopsMailer->useMail();
                    $xoopsMailer->setTemplate('delete.tpl');
                    $xoopsMailer->setToEmails($subscrObj->getVar('subscr_email'));
                    if (isset($xoopsConfig['adminmail'])) $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                    if (isset($xoopsConfig['sitename'])) $xoopsMailer->setFromName($xoopsConfig['sitename']);
                    $xoopsMailer->assign('SEX', $subscrObj->getVar('subscr_sex'));
                    $xoopsMailer->assign('EMAIL', $subscrObj->getVar('subscr_email'));
                    $xoopsMailer->assign('FIRSTNAME', $subscrObj->getVar('subscr_firstname'));
                    $xoopsMailer->assign('LASTNAME', $subscrObj->getVar('subscr_lastname'));
                    $xoopsMailer->assign('IP', $ip);
                    $activationKey = base64_encode(XOOPS_URL . "||{$subscrObj->getVar('subscr_id')}||{$subscrObj->getVar('subscr_actkey')}||{$subscrObj->getVar('subscr_email')}");
                    $actLink = XOOPS_URL . "/modules/xnewsletter/{$currentFile}?op=delete_subscription_confirmed&actkey={$activationKey}";
                    $xoopsMailer->assign('ACTLINK', $actLink);
                    $subject_delete = _MA_XNEWSLETTER_DELETESUBJECT . $GLOBALS['xoopsConfig']['sitename'];
                    $xoopsMailer->setSubject($subject_delete);
                    if (!$xoopsMailer->send()) {
                        redirect_header($currentFile, 10, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR . "<br />" . $xoopsMailer->getErrors());
                    }
                }
                redirect_header('index.php', 3, _MA_XNEWSLETTER_SENDMAIL_UNREG_OK);
            }
        } else {
            $subscrObj = $xnewsletter->getHandler('subscr')->get($subscr_id);
            xoops_confirm(['ok' => true, 'subscr_id' => $subscr_id, 'subscr_email' => $subscr_email, 'op' => 'delete_subscription'], $currentFile, sprintf(_MA_XNEWSLETTER_SUBSCRIPTION_DELETE_SURE));
        }
        break;

    case "list_subscriptions" :
    default :
        $xoopsOption['template_main'] = 'xnewsletter_subscription_list_subscriptions.tpl';
        include_once XOOPS_ROOT_PATH . "/header.php";

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description

        // Breadcrumb
        $breadcrumb = new XnewsletterBreadcrumb();
        $breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $showSubscrSearchForm = true;
        $showSubscrForm = false;

        $subscr_id = 0;
        // get subscr email
        $subscr_email = XoopsRequest::getString('subscr_email', '');
        if ($subscr_email != '') {
            // existing email from search form
            if (!xnewsletter_checkEmail($subscr_email))
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
            xoops_load("captcha");
            $xoopsCaptcha = XoopsCaptcha::getinstance();
            if (!$xoopsCaptcha->verify()) {
                $_SESSION['redirect_mail'] = $subscr_email;
                redirect_header($currentFile, 3, $xoopsCaptcha->getMessage());
            } else {
                $showSubscrSearchForm = false;
            }
        } elseif (is_object($xoopsUser) && isset($xoopsUser)) {
            // take actual xoops user email
            $subscr_email = $xoopsUser->email();
            $showSubscrSearchForm = false;
        } else {
            $subscr_email = '';
        }

        // show search subscr form
        $xoopsTpl->assign('showSubscrSearchForm', $showSubscrSearchForm);
        if ($showSubscrSearchForm) {
            // show form search
            $subscrObj = $xnewsletter->getHandler('subscr')->create();
            $redirect_mail = (isset($_SESSION['redirect_mail'])) ? $_SESSION['redirect_mail'] : '';
            if ($redirect_mail != '') {
                $subscrObj->setVar('subscr_email', $redirect_mail);
                $subscr_email = $redirect_mail;
            }
            $subscrObj->setVar('subscr_email', $subscr_email);
            $xoopsTpl->assign('subscrSearchForm', $subscrObj->getSearchForm()->render());
        }

        if (isset($_SESSION['redirect_mail'])) {
            if (!isset($_SESSION['unsub'])) {
                $subscr_email = '';
            } else {
                unset($_SESSION['unsub']);
            }
            unset($_SESSION['redirect_mail']);
        }

        if ($subscr_email != '') {
            // look for existing subscriptions
            $subscrCriteria = new CriteriaCompo();
            $subscrCriteria->add(new Criteria('subscr_email', $subscr_email));
            $subscrCriteria->setSort('subscr_id');
            $subscrCriteria->setOrder('ASC');
            $subscrCount = $xnewsletter->getHandler('subscr')->getCount($subscrCriteria);
            $xoopsTpl->assign('subscrCount', $subscrCount);

            if ($subscrCount > 0) {
                $subscrObjs = $xnewsletter->getHandler('subscr')->getAll($subscrCriteria);
                foreach ($subscrObjs as $subscr_id => $subscrObj) {
                    $subscr_array = $subscrObj->toArray();
                    $subscr_array['subscr_created_timestamp'] = formatTimestamp($subscrObj->getVar('subscr_created'), $xnewsletter->getConfig('dateformat'));

                    $catsubscrCriteria = new CriteriaCompo();
                    $catsubscrCriteria->add(new Criteria('catsubscr_subscrid', $subscr_id));
                    $catsubscrCriteria->setSort('catsubscr_id');
                    $catsubscrCriteria->setOrder('ASC');
                    $catsubscrCount = $xnewsletter->getHandler('catsubscr')->getCount($catsubscrCriteria);
                    $catsubscrObjs = $xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
                    foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                        $catsubscr_array = $catsubscrObj->toArray();
                        $catObj = $xnewsletter->getHandler('cat')->get($catsubscrObj->getVar('catsubscr_catid'));
                        $cat_array = $catObj->toArray();
                        $catsubscr_array['cat'] = $cat_array;
                        $subscr_array['catsubscrs'][] = $catsubscr_array;
                        unset($catsubscr_array);
                        unset($cat_array);
                    }
                    $xoopsTpl->append('subscrs', $subscr_array);
                }
            } else {
                // show subscr form
                $xoopsTpl->assign('showSubscrForm', true);
                $subscrObj = $xnewsletter->getHandler('subscr')->create();
                $subscrObj->setVar('subscr_email', $subscr_email);
                $form = $subscrObj->getForm($currentFile);
                $xoopsTpl->assign('subscrForm', $form->render());
            }
        }
        break;
}

include __DIR__ . '/footer.php';
