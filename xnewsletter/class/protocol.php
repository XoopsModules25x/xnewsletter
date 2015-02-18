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

include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class XnewsletterProtocol
 */
class XnewsletterProtocol extends XoopsObject
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
        $this->initVar('protocol_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_letter_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_subscriber_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_status', XOBJ_DTYPE_TXTBOX, '', false, 200); // old style
        $this->initVar('protocol_success', XOBJ_DTYPE_OTHER, null, false); // boolean
        $this->initVar('protocol_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('protocol_status_str_id', XOBJ_DTYPE_INT, null, false); // new from v1.3
        $this->initVar('protocol_status_vars', XOBJ_DTYPE_ARRAY, array(), false); // new from v1.3
    }

    private $protocol_status_strs = array(
        _XNEWSLETTER_PROTOCOL_STATUS_EMPTY => _AM_XNEWSLETTER_PROTOCOL_STATUS_EMPTY,
        _XNEWSLETTER_PROTOCOL_STATUS_SAVED => _AM_XNEWSLETTER_PROTOCOL_STATUS_SAVED,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_PHPMAILER => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_PHPMAILER,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND_COUNT => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND_COUNT,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_COUNT => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_COUNT,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_MAILINGLIST => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_MAILINGLIST,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_MAILINGLIST => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_MAILINGLIST,
        _XNEWSLETTER_PROTOCOL_STATUS_CRON => _AM_XNEWSLETTER_PROTOCOL_STATUS_CRON,
        _XNEWSLETTER_PROTOCOL_STATUS_SKIP_IMPORT => _AM_XNEWSLETTER_PROTOCOL_STATUS_SKIP_IMPORT,
        _XNEWSLETTER_PROTOCOL_STATUS_ERROR_IMPORT => _AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_IMPORT,
        _XNEWSLETTER_PROTOCOL_STATUS_OK_IMPORT => _AM_XNEWSLETTER_PROTOCOL_STATUS_OK_IMPORT,
        _XNEWSLETTER_PROTOCOL_STATUS_EXIST_IMPORT => _AM_XNEWSLETTER_PROTOCOL_STATUS_EXIST_IMPORT
    );

    public function getStatusString()
    {
        return xnewsletter_sprintf($this->protocol_status_strs[$this->getVar('protocol_status_str_id')], $this->getVar('protocol_status_vars'));
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
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, "xnewsletter_protocol", "XnewsletterProtocol", "protocol_id", "protocol_letter_id");
        $this->xnewsletter = XnewsletterXnewsletter::getInstance();
    }

    /**
     * Create and insert a new protocol item
     *
     * @param int $letter_id
     * @param int $subscr_id
     * @param
     * @param
     * @param
     * @param bool $success
     *
     * @return bool|object $protocol
     */
    public function protocol($letter_id = 0, $subscr_id = 0, $status = '', $status_str_id = 0, $status_vars = array(), $success = true)
    {
        $uid = (is_object($GLOBALS['xoopsUser']) && isset($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->uid(): 0;
        if (!$protocolObj = $this->create()) {
            return false;
        }
        $protocolObj->setVar('protocol_letter_id', $letter_id);
        $protocolObj->setVar('protocol_subscriber_id', $subscr_id);
        $protocolObj->setVar('protocol_status_str_id', $status_str_id);
        $protocolObj->setVar('protocol_status_vars', $status_vars);
        if ($status == '') {
            $protocolObj->setVar('protocol_status', $protocolObj->getStatusString());
        } else {
            $protocolObj->setVar('protocol_status', $status);
        }
        $protocolObj->setVar('protocol_success', $success); // boolean
        $protocolObj->setVar('protocol_submitter', $uid);
        $protocolObj->setVar('protocol_created', time());
        if (!$this->insert($protocolObj)) {
            echo $protocolObj->getHtmlErrors();
            return false;
        }
        return $protocolObj;
    }
}
