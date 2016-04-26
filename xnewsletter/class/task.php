<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 * xnewsletter module for xoops
 *
 * @copyright       The TXMod XOOPS Project http://sourceforge.net/projects/thmod/
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GPL 2.0 or later
 * @package         xnewsletter
 * @since           2.5.x
 * @author          XOOPS Development Team ( name@site.com ) - ( http://xoops.org )
 * @version         $Id: xnewsletter_task.php 12559 2014-06-02 08:10:39Z beckmi $
 */

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class XnewsletterTask
 */
class XnewsletterTask extends XoopsObject
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
        $this->XoopsObject();
        $this->initVar("task_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("task_letter_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("task_subscr_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("task_starttime", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("task_submitter", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("task_created", XOBJ_DTYPE_INT, null, false, 8);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    function getForm($action = false)
    {
        global $xoopsDB;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_TASK_ADD) : sprintf(_AM_XNEWSLETTER_TASK_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");

        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letter_select = new XoopsFormSelect(_AM_XNEWSLETTER_TASK_LETTER_ID, "task_letter_id", $this->getVar("task_letter_id"));
        $letter_select->addOptionArray($this->xnewsletter->getHandler('letter')->getList($letterCriteria));
        $form->addElement($letter_select, true);

        $subscrCriteria = new CriteriaCompo();
        $subscrCriteria->setSort('subscr_id');
        $subscrCriteria->setOrder('ASC');
        $subscr_select = new XoopsFormSelect(_AM_XNEWSLETTER_TASK_SUBSCR_ID, "task_subscr_id", $this->getVar("task_subscr_id"));
        $subscr_select->addOptionArray($this->xnewsletter->getHandler('subscr')->getList($subscrCriteria));
        $form->addElement($subscr_select, true);

        $form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_TASK_STARTTIME, "task_starttime", "", $this->getVar("task_starttime")));

        $form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_TASK_SUBMITTER, "task_submitter", false, $this->getVar("task_submitter"), 1, false), true);

        $form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_TASK_CREATED, "task_created", "", $this->getVar("task_created")));

        $form->addElement(new XoopsFormHidden("op", "save_task"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }
}

/**
 * Class XnewsletterTaskHandler
 */
class XnewsletterTaskHandler extends XoopsPersistableObjectHandler
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
        parent::__construct($db, "xnewsletter_task", "XnewsletterTask", "task_id", "task_letter_id");
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
