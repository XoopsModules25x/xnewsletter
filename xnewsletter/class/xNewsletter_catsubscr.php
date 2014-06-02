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
include_once dirname(dirname(__FILE__)) . '/include/common.php';
class xnewsletter_catsubscr extends XoopsObject
{
    public $xnewsletter = null;

    //Constructor
    public function __construct()
    {
        $this->xnewsletter = xNewsletterxNewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar("catsubscr_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("catsubscr_catid", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("catsubscr_subscrid", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("catsubscr_quited", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("catsubscr_submitter", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("catsubscr_created", XOBJ_DTYPE_INT, null, false, 10);
    }

    public function getForm($action = false)
    {
        global $xoopsDB;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_CATSUBSCR_ADD) : sprintf(_AM_XNEWSLETTER_CATSUBSCR_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $criteria = new CriteriaCompo();
        $criteria->setSort('cat_id ASC, cat_name');
        $criteria->setOrder('ASC');
        $cat_select = new XoopsFormSelect(_AM_XNEWSLETTER_CATSUBSCR_CATID, "catsubscr_catid", $this->getVar("catsubscr_catid"));
        $cat_select->addOptionArray($this->xnewsletter->getHandler('xNewsletter_cat')->getList());
        $form->addElement($cat_select, true);

        $criteria = new CriteriaCompo();
        $criteria->setSort('subscr_email ');
        $criteria->setOrder('ASC');
        $subscr_select = new XoopsFormSelect(_AM_XNEWSLETTER_CATSUBSCR_SUBSCRID, "catsubscr_subscrid", $this->getVar("catsubscr_subscrid"));
        $subscr_select->addOptionArray($this->xnewsletter->getHandler('xNewsletter_subscr')->getList());
        $form->addElement($subscr_select, true);

        $quited_tray = new XoopsFormElementTray(_AM_XNEWSLETTER_CATSUBSCR_QUITED, "&nbsp;");

        $quit_now = new XoopsFormRadio("", "catsubscr_quit_now",0);
        $quit_now->addOption(0, _AM_XNEWSLETTER_CATSUBSCR_QUIT_NONE);
        $quit_now->addOption(1, _AM_XNEWSLETTER_CATSUBSCR_QUIT_NOW);
        $quit_now->addOption(2, _AM_XNEWSLETTER_CATSUBSCR_QUIT_REMOVE);
        $quited_tray->addElement($quit_now, false);
        $quited_tray->addElement(new XoopsFormLabel("", $this->getVar("catsubscr_quited")));
        $form->addElement($quited_tray, false);

        $time = ($this->isNew()) ? time() : $this->getVar("catsubscr_created");
        $form->addElement(new XoopsFormHidden("catsubscr_submitter", $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormHidden("catsubscr_created", $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_CATSUBSCR_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_CATSUBSCR_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_CATSUBSCR_SUBMITTER, "catsubscr_submitter", false, $this->getVar("catsubscr_submitter"), 1, false), true);
        //$form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_CATSUBSCR_CREATED, "catsubscr_created", "", $this->getVar("catsubscr_created")));

        $form->addElement(new XoopsFormHidden("op", "save_catsubscr"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }
}

class xNewsletterxnewsletter_catsubscrHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var xNewsletterxNewsletter
     * @access public
     */
    public $xnewsletter = null;

    /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, "mod_xnewsletter_catsubscr", "xnewsletter_catsubscr", "catsubscr_id", "catsubscr_catid");
        $this->xnewsletter = xNewsletterxNewsletter::getInstance();
    }
}
