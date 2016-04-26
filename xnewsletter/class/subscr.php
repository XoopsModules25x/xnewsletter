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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';

/**
 * Class XnewsletterSubscr
 */
class XnewsletterSubscr extends XoopsObject
{
    public $xnewsletter = null;

    //Constructor
    /**
     *
     */
    public function __construct()
    {
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar("subscr_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("subscr_email", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("subscr_firstname", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("subscr_lastname", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("subscr_uid", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("subscr_sex", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("subscr_submitter", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("subscr_created", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("subscr_actkey", XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar("subscr_ip", XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar("subscr_activated", XOBJ_DTYPE_TXTBOX, null, false, 8);
        $this->initVar("subscr_actoptions", XOBJ_DTYPE_TXTBOX, null, false, 500);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getSearchForm($action = false)
    {
        global $xoopsDB;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm(_MA_XNEWSLETTER_SUBSCRIPTION_SEARCH, "formsearch", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $email_tray  = new XoopsFormElementTray(_MA_XNEWSLETTER_SUBSCRIPTION_SEARCH_EMAIL, '&nbsp;&nbsp;');
        $email_field = new XoopsFormText("", "subscr_email", 50, 255, $this->getVar("subscr_email"));
        if ($this->getVar("subscr_email") != "") {
            $email_field->setExtra('disabled="disabled"');
        }
        $email_tray->addElement($email_field, false);
        if ($this->getVar("subscr_email") == "") {
            $email_tray->addElement(new XoopsFormCaptcha('<br /><br />'), true);
            $email_tray->addElement(new XoopsFormButton("<br /><br />", "submit", _AM_XNEWSLETTER_SUBSCRIPTION_SEARCH_ADD, "submit"));
        }
        $form->addElement($email_tray);

        $form->addElement(new XoopsFormHidden("op", "exec_search"));
        //$form->addElement(new XoopsFormButton(_MA_XNEWSLETTER_SUBSCRIPTION_ADDNEW_EMAIL, "addnew", _ADD, "submit"));
        return $form;
    }

    //**********************************************************************************************
    //    form for user area    *******************************************************************
    //**********************************************************************************************
    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB, $xoopsUser;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        $title = $this->isNew() ? sprintf(_MA_XNEWSLETTER_SUBSCRIPTION_ADD) : sprintf(_MA_XNEWSLETTER_SUBSCRIPTION_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormLabel("<span style='text-decoration:underline'>" . _MA_XNEWSLETTER_SUBSCRIPTION_INFO_PERS . "</span>", ""));
        $subscr_id = $this->isNew() ? 0 : $this->getVar("subscr_id");
        if ($subscr_id > 0 || $this->getVar("subscr_email") != "") {
            $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_EMAIL, $this->getVar("subscr_email")));
            $form->addElement(new XoopsFormHidden("subscr_email", $this->getVar("subscr_email")));
        } else {
            $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, "subscr_email", 50, 255, $this->getVar("subscr_email")), true);
        }
        if ($this->xnewsletter->getConfig('xn_use_salutation') == 1) {
            $select_subscr_sex = new XoopsFormSelect(_AM_XNEWSLETTER_SUBSCR_SEX, "subscr_sex", $this->getVar("subscr_sex"));
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_EMPTY, _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_GIRL);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_BOY);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_MALE, _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_DOCTOR);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_COMP, _AM_XNEWSLETTER_SUBSCR_SEX_COMP);
            $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FAMILY, _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY);
            $form->addElement($select_subscr_sex);
        }
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_FIRSTNAME, "subscr_firstname", 50, 255, $this->getVar("subscr_firstname")), false);

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_LASTNAME, "subscr_lastname", 50, 255, $this->getVar("subscr_lastname")), false);

        $form->addElement(new XoopsFormLabel("<br/><br/>", ""));

        $opt_cat  = array();
        $opt_tray = new XoopsFormElementTray("<span style='text-decoration:underline'>" . _MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL . "</span>", "<br />");

        //get newsletters available for current user
        $gperm_handler  =& xoops_gethandler('groupperm');
        $member_handler =& xoops_gethandler('member');
        $currentuid     = (is_object($xoopsUser) && isset($xoopsUser)) ? $xoopsUser->uid() : 0;
        if ($currentuid == 0) {
            $my_group_ids = array(XOOPS_GROUP_ANONYMOUS);
        } else {
            $my_group_ids = $member_handler->getGroupsByUser($currentuid);
        }

        $catCriteria = new CriteriaCompo();
        $catCriteria->setSort('cat_id');
        $catCriteria->setOrder('ASC');
        $catObjs          = $this->xnewsletter->getHandler('cat')->getAll($catCriteria);
        $count_cats_avail = 0;
        foreach ($catObjs as $cat_id => $catObj) {
            //first check group anonymous
            $show = $gperm_handler->checkRight('newsletter_read_cat', $cat_id, XOOPS_GROUP_ANONYMOUS, $this->xnewsletter->getModule()->mid());
            if ($show == 0) {
                $show = $gperm_handler->checkRight('newsletter_read_cat', $cat_id, $my_group_ids, $this->xnewsletter->getModule()->mid());
            }
            if ($show == 1) {
                ++$count_cats_avail;
                $cat_name = $catObj->getVar("cat_name");
                //get subscription of current cat and current user
                $catsubscr        = 0;
                $catsubscr_id     = 0;
                $catsubscr_quited = 0;

                $catsubscrCriteria = new CriteriaCompo();
                $catsubscrCriteria->add(new Criteria('catsubscr_catid', $cat_id));
                $catsubscrCriteria->add(new Criteria('catsubscr_subscrid', $subscr_id));
                $catsubscrObjs = $this->xnewsletter->getHandler('catsubscr')->getAll($catsubscrCriteria);
                foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                    $catsubscr_quited = $catsubscrObj->getVar("catsubscr_quited");
                }

                if ($catsubscr_quited > 0) {
                    $dat_catsubscr_quited = formatTimeStamp($catsubscr_quited, "M");
                    $cat_name .= "<div style='padding-left:20px;padding-top:0;padding-bottom:0'>";
                    $cat_name .= str_replace("%q", $dat_catsubscr_quited, _MA_XNEWSLETTER_SUBSCRIPTION_QUITED_DETAIL);
                    $cat_name .= "</div>";
                } else {
                    $catsubscr_quited = 0;
                }
                $cat_info = "<div style='padding-left:20px;padding-top:10px'>";
                $cat_info .= $catObj->getVar("cat_info");
                $cat_info .= "</div>";
                $opt_cat[$cat_id] = new XoopsFormCheckBox('', "letter_cats_" . $cat_id, $catsubscr_id > 0);
                $opt_cat[$cat_id]->addOption($cat_id, $cat_name);
                $opt_tray->addElement($opt_cat[$cat_id]);
                $opt_tray->addElement(new XoopsFormLabel($cat_info, ''));
                $form->addElement(new XoopsFormHidden("letter_cats_old_catsubcr_id_" . $cat_id, $catsubscr_id));
                $form->addElement(new XoopsFormHidden("letter_cats_old_catsubscr_quited_" . $cat_id, $catsubscr_quited));
            }
        }

        if ($count_cats_avail == 0) {
            $form->addElement(new XoopsFormLabel(_MA_XNEWSLETTER_SUBSCRIPTION_CATS_AVAIL, _MA_XNEWSLETTER_SUBSCRIPTION_NO_CATS_AVAIL));
        } else {
            $form->addElement($opt_tray);
        }

        $form->addElement(new XoopsFormHidden("op", "save_subscription"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }

    //**********************************************************************************************
    //   form for admin aerea    *******************************************************************
    //**********************************************************************************************
    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getFormAdmin($action = false)
    {
        global $xoopsDB;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_SUBSCR_ADD) : sprintf(_AM_XNEWSLETTER_SUBSCR_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_EMAIL, "subscr_email", 50, 255, $this->getVar("subscr_email")), true);
        $select_subscr_sex = new XoopsFormSelect(_AM_XNEWSLETTER_SUBSCR_SEX, "subscr_sex", $this->getVar("subscr_sex"));
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_EMPTY, _AM_XNEWSLETTER_SUBSCR_SEX_EMPTY);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_GIRL, _AM_XNEWSLETTER_SUBSCR_SEX_GIRL);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE, _AM_XNEWSLETTER_SUBSCR_SEX_FEMALE);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_BOY, _AM_XNEWSLETTER_SUBSCR_SEX_BOY);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_MALE, _AM_XNEWSLETTER_SUBSCR_SEX_MALE);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_DOCTOR, _AM_XNEWSLETTER_SUBSCR_SEX_DOCTOR);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_COMP, _AM_XNEWSLETTER_SUBSCR_SEX_COMP);
        $select_subscr_sex->addOption(_AM_XNEWSLETTER_SUBSCR_SEX_FAMILY, _AM_XNEWSLETTER_SUBSCR_SEX_FAMILY);
        $form->addElement($select_subscr_sex);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_FIRSTNAME, "subscr_firstname", 50, 255, $this->getVar("subscr_firstname")), false);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_SUBSCR_LASTNAME, "subscr_lastname", 50, 255, $this->getVar("subscr_lastname")), false);

        $form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_SUBSCR_UID, "subscr_uid", true, $this->getVar("subscr_uid"), 1, false), false);

        $form->addElement(new XoopsFormHidden('subscr_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_SUBSCR_SUBMITTER, "subscr_submitter", false, $this->getVar("subscr_submitter"), 1, false), true);

        if ($this->getVar("subscr_id") > 0) {
            $form->addElement(
                new XoopsFormLabel(
                    _AM_XNEWSLETTER_SUBSCR_CREATED,
                    formatTimestamp($this->getVar("subscr_created"), $this->xnewsletter->getConfig('dateformat')) . " [" . $this->getVar("subscr_ip") . "]"
                )
            );
            $form->addElement(new XoopsFormHidden('subscr_created', $this->getVar("subscr_created")));
            $form->addElement(new XoopsFormHidden('subscr_ip', $this->getVar("subscr_ip")));
        } else {
            $time = time();
            $ip   = xoops_getenv("REMOTE_ADDR");
            $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_SUBSCR_CREATED, formatTimestamp($time, 's') . " [" . $ip . "]"));
            $form->addElement(new XoopsFormHidden('subscr_created', $time));
            $form->addElement(new XoopsFormHidden('subscr_ip', $ip));
        }
        $form->addElement(new XoopsFormRadioYN(_AM_XNEWSLETTER_SUBSCR_ACTIVATED, 'subscr_activated', $this->getVar("subscr_activated")));
        $form->addElement(new XoopsFormHidden('subscr_actkey', ""));
        $form->addElement(new XoopsFormHidden("op", "save_subscr"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }
}

/**
 * Class XnewsletterSubscrHandler
 */
class XnewsletterSubscrHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var xnewsletterxnewsletter
     * @access public
     */
    public $xnewsletter = null;

    /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, "xnewsletter_subscr", "XnewsletterSubscr", "subscr_id", "subscr_email");
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
