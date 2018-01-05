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
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :
 * ****************************************************************************
 */

require_once __DIR__ . '/../include/common.php';

/**
 * Class XnewsletterProtocol
 */
class XnewsletterProtocol extends XoopsObject
{
    public $xnewsletter = null;

    public $protocol_status_strs = [
        _XNEWSLETTER_PROTOCOL_STATUS_SAVED             => _AM_XNEWSLETTER_PROTOCOL_STATUS_SAVED,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST      => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND           => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND        => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND// INPROGRESS
    ];
    //Constructor

    /**
     *
     */
    public function __construct()
    {
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('protocol_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_letter_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_subscriber_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_status', XOBJ_DTYPE_TXTBOX, '', false, 200); // old style
        $this->initVar('protocol_success', XOBJ_DTYPE_OTHER, null, false); // boolean
        $this->initVar('protocol_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_status_str_id', XOBJ_DTYPE_TXTBOX, '', false); // new from v1.3
        $this->initVar('protocol_status_vars', XOBJ_DTYPE_ARRAY, [], false); // new from v1.3
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_PROTOCOL_ADD) : sprintf(_AM_XNEWSLETTER_PROTOCOL_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letter_select = new XoopsFormSelect(_AM_XNEWSLETTER_PROTOCOL_LETTER_ID, 'protocol_letter_id', $this->getVar('protocol_letter_id'));
        $letter_select->addOptionArray($this->xnewsletter->getHandler('letter')->getList($letterCriteria));
        $form->addElement($letter_select, true);

        $subscrCriteria = new CriteriaCompo();
        $subscrCriteria->setSort('subscr_id');
        $subscrCriteria->setOrder('ASC');
        $subscr_select = new XoopsFormSelect(_AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID, 'protocol_subscriber_id', $this->getVar('protocol_subscriber_id'));
        $subscr_select->addOptionArray($this->xnewsletter->getHandler('subscr')->getList($subscrCriteria));
        $form->addElement($subscr_select, true);

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_PROTOCOL_STATUS, 'protocol_status', 50, 200, $this->getVar('protocol_status')), false);

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_PROTOCOL_SUCCESS, 'protocol_success', 50, 255, $this->getVar('protocol_success')), false);

        $form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_PROTOCOL_SUBMITTER, 'protocol_submitter', false, $this->getVar('protocol_submitter'), 1, false), true);

        $form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_PROTOCOL_CREATED, 'protocol_created', '', $this->getVar('protocol_created')));

        $form->addElement(new XoopsFormHidden('op', 'save_protocol'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * Class XnewsletterProtocolHandler
 */
class XnewsletterProtocolHandler extends XoopsPersistableObjectHandler
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
        parent::__construct($db, 'xnewsletter_protocol', 'XnewsletterProtocol', 'protocol_id', 'protocol_letter_id');
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
    }
}
