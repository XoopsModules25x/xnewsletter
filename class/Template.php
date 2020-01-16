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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

//use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Template
 */
class Template extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('template_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template_title', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('template_description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('template_content', XOBJ_DTYPE_TXTAREA, '', true);
        $this->initVar('template_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template_type', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template_created', XOBJ_DTYPE_INT, time(), false);
    }

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_TEMPLATE_ADD) : sprintf(_AM_XNEWSLETTER_TEMPLATE_EDIT);
        $form  = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        // template_title
        if ( $this->getVar('template_type') == _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL ) {
            $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_TEMPLATE_TITLE, $this->getVar('template_title')));
        } else {
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_TEMPLATE_TITLE, 'template_title', 50, 255, $this->getVar('template_title', 'e')), true);
        }

        // template_description
        if ( $this->getVar('template_type') == _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL ) {
            $form->addElement(new \XoopsFormHidden('template_description', '-'));
        } else {
            $template_description_textarea = new \XoopsFormTextArea(_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION, 'template_description', $this->getVar('template_description', 'e'), 5, 50);
            $template_description_textarea->setDescription(_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION_DESC);
            $form->addElement($template_description_textarea, false);
        }

        // template_content
        if ( $this->getVar('template_type') == _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL ) {
            $form->addElement(new \XoopsFormHidden('template_content', '-'));
        } else {
            $editor_configs = [];
            $editor_configs['name'] = 'template_content';
            $editor_configs['value'] = $this->getVar('template_content', 'e');
            $editor_configs['rows'] = 40;
            $editor_configs['cols'] = 80;
            $editor_configs['width'] = '100%';
            $editor_configs['height'] = '800px';
            $editor_configs['editor'] = $this->helper->getConfig('template_editor');
            $template_content_editor = new \XoopsFormEditor(_AM_XNEWSLETTER_TEMPLATE_CONTENT, 'template_content', $editor_configs);
            $template_content_editor->setDescription(_AM_XNEWSLETTER_TEMPLATE_CONTENT_DESC);
            $form->addElement($template_content_editor, true);
        }

        $templateOnline = $this->isNew() ? 1 : $this->getVar('template_online');
        $form->addElement(new \XoopsFormRadioYN(_AM_XNEWSLETTER_TEMPLATE_ONLINE, 'template_online', $templateOnline, _YES, _NO));

        $templateType = $this->isNew() ? _XNEWSLETTER_MAILINGLIST_TPL_CUSTOM_VAL : $this->getVar('template_type');
        $form->addElement(new \XoopsFormHidden('template_type', $templateType));

        $time = $this->isNew() ? time() : $this->getVar('template_created');
        $form->addElement(new \XoopsFormHidden('template_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new \XoopsFormHidden('template_created', $time));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new \XoopsFormSelectUser(_AM_XNEWSLETTER_SUBMITTER, "template_submitter", false, $this->getVar("template_submitter"), 1, false), true);
        //$form->addElement(new \XoopsFormTextDateSelect(_AM_XNEWSLETTER_CREATED, "template_created", "", $this->getVar("template_created")));

        $form->addElement(new \XoopsFormHidden('op', 'save_template'));
        $form->addElement(new \XoopsFormButtonTray('', _SUBMIT, 'submit', '', false));

        return $form;
    }

    /**
     * Get Values
     * @param null $keys
     * @param string|null $format
     * @param int|null $maxDepth
     * @return array
     */
    public function getValuesTemplate($keys = null, $format = null, $maxDepth = null)
    {
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id']          = $this->getVar('template_id');
        $ret['title']       = $this->getVar('template_title');
        $ret['description'] = $this->getVar('template_description');
        $ret['content']     = $this->getVar('template_content');
        $ret['online']      = $this->getVar('template_online');
        $ret['online_text'] = $this->getVar('template_online') == 1 ? _YES : _NO;
        $ret['type']        = $this->getVar('template_type');
        if ( $this->getVar('template_type') == _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL ) {
            $ret['type_text'] = _AM_XNEWSLETTER_TEMPLATE_TYPE_FILE;
        } else {
            $ret['type_text'] = _AM_XNEWSLETTER_TEMPLATE_TYPE_CUSTOM;
        }

        $ret['created']     = formatTimestamp($this->getVar('template_created'), 's');
        $ret['submitter']   = \XoopsUser::getUnameFromId($this->getVar('template_submitter'));
        return $ret;
    }
}
