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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';

/**
 * Class XnewsletterBmh
 */
class XnewsletterBmh extends XoopsObject
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
        $this->initVar("bmh_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("bmh_rule_no", XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar("bmh_rule_cat", XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar("bmh_bouncetype", XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar("bmh_remove", XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar("bmh_email", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("bmh_subject", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("bmh_measure", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("bmh_submitter", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("bmh_created", XOBJ_DTYPE_INT, null, false, 10);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_BMH_ADD) : sprintf(_AM_XNEWSLETTER_BMH_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $account_default = $this->getVar("bmh_accounts_id");
        $crit_accounts   = new CriteriaCompo();
        $crit_accounts->setSort("accounts_id");
        $crit_accounts->setOrder("ASC");
        $opt_accounts = new XoopsFormSelect(_AM_XNEWSLETTER_BMH_ACCOUNTS_ID, "bmh_accounts_id", $account_default);
        $opt_accounts->addOptionArray($this->xnewsletter->getHandler('accounts')->getList($crit_accounts));
        $form->addElement($opt_accounts, false);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_BMH_RULE_NO, "bmh_rule_no", 50, 255, $this->getVar("bmh_rule_no")), true);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_BMH_RULE_CAT, "bmh_rule_cat", 50, 255, $this->getVar("bmh_rule_cat")), true);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_BMH_BOUNCETYPE, "bmh_bouncetype", 50, 255, $this->getVar("bmh_bouncetype")), true);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_BMH_REMOVE, "bmh_remove", 50, 255, $this->getVar("bmh_remove")), true);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_BMH_EMAIL, "bmh_email", 50, 255, $this->getVar("bmh_email")), true);
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_BMH_SUBJECT, "bmh_subject", 50, 255, $this->getVar("bmh_subject")), false);

        $measure_select = new XoopsFormSelect(_AM_XNEWSLETTER_BMH_MEASURE, "bmh_measure", $this->getVar("bmh_measure"));
        $measure_select->addOption(_AM_XNEWSLETTER_BMH_MEASURE_VAL_PENDING, _AM_XNEWSLETTER_BMH_MEASURE_PENDING);
        $measure_select->addOption(_AM_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING, _AM_XNEWSLETTER_BMH_MEASURE_NOTHING);
        $measure_select->addOption(_AM_XNEWSLETTER_BMH_MEASURE_VAL_QUIT, _AM_XNEWSLETTER_BMH_MEASURE_QUIT);
        $form->addElement($measure_select, true);

        $time = ($this->isNew()) ? time() : $this->getVar("bmh_created");
        $form->addElement(new XoopsFormHidden("bmh_submitter", $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormHidden("bmh_created", $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_BMH_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_BMH_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_BMH_SUBMITTER, "bmh_submitter", false, $this->getVar("bmh_submitter"), 1, false), true);
        //$form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_BMH_CREATED, "bmh_created", "", $this->getVar("bmh_created")));

        $form->addElement(new XoopsFormHidden("op", "save_bmh"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }
}

/**
 * Class XnewsletterBmh
 */
class XnewsletterBmhHandler extends XoopsPersistableObjectHandler
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
        parent::__construct($db, "xnewsletter_bmh", "XnewsletterBmh", "bmh_id", "bmh_rule_no");
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
