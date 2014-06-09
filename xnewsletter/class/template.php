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
 * Class XnewsletterTemplate
 */
class XnewsletterTemplate extends XoopsObject
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
        $this->initVar("template_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("template_title", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("template_description", XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar("template_content", XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar("template_submitter", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("template_created", XOBJ_DTYPE_INT, null, false, 10);
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_TEMPLATE_ADD) : sprintf(_AM_XNEWSLETTER_TEMPLATE_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_TEMPLATE_TITLE, "template_title", 50, 255, $this->getVar("template_title", 'e')), true);

        $editor_configs           = array();
        $editor_configs["name"]   = "template_description";
        $editor_configs["value"]  = $this->getVar("template_description", "e");
        $editor_configs["rows"]   = 10;
        $editor_configs["cols"]   = 80;
        $editor_configs["width"]  = "100%";
        $editor_configs["height"] = "400px";
        $editor_configs["editor"] = $this->xnewsletter->getConfig('xnewsletter_editor');
        $template_description_editor = new XoopsFormEditor(_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION, "template_description", $editor_configs);
        $template_description_editor->setDescription(_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION_DESC);
        $form->addElement($template_description_editor, false);

        $editor_configs           = array();
        $editor_configs["name"]   = "template_content";
        $editor_configs["value"]  = $this->getVar("template_content", "e");
        $editor_configs["rows"]   = 10;
        $editor_configs["cols"]   = 80;
        $editor_configs["width"]  = "100%";
        $editor_configs["height"] = "400px";
        $editor_configs["editor"] = $this->xnewsletter->getConfig('template_editor');
        $template_content_editor = new XoopsFormEditor(_AM_XNEWSLETTER_TEMPLATE_CONTENT, "template_content", $editor_configs);
        $template_content_editor->setDescription(_AM_XNEWSLETTER_TEMPLATE_CONTENT_DESC);
        $form->addElement($template_content_editor, true);

        $time = ($this->isNew()) ? time() : $this->getVar("template_created");
        $form->addElement(new XoopsFormHidden("template_submitter", $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormHidden("template_created", $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_TEMPLATE_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_TEMPLATE_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_TEMPLATE_SUBMITTER, "template_submitter", false, $this->getVar("template_submitter"), 1, false), true);
        //$form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_TEMPLATE_CREATED, "template_created", "", $this->getVar("template_created")));

        $form->addElement(new XoopsFormHidden("op", "save_template"));
        $form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));

        return $form;
    }
}

/**
 * Class XnewsletterTemplateHandler
 */
class XnewsletterTemplateHandler extends XoopsPersistableObjectHandler
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
        parent::__construct($db, "xnewsletter_template", "XnewsletterTemplate", "template_id", "template_title");
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
