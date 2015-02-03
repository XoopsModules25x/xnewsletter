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

include_once dirname(__DIR__) . '/include/common.php';

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
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('template_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template_title', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('template_description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('template_content', XOBJ_DTYPE_TXTAREA, '', true);
        $this->initVar('template_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template_created', XOBJ_DTYPE_INT, time(), false);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsUser;
        //
        xoops_load('XoopsFormLoader');
        //
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        //
        $isAdmin = xnewsletter_userIsAdmin();
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);
        //
        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_TEMPLATE_ADD) : sprintf(_AM_XNEWSLETTER_TEMPLATE_EDIT);
        //
        $form  = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        // template: template_title
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_TEMPLATE_TITLE, 'template_title', 50, 255, $this->getVar('template_title', 'e')), true);
        // template: template_description
        $template_description_textarea = new XoopsFormTextArea(_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION, 'template_description', $this->getVar('template_description', 'e'), 5, 50);
        $template_description_textarea->setDescription(_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION_DESC);
        $form->addElement($template_description_textarea, false);
        // template: template_content
        $editor_configs           = array();
        $editor_configs['name']   = 'template_content';
        $editor_configs['value']  = $this->getVar('template_content', 'e');
        $editor_configs['rows']   = 40;
        $editor_configs['cols']   = 80;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '800px';
        $editor_configs['editor'] = $this->xnewsletter->getConfig('template_editor');
        $template_content_editor  = new XoopsFormEditor(_AM_XNEWSLETTER_TEMPLATE_CONTENT, 'template_content', $editor_configs);
        $template_content_editor->setDescription(_AM_XNEWSLETTER_TEMPLATE_CONTENT_DESC);
        $form->addElement($template_content_editor, true);
        // template: extra info
        $time = ($this->isNew()) ? time() : $this->getVar('template_created');
        $form->addElement(new XoopsFormHidden('template_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormHidden('template_created', $time));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_TEMPLATE_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_TEMPLATE_CREATED, formatTimestamp($time, 's')));
        //
        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_TEMPLATE_SUBMITTER, 'template_submitter', false, $this->getVar('template_submitter'), 1, false), true);
        //$form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_TEMPLATE_CREATED, 'template_created', '', $this->getVar('template_created')));
        // form: button tray
        $button_tray = new XoopsFormElementTray('', '');
        $button_tray->addElement(new XoopsFormHidden('op', 'save_template'));
        //
        $button_submit = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
        $button_tray->addElement($button_submit);
        //
        $button_reset = new XoopsFormButton('', '', _RESET, 'reset');
        $button_tray->addElement($button_reset);
        //
        $button_cancel = new XoopsFormButton('', '', _CANCEL, 'button');
        $button_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($button_cancel);
        //
        $form->addElement($button_tray);
        //
        return $form;
    }
}

/**
 * Class XnewsletterTemplateHandler
 */
class XnewsletterTemplateHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var XnewsletterXnewsletter
     * @access public
     */
    public $xnewsletter = null;

    /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, 'xnewsletter_template', 'XnewsletterTemplate', 'template_id', 'template_title');
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
    }
}
