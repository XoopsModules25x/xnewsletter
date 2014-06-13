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
 * Class XnewsletterAttachment
 */
class XnewsletterAttachment extends XoopsObject
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
        $this->initVar("attachment_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("attachment_letter_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("attachment_name", XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar("attachment_type", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("attachment_submitter", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("attachment_created", XOBJ_DTYPE_INT, null, false, 10);
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_ATTACHMENT_ADD) : sprintf(_AM_XNEWSLETTER_ATTACHMENT_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letter_select = new XoopsFormSelect(_AM_XNEWSLETTER_PROTOCOL_LETTER_ID, "attachment_letter_id", $this->getVar("attachment_letter_id"));
        $letter_select->addOptionArray($this->xnewsletter->getHandler('letter')->getList($letterCriteria));
        $form->addElement($letter_select, true);

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_ATTACHMENT_NAME, "attachment_name", 50, 255, $this->getVar("attachment_name")), true);

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_ATTACHMENT_TYPE, "attachment_type", 50, 255, $this->getVar("attachment_type")), false);

        $time = ($this->isNew()) ? time() : $this->getVar("attachment_created");
        $form->addElement(new XoopsFormHidden("attachment_submitter", $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormHidden("attachment_created", $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_ATTACHMENT_SUBMITTER, "attachment_submitter", false, $this->getVar("attachment_submitter"), 1, false), true);
        //$form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_ATTACHMENT_CREATED, "attachment_created", "", $this->getVar("attachment_created")));

        $form->addElement(new XoopsFormHidden("op", "save_attachment"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }
}

/**
 * Class XnewsletterAttachmentHandler
 */
class XnewsletterAttachmentHandler extends XoopsPersistableObjectHandler
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
        parent::__construct($db, "xnewsletter_attachment", "XnewsletterAttachment", "attachment_id", "attachment_letter_id");
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
