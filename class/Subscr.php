<?php

namespace XoopsModules\Xnewsletter;

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

//use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Subscr
 */
class Subscr extends \XoopsObject
{
    public $helper = null;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('subscr_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('subscr_email', XOBJ_DTYPE_TXTBOX, '', false, 100);
        $this->initVar('subscr_firstname', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('subscr_lastname', XOBJ_DTYPE_TXTBOX, '', false, 100);
        $this->initVar('subscr_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('subscr_sex', XOBJ_DTYPE_TXTBOX, '', false, 100);
        $this->initVar('subscr_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('subscr_created', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('subscr_actkey', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('subscr_ip', XOBJ_DTYPE_TXTBOX, xoops_getenv('REMOTE_ADDR'), false, 32);
        $this->initVar('subscr_activated', XOBJ_DTYPE_INT, 0, false);  // IN PROGRESS: should be false or timestamp
        $this->initVar('subscr_actoptions', XOBJ_DTYPE_ARRAY, [], false);
    }

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getSearchForm($action = false)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm(_MA_XNEWSLETTER_SUBSCRIPTION_SEARCH, 'formsearch', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        // subscr_email
        $email_field = new \XoopsFormText(_MA_XNEWSLETTER_SUBSCRIPTION_SEARCH_EMAIL, 'subscr_email', 50, 100, $this->getVar('subscr_email'));
        if ('' != $this->getVar('subscr_email')) {
            //$email_field->setExtra('disabled="disabled"');
        }
        $form->addElement($email_field, true);

        // captcha
        xoops_load('xoopscaptcha');
        $form->addElement(new \XoopsFormCaptcha('', 'xoopscaptcha', true));

        // op
        $form->addElement(new \XoopsFormHidden('op', 'list_subscriptions'));

        // buttons
        $buttonTray = new \XoopsFormElementTray('', '');
        $buttonTray->addElement(new \XoopsFormButton('', 'submit', _AM_XNEWSLETTER_SUBSCRIPTION_SEARCH_ADD, 'submit'));
        $button_reset = new \XoopsFormButton('', '', _RESET, 'reset');
        $buttonTray->addElement($button_reset);
        $button_cancel = new \XoopsFormButton('', '', _CANCEL, 'button');
        $button_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($button_cancel);
        $form->addElement($buttonTray);

        return $form;
    }

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB, $xoopsUser;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $title = $this->isNew() ? sprintf(_MA_XNEWSLETTER_SUBSCRIPTION_ADD) : sprintf(_MA_XNEWSLETTER_SUBSCRIPTION_EDIT);
        $form  = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new \XoopsFormLabel("<span style='text-decoration:underline'>" . _MA_XNEWSLETTER_SUBSCRIPTION_INFO_PERS . '</span>', ''));
        $subscr_id = $this->isNew() ? 0 : $this->getVar('subscr_id');

        // subscr_email
        if ($subscr_id > 0 || '' != $this->getVar('subscr_email')) {
            $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_EMAIL, $this->getVar('subscr_email')));
            $form->addElement(new \XoopsFormHidden('subscr_email', $this->getVar('subscr_email')));
        } else {
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, 'subscr_email', 50, 255, $this->getVar('subscr_email')), true);
        }

        // subscr_sex
        if (1 == $this->helper->getConfig('xn_use_salutation')) {
            $select_subscr_sex = new \XoopsFormSelect(_AM_XNEWSLETTER_SUBSCR_SEX, 'subscr_sex', $this->getVar('subscr_sex'));
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_EMPTY, _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_MALE, _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_COMP, _AM_XNEWSLETTER_SUBSCR_SEX_COMP);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FAMILY, _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY);
            $form->addElement($select_subscr_sex);
        }

        // subscr_firstname
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_FIRSTNAME, 'subscr_firstname', 50, 255, $this->getVar('subscr_firstname')), true);

        // subscr_lastname
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_LASTNAME, 'subscr_lastname', 50, 255, $this->getVar('subscr_lastname')), false);

        $form->addElement(new \XoopsFormLabel('<br><br>', ''));

        // get newsletters available for current user
        $opt_cat  = [];
        $opt_tray = new \XoopsFormElementTray("<span style='text-decoration:underline'>" . _MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL . '</span>', '<br>');
        $opt_tray->setDescription(_MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL_DESC);
        $grouppermHandler = xoops_getHandler('groupperm');
        $uid              = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
        $groups           = is_object($xoopsUser) ? $xoopsUser->getGroups() : [0 => XOOPS_GROUP_ANONYMOUS];

        // cats[], existing_catsubcr_id_{$cat_id}, existing_catsubscr_quited_{$cat_id}
        $catCriteria = new \CriteriaCompo();
        $catCriteria->setSort('cat_id');
        $catCriteria->setOrder('ASC');
        $catObjs      = $this->helper->getHandler('cat')->getAll($catCriteria);
        $cat_checkbox = new \XoopsFormCheckBox(_MA_XNEWSLETTER_SUBSCRIPTION_SELECT_CATS, 'cats', null, '<br>');
        $cat_checkbox->setDescription(_MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL_DESC);
        $values = [];
        foreach ($catObjs as $cat_id => $catObj) {
            // if anonymous user or Xoops user can read cat...
            if ($grouppermHandler->checkRight('newsletter_read_cat', $cat_id, XOOPS_GROUP_ANONYMOUS, $this->helper->getModule()->mid())
                || $grouppermHandler->checkRight('newsletter_read_cat', $cat_id, $groups, $this->helper->getModule()->mid())) {
                // get existing catsubscr
                $catsubscrCriteria = new \CriteriaCompo();
                $catsubscrCriteria->add(new \Criteria('catsubscr_catid', $cat_id));
                $catsubscrCriteria->add(new \Criteria('catsubscr_subscrid', $subscr_id));
                $catsubscrCriteria->setLimit(1);
                $catsubscrObjs = $this->helper->getHandler('catsubscr')->getObjects($catsubscrCriteria);
                if (isset($catsubscrObjs[0])) {
                    $values[]         = $cat_id;
                    $catsubscr_quited = $catsubscrObjs[0]->getVar('catsubscr_quited');
                    $catsubscr_id     = $catsubscrObjs[0]->getVar('catsubscr_id');
                } else {
                    $catsubscr_quited = 0;
                    $catsubscr_id     = 0;
                }
                $name = $catObj->getVar('cat_name');
                $name .= '<div>' . $catObj->getVar('cat_info', 's') . '</div>';
                if (0 == $catsubscr_quited) {
                    // NOP
                } else {
                    $name .= '<div>';
                    $name .= str_replace('%q', formatTimestamp($catsubscr_quited, $this->helper->getConfig('dateformat')), _MA_XNEWSLETTER_SUBSCRIPTION_QUITED_DETAIL);
                    $name .= '</div>';
                }
                $name .= "<div style='clear:both'></div>";
                $cat_checkbox->addOption($cat_id, $name);
                $form->addElement(new \XoopsFormHidden("existing_catsubcr_id_{$cat_id}", $catsubscr_id));
                $form->addElement(new \XoopsFormHidden("existing_catsubscr_quited_{$cat_id}", $catsubscr_quited));
            }
        }
        $cat_checkbox->setValue($values);
        $form->addElement($cat_checkbox);

        // op
        $form->addElement(new \XoopsFormHidden('op', 'save_subscription'));

        // button
        $buttonTray = new \XoopsFormElementTray('', '');
        $buttonTray->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $button_reset = new \XoopsFormButton('', '', _RESET, 'reset');
        $buttonTray->addElement($button_reset);
        $button_cancel = new \XoopsFormButton('', '', _CANCEL, 'button');
        $button_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($button_cancel);
        $form->addElement($buttonTray);

        return $form;
    }

    //**********************************************************************************************
    //   form for admin aerea    *******************************************************************
    //**********************************************************************************************

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getFormAdmin($action = false)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_SUBSCR_ADD) : sprintf(_AM_XNEWSLETTER_SUBSCR_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, 'subscr_email', 50, 255, $this->getVar('subscr_email')), true);
        $select_subscr_sex = new \XoopsFormSelect(_AM_XNEWSLETTER_SUBSCR_SEX, 'subscr_sex', $this->getVar('subscr_sex'));
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_EMPTY, _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_MALE, _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_COMP, _AM_XNEWSLETTER_SUBSCR_SEX_COMP);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FAMILY, _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY);
        $form->addElement($select_subscr_sex);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_FIRSTNAME, 'subscr_firstname', 50, 255, $this->getVar('subscr_firstname')), false);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_SUBSCR_LASTNAME, 'subscr_lastname', 50, 255, $this->getVar('subscr_lastname')), false);

        $form->addElement(new \XoopsFormSelectUser(_AM_XNEWSLETTER_SUBSCR_UID, 'subscr_uid', true, $this->getVar('subscr_uid'), 1, false), false);

        $form->addElement(new \XoopsFormHidden('subscr_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        //$form->addElement(new \XoopsFormSelectUser(_AM_XNEWSLETTER_SUBSCR_SUBMITTER, 'subscr_submitter', false, $this->getVar('subscr_submitter'), 1, false), true);

        if ($this->getVar('subscr_id') > 0) {
            $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_CREATED, formatTimestamp($this->getVar('subscr_created'), $this->helper->getConfig('dateformat')) . ' [' . $this->getVar('subscr_ip') . ']'));
            $form->addElement(new \XoopsFormHidden('subscr_created', $this->getVar('subscr_created')));
            $form->addElement(new \XoopsFormHidden('subscr_ip', $this->getVar('subscr_ip')));
        } else {
            $time = time();
            $ip   = xoops_getenv('REMOTE_ADDR');
            $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_CREATED, formatTimestamp($time, 's') . " [{$ip}]"));
            $form->addElement(new \XoopsFormHidden('subscr_created', $time));
            $form->addElement(new \XoopsFormHidden('subscr_ip', $ip));
        }
        $form->addElement(new \XoopsFormRadioYN(_AM_XNEWSLETTER_SUBSCR_ACTIVATED, 'subscr_activated', $this->getVar('subscr_activated')));
        $form->addElement(new \XoopsFormHidden('subscr_actkey', ''));
        $form->addElement(new \XoopsFormHidden('op', 'save_subscr'));
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}
