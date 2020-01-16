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
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

use XoopsModules\Xnewsletter;
use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/header.php';
$op            = Request::getString('op', 'search_subscription');
$activationKey = Request::getString('actkey', '');
$subscr_id     = Request::getInt('subscr_id', 0);
$subscr_email  = Request::getString('subscr_email', '');

if (Request::hasVar('addnew', 'REQUEST')) {
    $op = 'addnew_subscription';
}
if ('' != $activationKey && 'unsub' !== $op) {
    $op = 'save_subscription';
}
if ('unsub' === $op) {
    $subscr_email = Request::getString('email', '');
    $op           = 'delete_subscription';
    //$GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription.tpl';
    $_SESSION['redirect_mail'] = Request::getString('email', '');
    $_SESSION['unsub']         = '1';
} else {
    $_SESSION['redirect_mail'] = '';
    $_SESSION['unsub']         = '0';
}

//to avoid errors in debug when xn_groups_change_other
$subscr_sex       = '';
$subscr_firstname = '';
$subscr_lastname  = '';

switch ($op) {
    case 'search_subscription':
    default:
        // if not anonymous subscriber / subscriber is a Xoops user
        if (is_object($xoopsUser) && isset($xoopsUser)) {
            header("Location:{$currentFile}?op=list_subscriptions&subscr_email=" . $subscr_email);
            exit();
        }
        // if anonymous subscriber
        $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription_list_subscriptions.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        $actionProts_ok      = [];
        $actionProts_error   = [];
        $actionProts_warning = [];

        $subscr_email         = '';
        $showSubscrSearchForm = true;
        $showSubscrForm       = false;

        // show search subscr form
        $xoopsTpl->assign('showSubscrSearchForm', $showSubscrSearchForm);
        // show form search
        $subscrObj = $helper->getHandler('Subscr')->create();
        $xoopsTpl->assign('subscrSearchForm', $subscrObj->getSearchForm()->render());

        break;
    case 'list_subscriptions':
        $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription_list_subscriptions.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());
        // init vars
        $actionProts_ok       = [];
        $actionProts_warning  = [];
        $actionProts_error    = [];
        $showSubscrSearchForm = false;
        $showSubscrForm       = true;

        if (is_object($xoopsUser) && isset($xoopsUser)) {
            // if not anonymous subscriber / subscriber is a Xoops user get subscr_email from Xoops user
            $redirect_mail = ('' === $subscr_email) ? $xoopsUser->email() : $subscr_email;
            $_SESSION['redirect_mail'] = $subscr_email;
        } else {
            // if anonymous subscriber get subscr_email from search form
            $subscr_email = Request::getString('subscr_email', '');
            if ('' != $subscr_email) {
                // check captcha
                xoops_load('xoopscaptcha');
                $xoopsCaptcha = XoopsCaptcha::getinstance();
                if (!$xoopsCaptcha->verify()) {
                    $_SESSION['redirect_mail'] = $subscr_email;
                    redirect_header('?op=search_subscription', 3, $xoopsCaptcha->getMessage());
                }
                // check subscr_email
                if (!xnewsletter_checkEmail($subscr_email)) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
                }
                // check if a Xoops user has $subscr_email
                if (0 != count($memberHandler->getUsers(new \Criteria('email', $subscr_email)))) {
                    $actionProts_warning[] = sprintf(_MA_XNEWSLETTER_PLEASE_LOGIN, $subscr_email);

                    $xoopsTpl->assign('actionProts_ok', $actionProts_ok);
                    $xoopsTpl->assign('actionProts_warning', $actionProts_warning);
                    $xoopsTpl->assign('actionProts_error', $actionProts_error);
                    break;
                }
            } else {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
            }
        }

        // ???
        /*
        if (Request::hasVar('redirect_mail', 'SESSION')) {
            if (!isset($_SESSION['unsub'])) {
                $subscr_email = $_SESSION['redirect_mail'];
            } else {
                unset($_SESSION['unsub']);
            }
            unset($_SESSION['redirect_mail']);
        }
        */

        // look for existing subscriptions
        if ('' === $subscr_email) {
            $subscr_email = $redirect_mail;
        }
        $subscrCriteria = new \CriteriaCompo();
        $subscrCriteria->add(new \Criteria('subscr_email', $subscr_email));
        $subscrCriteria->setSort('subscr_id');
        $subscrCriteria->setOrder('ASC');
        $subscrCount = $helper->getHandler('Subscr')->getCount($subscrCriteria);
        $xoopsTpl->assign('subscrCount', $subscrCount);
        if ($subscrCount > 0) {
            // there are subscriptions with this email
            $actionProts_warning[] = _MA_XNEWSLETTER_REGISTRATION_EXIST;
            $subscrObjs            = $helper->getHandler('Subscr')->getAll($subscrCriteria);
            foreach ($subscrObjs as $subscrObj) {
                $subscr_array                             = $subscrObj->toArray();
                $subscr_array['subscr_created_formatted'] = formatTimestamp($subscr_array['subscr_created'], $helper->getConfig('dateformat'));
                // subscr exists but is unactivated
                if (0 == $subscr_array['subscr_activated']) {
                    $actionProts_warning[] = str_replace('%link', "?op=resend_subscription&subscr_id={$subscr_array['subscr_id']}", _MA_XNEWSLETTER_SUBSCRIPTION_UNFINISHED);
                }
                $catsubscrCriteria = new \CriteriaCompo();
                $catsubscrCriteria->add(new \Criteria('catsubscr_subscrid', $subscr_array['subscr_id']));
                $catsubscrCriteria->setSort('catsubscr_id');
                $catsubscrCriteria->setOrder('ASC');
                $catsubscrCount = $helper->getHandler('Catsubscr')->getCount($catsubscrCriteria);
                $catsubscrObjs  = $helper->getHandler('Catsubscr')->getAll($catsubscrCriteria);
                foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                    $catsubscr_array              = $catsubscrObj->toArray();
                    $catObj                       = $helper->getHandler('Cat')->get($catsubscrObj->getVar('catsubscr_catid'));
                    $cat_array                    = $catObj->toArray();
                    $catsubscr_array['cat']       = $cat_array;
                    $subscr_array['catsubscrs'][] = $catsubscr_array;
                    unset($catsubscr_array);
                    unset($cat_array);
                }
                $xoopsTpl->append('subscrs', $subscr_array);
            }
        } else {
            // show subscr form
            $actionProts_warning[] = str_replace('%s', $subscr_email, _MA_XNEWSLETTER_REGISTRATION_NONE);
            $xoopsTpl->assign('showSubscrForm', true);
            $subscrObj = $helper->getHandler('Subscr')->create();
            $subscrObj->setVar('subscr_email', $subscr_email);
            $form = $subscrObj->getForm($currentFile);
            $xoopsTpl->assign('subscrForm', $form->render());
        }

        $xoopsTpl->assign('actionProts_ok', $actionProts_ok);
        $xoopsTpl->assign('actionProts_warning', $actionProts_warning);
        $xoopsTpl->assign('actionProts_error', $actionProts_error);
        break;
    case 'resend_subscription':
        $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription_result.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());
        // init vars
        $actionProts_ok      = [];
        $actionProts_warning = [];
        $actionProts_error   = [];

        // check if subscr exists
        $subscr_id      = Request::getInt('subscr_id', 0);
        $subscrCriteria = new \Criteria('subscr_id', $subscr_id);
        $subscrCount    = $helper->getHandler('Subscr')->getCount($subscrCriteria);
        if (0 == $subscrCount) {
            redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOID);
        }
        // get subscr data
        $subscrObj    = $helper->getHandler('Subscr')->get($subscr_id);
        $subscr_email = $subscrObj->getVar('subscr_email');
        // resend the email with the confirmation code
        $xoopsMailer = xoops_getMailer();
        $xoopsMailer->reset();
        $xoopsMailer->setTemplateDir();
        $xoopsMailer->useMail();
        $xoopsMailer->setTemplate('activate.tpl');
        $xoopsMailer->setToEmails($subscr_email);
        if (isset($xoopsConfig['adminmail'])) {
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        }
        if (isset($xoopsConfig['sitename'])) {
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
        }
        $xoopsMailer->assign('EMAIL', $subscr_email);
        $xoopsMailer->assign('SEX', $subscrObj->getVar('subscr_sex'));
        $xoopsMailer->assign('FIRSTNAME', $subscrObj->getVar('subscr_firstname'));
        $xoopsMailer->assign('LASTNAME', $subscrObj->getVar('subscr_lastname'));
        $xoopsMailer->assign('IP', xoops_getenv('REMOTE_ADDR'));
        $activationKey = base64_encode(XOOPS_URL . "||addnew||{$subscr_id}||{$subscrObj->getVar('subscr_actkey')}||{$subscr_email}");
        $xoopsMailer->assign('ACTLINK', XOOPS_URL . "/modules/xnewsletter/{$currentFile}?actkey={$activationKey}");
        $subject = _MA_XNEWSLETTER_SUBSCRIPTIONSUBJECT . $GLOBALS['xoopsConfig']['sitename'];
        $xoopsMailer->setSubject($subject);
        if (!$xoopsMailer->send()) {
            $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors();
        } else {
            $actionProts_ok[] = str_replace('%subscr_email', $subscr_email, _MA_XNEWSLETTER_RESENDMAIL_REG_OK);
        }

        $xoopsTpl->assign('actionProts_ok', $actionProts_ok);
        $xoopsTpl->assign('actionProts_warning', $actionProts_warning);
        $xoopsTpl->assign('actionProts_error', $actionProts_error);
        break;
    case 'add_subscription':
    case 'create_subscription':
        $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get create subscr form
        if ('' != $subscr_email) {
            // existing email
            if (!xnewsletter_checkEmail($subscr_email)) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
            }
        } elseif (is_object($xoopsUser) && isset($xoopsUser)) {
            // take actual xoops user
            $subscr_email = $xoopsUser->email();
        } else {
            $subscr_email = '';
        }
        $subscrObj = $helper->getHandler('Subscr')->create();
        $subscrObj->setVar('subscr_email', $subscr_email);
        $subscrForm = $subscrObj->getForm();
        $xoopsTpl->assign('xnewsletter_content', $subscrForm->render());
        break;
    case 'edit_subscription':
        $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, XNEWSLETTER_URL . '/subscription.php?op=list_subscriptions');
        $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIPTION_EDIT, '');
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

        // get edit subscr form
        $subscr_id = Request::getInt('subscr_id', 0);
        if ($subscr_id <= 0) {
            redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOID);
        }
        $subscrObj  = $helper->getHandler('Subscr')->get($subscr_id);
        $subscrForm = $subscrObj->getForm();
        $xoopsTpl->assign('xnewsletter_content', $subscrForm->render());
        break;
    case 'save_subscription':
        $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription_result.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';

        $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
        $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
        $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
        // breadcrumb
        $breadcrumb = new Xnewsletter\Breadcrumb();
        $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
        $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());
        // init vars
        $actionProts_ok       = [];
        $actionProts_warning  = [];
        $actionProts_error    = [];
        $count_ok             = 0;
        $count_err            = 0;
        $activationKeyIsValid = false;
        // check right to subscribe directly
        $allowedWithoutActivationKey = false;
        $uid                         = is_object($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
        if (is_object($xoopsUser) && isset($xoopsUser)) {
            // if not anonymous subscriber / subscriber is a Xoops user
            $submitter_email = $xoopsUser->email();
            foreach ($xoopsUser->getGroups() as $group) {
                if (in_array($group, $helper->getConfig('xn_groups_without_actkey'))
                    || XOOPS_GROUP_ADMIN == $group) {
                    $allowedWithoutActivationKey = true;
                    break;
                }
            }
        }
        // if anonymous subscriber
        // NOP

        if ($allowedWithoutActivationKey) {
            // 1st case: subscribe WITHOUT confirmation
            // check form
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            // check email
            if ('' == $subscr_email || !xnewsletter_checkEmail($subscr_email)) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
            }
            // get or create subscr
            if ($subscr_id > 0) {
                $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
                $saveType  = 'update';
            } else {
                $subscrObj = $helper->getHandler('Subscr')->create();
                $saveType  = 'addnew';
            }
            $subscrObj->setVar('subscr_sex', Request::getString('subscr_sex', ''));
            $subscrObj->setVar('subscr_firstname', Request::getString('subscr_firstname', ''));
            $subscrObj->setVar('subscr_lastname', Request::getString('subscr_lastname', ''));
            $subscrObj->setVar('subscr_email', Request::getString('subscr_email', ''));
            // insert subscr
            if (!$helper->getHandler('Subscr')->insert($subscrObj)) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR . '<br>' . $subscrObj->getHtmlErrors());
            }
            if ($subscr_id > 0) {
                $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_UPDATE_OK;
            } else {
                $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_OK;
            }
            $subscr_id = $subscrObj->getVar('subscr_id');
            // create $code_selections string
            $catCriteria = new \CriteriaCompo();
            $catCriteria->setSort('cat_id');
            $catCriteria->setOrder('ASC');
            $catObjs    = $helper->getHandler('Cat')->getAll($catCriteria);
            $selections = [];
            foreach ($catObjs as $cat_id => $catObj) {
                // create selections: $cat_id-$cat_selected-$old_catsubcr_id-$old_catsubscr_quited
                $selection      = [];
                $selection[0]   = $cat_id;
                $selection[1]   = in_array($cat_id, $_REQUEST['cats']) ? '1' : '0'; //isset($_REQUEST["cats_{$cat_id}"]);
                $selection[2]   = Request::getInt("existing_catsubcr_id_{$cat_id}", 0);
                $selection[3]   = Request::getInt("existing_catsubscr_quited_{$cat_id}", 0);
                $code_selection = implode('-', $selection);
                $selections[]   = $code_selection;
                unset($selection);
            }
            $code_selections = implode('|', $selections);
        }

        if (!$allowedWithoutActivationKey) {
            // 2nd case: subscribe WITH confirmation
            if ('' == $activationKey) {
                // activation key DOESN'T EXIST
                // create and send confirmation email
                // check form
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                // check email
                if ('' == $subscr_email || !xnewsletter_checkEmail($subscr_email)) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
                }
                // get subscr fields from form
                $subscr_firstname = Request::getString('subscr_firstname', '');
                $subscr_lastname  = Request::getString('subscr_lastname', '');
                $subscr_sex       = Request::getString('subscr_sex', '');
                // create $code_selections string
                $catCriteria = new \CriteriaCompo();
                $catCriteria->setSort('cat_id');
                $catCriteria->setOrder('ASC');
                $catObjs    = $helper->getHandler('Cat')->getAll($catCriteria);
                $selections = [];
                foreach ($catObjs as $cat_id => $catObj) {
                    // create selections: $cat_id-$cat_selected-$old_catsubcr_id-$old_catsubscr_quited
                    $selection      = [];
                    $selection[0]   = $cat_id;
                    $selection[1]   = in_array($cat_id, Request::getArray('cats')) ? '1' : '0'; //isset($_REQUEST["cats_{$cat_id}"]);
                    $selection[2]   = Request::getInt("existing_catsubcr_id_{$cat_id}", 0);
                    $selection[3]   = Request::getInt("existing_catsubscr_quited_{$cat_id}", 0);
                    $code_selection = implode('-', $selection);
                    $selections[]   = $code_selection;
                    unset($selection);
                }
                $code_selections = implode('|', $selections); // string
                //
                // get or create subscr
                if ($subscr_id > 0) {
                    $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
                    $saveType  = 'update';
                } else {
                    $subscrObj = $helper->getHandler('Subscr')->create();
                    $saveType  = 'addnew';
                }
                // fill subscr
                if ($subscr_id <= 0) {
                    // form subscr_email
                    $subscrObj->setVar('subscr_email', $subscr_email);
                    // form subscr_uid
                    $subscr_uid = 0;
                    $sql        = "SELECT `uid` FROM {$xoopsDB->prefix('users')}";
                    $sql        .= " WHERE (`email`='{$subscr_email}')";
                    $sql        .= ' LIMIT 1';
                    $user       = $xoopsDB->query($sql);
                    if ($user) {
                        $row_user   = $xoopsDB->fetchRow($user);
                        $subscr_uid = $row_user[0];
                    }
                    $subscrObj->setVar('subscr_uid', $subscr_uid);
                    // form subscr_submitter
                    $subscrObj->setVar('subscr_submitter', $uid);
                }

                $subscrObj->setVar('subscr_created', time());
                $subscrObj->setVar('subscr_ip', xoops_getenv('REMOTE_ADDR'));
                $subscr_actkey = xoops_makepass();
                $subscrObj->setVar('subscr_actkey', $subscr_actkey);
                // format subscr_actoptions: selected_newsletters||firstname||lastname||sex
                $activationOptions = [
                    'code_selections'  => $code_selections,
                    'subscr_firstname' => $subscr_firstname,
                    'subscr_lastname'  => $subscr_lastname,
                    'subscr_sex'       => $subscr_sex,
                    'subscr_created'   => $subscrObj->getVar('subscr_created'),
                    'subscr_ip'        => $subscrObj->getVar('subscr_ip'),
                ];
                $subscrObj->setVar('subscr_actoptions', $activationOptions); // XOBJ_DTYPE_ARRAY
                // insert subscr
                if (!$helper->getHandler('Subscr')->insert($subscrObj)) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR . '<br>' . $subscrObj->getHtmlErrors());
                }
                if ($subscr_id > 0) {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_UPDATE_OK;
                } else {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_OK;
                }
                $subscr_id = $subscrObj->getVar('subscr_id');
                // send the email with the confirmation code
                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->reset();
                $xoopsMailer->setTemplateDir();
                $xoopsMailer->useMail();
                $xoopsMailer->setTemplate(('update' === $saveType) ? 'update.tpl' : 'activate.tpl');
                $xoopsMailer->setToEmails($subscr_email);
                if (isset($xoopsConfig['adminmail'])) {
                    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                }
                if (isset($xoopsConfig['sitename'])) {
                    $xoopsMailer->setFromName($xoopsConfig['sitename']);
                }
                $xoopsMailer->assign('EMAIL', $subscr_email);
                $xoopsMailer->assign('SEX', '' != $subscrObj->getVar('subscr_sex') ? $subscrObj->getVar('subscr_sex') : $subscr_sex);
                $xoopsMailer->assign('FIRSTNAME', '' != $subscrObj->getVar('subscr_firstname') ? $subscrObj->getVar('subscr_firstname') : $subscr_firstname);
                $xoopsMailer->assign('LASTNAME', '' != $subscrObj->getVar('subscr_lastname') ? $subscrObj->getVar('subscr_lastname') : $subscr_lastname);
                $xoopsMailer->assign('IP', xoops_getenv('REMOTE_ADDR'));
                $act           = [
                    XOOPS_URL,
                    $saveType,
                    $subscr_id,
                    $subscr_actkey,
                    $subscr_email,
                ];
                $activationKey = base64_encode(implode('||', $act));
                $xoopsMailer->assign('ACTLINK', XOOPS_URL . "/modules/xnewsletter/{$currentFile}?actkey={$activationKey}");
                $xoopsMailer->setSubject(_MA_XNEWSLETTER_SUBSCRIPTIONSUBJECT . $GLOBALS['xoopsConfig']['sitename']);
                if (!$xoopsMailer->send()) {
                    $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors();
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors());
                } else {
                    $actionProts_ok[] = str_replace('%subscr_email', $subscr_email, _MA_XNEWSLETTER_SENDMAIL_REG_OK);
                }
            } else {
                // activation key EXISTS
                // check confirmation email
                // check activation key
                $activationKey_array  = explode('||', base64_decode($activationKey, true));
                $activationKeyIsValid = false;
                if ((XOOPS_URL == $activationKey_array[0]) && ('' != trim($activationKey_array[1]))
                    && ((int)$activationKey_array[2] > 0)
                    && ('' != trim($activationKey_array[3]))) {
                    $activationKeyIsValid = true;
                } else {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_INVALIDKEY);
                }
                $saveType      = trim($activationKey_array[1]);
                $subscr_id     = (int)$activationKey_array[2];
                $subscr_actkey = trim($activationKey_array[3]);
                // check given data with table subscr
                $subscrCriteria = new \CriteriaCompo();
                $subscrCriteria->add(new \Criteria('subscr_id', $subscr_id));
                $subscrCriteria->add(new \Criteria('subscr_actkey', $subscr_actkey));
                $subscrCriteria->setLimit(1);
                $subscrCount = $helper->getHandler('Subscr')->getCount($subscrCriteria);
                if (0 == $subscrCount) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NODATAKEY);
                }
                // get subscr
                $subscrObj         = $helper->getHandler('Subscr')->get($subscr_id);
                $activationOptions = $subscrObj->getVar('subscr_actoptions'); // XOBJ_DTYPE_ARRAY
                // check time: confirmation not later than ... hours
                if ((0 != $helper->getConfig('confirmation_time'))
                    && ((int)$activationOptions['subscr_created'] < time() - (3600 + (int)$helper->getConfig('confirmation_time')))) {
                    // time expired
                    $subscrObj->setVar('subscr_actkey', '');
                    $subscrObj->setVar('subscr_actoptions', []);
                    $helper->getHandler('Subscr')->insert($subscrObj);
                    // IN PROGRESS
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_KEYEXPIRED);
                }
                // get subscr fields from subscr_actoptions
                $subscr_sex       = $activationOptions['subscr_sex'];
                $subscr_firstname = $activationOptions['subscr_firstname'];
                $subscr_lastname  = $activationOptions['subscr_lastname'];
                // insert subscr
                $subscrObj->setVar('subscr_sex', $subscr_sex);
                $subscrObj->setVar('subscr_firstname', $subscr_firstname);
                $subscrObj->setVar('subscr_lastname', $subscr_lastname);
                if (!$helper->getHandler('Subscr')->insert($subscrObj)) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR);
                }
                $code_selections = $activationOptions['code_selections']; // string
            }
        }
        //
        //
        // subscribe subscr to cat (create/update catsubscr)
        if ($activationKeyIsValid || $allowedWithoutActivationKey) {
            // update xnewsletter_subscr
            $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
            if (0 == $subscrObj->getVar('subscr_activated')) {
                $subscrObj->setVar('subscr_created', time());
                $subscrObj->setVar('subscr_ip', xoops_getenv('REMOTE_ADDR'));
                $subscrObj->setVar('subscr_activated', 1);
            }
            // reset act fields
            $subscrObj->setVar('subscr_actkey', '');
            $subscrObj->setVar('subscr_actoptions', []);
            // insert subscr
            if (!$helper->getHandler('Subscr')->insert($subscrObj)) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVESUBSCR);
            }
            // create cat subscr
            $selections = explode('|', $code_selections); // array
            foreach ($selections as $code_selection) {
                if ('' == $code_selection) {
                    $code_selection = '0-0-0-0';
                }
                $selection           = explode('-', $code_selection); // array
                $cat_id              = $selection[0];
                $catsubcr            = $selection[1];
                $catsubcr_id_old     = (int)$selection[2];
                $catsubcr_quited_old = (int)$selection[3];
                $catObj              = $helper->getHandler('Cat')->get($cat_id);
                $cat_mailinglist     = $catObj->getVar('cat_mailinglist');
                $cat_name            = $catObj->getVar('cat_name');
                if ('1' == $catsubcr && 0 == $catsubcr_id_old) {
                    $catsubscrObj = $helper->getHandler('Catsubscr')->create();
                    $catsubscrObj->setVar('catsubscr_catid', $cat_id);
                    $catsubscrObj->setVar('catsubscr_subscrid', $subscr_id);
                    $catsubscrObj->setVar('catsubscr_submitter', $uid);
                    $catsubscrObj->setVar('catsubscr_created', time());
                    if ($helper->getHandler('Catsubscr')->insert($catsubscrObj)) {
                        $count_ok++;
                        if ($catsubcr_id_old > 0) {
                            $actionProts_ok[] = str_replace('%nl', $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_NO_CHANGE);
                        } else {
                            $actionProts_ok[] = str_replace('%nl', $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_SUBSCRIBE);
                        }
                        // handle mailinglists
                        if ($cat_mailinglist > 0) {
                            require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                            subscribingMLHandler(_XNEWSLETTER_MAILINGLIST_SUBSCRIBE, $subscr_id, $cat_mailinglist);
                        }
                    } else {
                        $count_err++;
                        $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR; //$catsubscrObj->getHtmlErrors();
                    }
                } elseif ('0' == $catsubcr && $catsubcr_id_old > 0) {
                    // unsubscribe / delete old subscription
                    $catsubscrObj = $helper->getHandler('Catsubscr')->get($catsubcr_id_old);
                    if ($helper->getHandler('Catsubscr')->delete($catsubscrObj, true)) {
                        // handle mailinglists
                        if ($cat_mailinglist > 0) {
                            require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                            subscribingMLHandler(_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE, $subscr_id, $cat_mailinglist);
                        }
                    } else {
                        $count_err++;
                        $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR; //$catsubscrObj->getHtmlErrors();
                    }
                    /*
                                        if ($count_err > 0) {
                                            redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELNOTOK);
                                        }
                    */
                    $actionProts_ok[] = str_replace('%nl', $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_UNSUBSCRIBE);
                } elseif ($catsubcr_id_old > 0 && $catsubcr_quited_old > 0) {
                    // newsletter stay selected, but catsubscr_quited will be removed
                    $catsubscrObj = $helper->getHandler('Catsubscr')->get($catsubcr_id_old);
                    // Form catsubscr_quited
                    $catsubscrObj->setVar('catsubscr_quited', '0');
                    if ($helper->getHandler('Catsubscr')->insert($catsubscrObj)) {
                        $count_ok++;
                        $actionProts_ok[] = str_replace('%nl', $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_DAT_QUITED_REMOVED);
                    } else {
                        $count_err++;
                        $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SAVECATSUBSCR; //$catsubscrObj->getHtmlErrors();
                    }
                } elseif ($catsubcr_id_old > 0) {
                    // newsletter still subscribed
                    $actionProts_ok[] = str_replace('%nl', $cat_name, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_NO_CHANGE);
                }
                // nothing to do
            }
            //
            // send infomail to subscriber if current user (submitter) is not the subscriber (subscr)
            if (isset($submitter_email) && ('' != $submitter_email) && ($submitter_email != $subscr_email)) {
                if ('' == $subscr_sex && '' == $subscr_firstname && '' == $subscr_lastname) {
                    $subscrObj        = $helper->getHandler('Subscr')->get($subscr_id);
                    $subscr_sex       = $subscrObj->getVar('subscr_sex');
                    $subscr_firstname = $subscrObj->getVar('subscr_firstname');
                    $subscr_lastname  = $subscrObj->getVar('subscr_lastname');
                }
                // send the email with the confirmation code
                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->reset();
                $xoopsMailer->setTemplateDir();
                $xoopsMailer->useMail();
                $xoopsMailer->setHTML();
                $xoopsMailer->setTemplate('info_change.tpl');
                $xoopsMailer->setToEmails($subscr_email);
                if (isset($xoopsConfig['adminmail'])) {
                    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                }
                if (isset($xoopsConfig['sitename'])) {
                    $xoopsMailer->setFromName($xoopsConfig['sitename']);
                }
                $xoopsMailer->assign('EMAIL', $subscr_email);
                $xoopsMailer->assign('SEX', $subscr_sex);
                $xoopsMailer->assign('FIRSTNAME', $subscr_firstname);
                $xoopsMailer->assign('LASTNAME', $subscr_lastname);
                $xoopsMailer->assign('IP', xoops_getenv('REMOTE_ADDR'));
                $xoopsMailer->assign('ACTLINK', XOOPS_URL . "/modules/xnewsletter/{$currentFile}?subscr_email={$subscr_email}");
                $xoopsMailer->assign('USERLINK', XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->uid());
                $xoopsMailer->assign('USERNAME', $xoopsUser->name());
                $subject = _MA_XNEWSLETTER_SUBSCRIPTION_SUBJECT_CHANGE . $GLOBALS['xoopsConfig']['sitename'];
                $xoopsMailer->setSubject($subject);
                if (!$xoopsMailer->send()) {
                    $count_err++;
                    $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors();
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors());
                } else {
                    $actionProts_ok[] = str_replace('%e', $subscr_email, _MA_XNEWSLETTER_SUBSCRIPTION_PROT_SENT_INFO);
                }
            }
            if (0 == $count_err) {
                if ('addnew' === $saveType) {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_CLOSED;
                } else {
                    $actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_REG_UPDATE_CLOSED;
                }
                //$actionProts_ok[] = _MA_XNEWSLETTER_SUBSCRIPTION_OK;
            }
        }

        $xoopsTpl->assign('actionProts_ok', $actionProts_ok);
        $xoopsTpl->assign('actionProts_warning', $actionProts_warning);
        $xoopsTpl->assign('actionProts_error', $actionProts_error);
        break;
    case 'delete_subscription':
        if ((!$activationKey && $subscr_id <= 0) && ('1' != $_SESSION['unsub'])) {
            redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOID);
        }
        // IN PROGRESS
        if ('1' == $_SESSION['unsub']) {
            $subscrCriteria = new \CriteriaCompo();
            $subscrCriteria->add(new \Criteria('subscr_email', $subscr_email));
            $subscrCriteria->setLimit(1);
            $subscrCount = $helper->getHandler('Subscr')->getCount($subscrCriteria);
            if (0 == $subscrCount) {
                redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
            }
            $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
            $subscr_id = $subscrObj->getVar('subscr_id');
        }
        // IN PROGRESS

        if (Request::getBool('ok', false, 'POST') || '' != $activationKey) {
            $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_subscription_result.tpl';
            require_once XOOPS_ROOT_PATH . '/header.php';

            $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
            $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
            $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
            // breadcrumb
            $breadcrumb = new Xnewsletter\Breadcrumb();
            $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
            $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, XNEWSLETTER_URL . '/subscription.php?op=list_subscriptions');
            $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIPTION_DELETE, '');
            $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());
            // init vars
            $actionProts_ok       = [];
            $actionProts_warning  = [];
            $actionProts_error    = [];
            $count_ok             = 0;
            $count_err            = 0;
            $activationKeyIsValid = false;
            // check right to unsubscribe directly
            $allowedWithoutActivationKey = false;
            $uid                         = is_object($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
            if (is_object($xoopsUser) && isset($xoopsUser)) {
                // if not anonymous subscriber / subscriber is a Xoops user
                $submitter_email = $xoopsUser->email();
                foreach ($xoopsUser->getGroups() as $group) {
                    if (in_array($group, $helper->getConfig('xn_groups_without_actkey'))
                        || XOOPS_GROUP_ADMIN == $group) {
                        $allowedWithoutActivationKey = true;
                        break;
                    }
                }
            }
            // if anonymous subscriber
            // NOP

            if ('' != $activationKey || $allowedWithoutActivationKey) {
                // 1st case: unsubscribe WITHOUT confirmation
                // 2nd case: unsubscribe WITH confirmation & activation key EXISTS
                // check given data with table subscr
                $subscrCriteria = new \CriteriaCompo();
                $subscrCriteria->add(new \Criteria('subscr_email', $subscr_email));
                $subscrCriteria->add(new \Criteria('subscr_id', $subscr_id));
                // got actkey or user is allowed to delete without actkey
                if ('' != $activationKey) {
                    // check activation key
                    $activationKey_array  = explode('||', base64_decode($activationKey, true));
                    $activationKeyIsValid = false;
                    $subscr_id            = (int)$activationKey_array[1];
                    $subscr_actkey        = trim($activationKey_array[2]);
                    $subscr_email         = trim($activationKey_array[3]);
                    if ((XOOPS_URL == $activationKey_array[0]) && ((int)$activationKey_array[1] > 0)
                        && ('' != trim($activationKey_array[2]))) {
                        $activationKeyIsValid = true;
                    } else {
                        redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_INVALIDKEY);
                    }
                    $subscrCriteria->add(new \Criteria('subscr_actkey', $subscr_actkey));
                }
                $subscrCriteria->setLimit(1);
                $subscrCount = $helper->getHandler('Subscr')->getCount($subscrCriteria);
                if (0 == $subscrCount) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
                }
                $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
                // delete subscriber (subscr), subscriptions (catsubscrs) and mailinglist
                if (!$helper->getHandler('Subscr')->delete($subscrObj, true)) {
                    $actionProts_error[] = $subscrObj->getHtmlErrors();
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR . $subscrObj->getHtmlErrors());
                }
                if (0 == $count_err) {
                    $actionProts_ok[] = _AM_XNEWSLETTER_FORMDELOK;
                }
            } else {
                // 2nd case: unsubscribe WITH confirmation & activation key DOESN'T EXIST
                // check form
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('subscr.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                $subscrObj     = $helper->getHandler('Subscr')->get($subscr_id);
                $subscr_actkey = xoops_makepass();
                $subscrObj->setVar('subscr_actkey', $subscr_actkey);
                // insert subscr
                if (!$helper->getHandler('Subscr')->insert($subscrObj)) {
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR);
                }
                //                if (!$allowedWithoutActivationKey) {
                $xoopsMailer = xoops_getMailer();
                $xoopsMailer->reset();
                $xoopsMailer->setTemplateDir();
                $xoopsMailer->useMail();
                $xoopsMailer->setTemplate('delete.tpl');
                $xoopsMailer->setToEmails($subscrObj->getVar('subscr_email'));
                if (isset($xoopsConfig['adminmail'])) {
                    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                }
                if (isset($xoopsConfig['sitename'])) {
                    $xoopsMailer->setFromName($xoopsConfig['sitename']);
                }
                $xoopsMailer->assign('EMAIL', $subscrObj->getVar('subscr_email'));
                $xoopsMailer->assign('SEX', $subscrObj->getVar('subscr_sex'));
                $xoopsMailer->assign('FIRSTNAME', $subscrObj->getVar('subscr_firstname'));
                $xoopsMailer->assign('LASTNAME', $subscrObj->getVar('subscr_lastname'));
                $xoopsMailer->assign('IP', xoops_getenv('REMOTE_ADDR'));
                $act           = [
                    XOOPS_URL,
                    $subscrObj->getVar('subscr_id'),
                    $subscrObj->getVar('subscr_actkey'),
                    $subscrObj->getVar('subscr_email'),
                ];
                $activationKey = base64_encode(implode('||', $act));
                $xoopsMailer->assign('ACTLINK', XOOPS_URL . "/modules/xnewsletter/{$currentFile}?op=unsub&email={$subscrObj->getVar('subscr_email')}&actkey={$activationKey}");
                $xoopsMailer->setSubject(_MA_XNEWSLETTER_DELETESUBJECT . $GLOBALS['xoopsConfig']['sitename']);
                if (!$xoopsMailer->send()) {
                    $count_err++;
                    $actionProts_error[] = _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors();
                    redirect_header($currentFile, 3, _MA_XNEWSLETTER_SUBSCRIPTION_ERROR_SENDACTKEY . '<br>' . $xoopsMailer->getErrors());
                }
                //                }
                if (0 == $count_err) {
                    $actionProts_ok[] = str_replace('%subscr_email', $subscrObj->getVar('subscr_email'), _MA_XNEWSLETTER_SENDMAIL_UNREG_OK);
                }
            }

            $xoopsTpl->assign('actionProts_ok', $actionProts_ok);
            $xoopsTpl->assign('actionProts_warning', $actionProts_warning);
            $xoopsTpl->assign('actionProts_error', $actionProts_error);
        } else {
            $GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_empty.tpl';
            require_once XOOPS_ROOT_PATH . '/header.php';

            $xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
            $xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
            $xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
            // breadcrumb
            $breadcrumb = new Xnewsletter\Breadcrumb();
            $breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
            $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIBE, XNEWSLETTER_URL . '/subscription.php?op=list_subscriptions');
            $breadcrumb->addLink(_MD_XNEWSLETTER_SUBSCRIPTION_DELETE, '');
            $xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

            $subscrObj = $helper->getHandler('Subscr')->get($subscr_id);
            xoops_confirm([
                              'ok'           => true,
                              'subscr_id'    => $subscr_id,
                              'subscr_email' => $subscr_email,
                              'op'           => 'delete_subscription',
                          ], $currentFile, sprintf(_MA_XNEWSLETTER_SUBSCRIPTION_DELETE_SURE));
        }
        break;
}

require_once __DIR__ . '/footer.php';
