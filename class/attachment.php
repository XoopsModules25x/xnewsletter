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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :
 * ****************************************************************************
 */

require_once __DIR__ . '/../include/common.php';

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
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('attachment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachment_letter_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachment_name', XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar('attachment_type', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('attachment_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachment_created', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('attachment_size', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('attachment_mode', XOBJ_DTYPE_INT, _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, false);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_ATTACHMENT_ADD) : sprintf(_AM_XNEWSLETTER_ATTACHMENT_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_NAME, $this->getVar('attachment_name')));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_SIZE, "<span title='" . $this->getVar('attachment_size') . " B'>" . xnewsletter_bytesToSize1024($this->getVar('attachment_size')) . '</span>'));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_TYPE, $this->getVar('attachment_type')));

        // attachment_mode
        $mode_select = new XoopsFormRadio(_AM_XNEWSLETTER_ATTACHMENT_MODE, 'attachment_mode', $this->getVar('attachment_mode'));
        $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASATTACHMENT);
        $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASLINK, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASLINK);
        //$mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_AUTO, _AM_XNEWSLETTER_ATTACHMENT_MODE_AUTO);  // for future features
        $form->addElement($mode_select);

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new XoopsFormHidden('op', 'save_attachment'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * Class XnewsletterAttachmentHandler
 */
class XnewsletterAttachmentHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var XnewsletterXnewsletter
     * @access public
     */
    public $xnewsletter = null;

    /**
     * @param null|object|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'xnewsletter_attachment', 'XnewsletterAttachment', 'attachment_id', 'attachment_letter_id');
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
    }

    /**
     * Delete attachment ({@link attachment} object) and file from filesystem
     *
     * @param object $attachmentObj
     * @param bool   $force
     *
     * @internal param object $object
     * @return bool
     */
    public function delete($attachmentObj, $force = false)
    {
        $res                  = true;
        $attachment_letter_id = (int)$attachmentObj->getVar('attachment_letter_id');
        $attachment_name      = (string)$attachmentObj->getVar('attachment_name');
        //
        if ($res = parent::delete($attachmentObj, $force)) {
            // delete file from filesystem
            @unlink(XOOPS_UPLOAD_PATH . $this->xnewsletter->getConfig('xn_attachment_path') . $attachment_letter_id . '/' . $attachment_name);
        }

        return $res;
    }
}
