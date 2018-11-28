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
include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class XnewsletterMailinglist
 */
class XnewsletterMailinglist extends XoopsObject
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
        $this->initVar('mailinglist_id', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('mailinglist_name', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_email', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_listname', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_subscribe', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_unsubscribe', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_submitter', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('mailinglist_created', XOBJ_DTYPE_INT, null, false, 10);
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
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_MAILINGLIST_ADD) : sprintf(_AM_XNEWSLETTER_MAILINGLIST_EDIT);

        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $mailinglist_name = $this->isNew() ? 'myname' : $this->getVar('mailinglist_name');
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_NAME, 'mailinglist_name', 50, 255, $mailinglist_name), true);

        $mailinglist_email = $this->isNew() ? 'mailinglist@mydomain.com' : $this->getVar('mailinglist_email');
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_EMAIL_DESC, 'mailinglist_email', 50, 255, $mailinglist_email), true);

        $mailinglist_listname = $this->isNew() ? 'nameofmylist' : $this->getVar('mailinglist_listname');
        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_LISTNAME, 'mailinglist_listname', 50, 255, $mailinglist_listname), true);

        $mailinglist_subscribe = $this->isNew() ? 'subscribe nameofmylist {email}' : $this->getVar('mailinglist_subscribe');
        $form->addElement(
            new XoopsFormText(
                _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE . "<br/><span style='font-size:0,75em'>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE_DESC . '</span>', 'mailinglist_subscribe',
                50,
                255,
                $mailinglist_subscribe
            ),
            true
        );

        $mailinglist_unsubscribe = $this->isNew() ? 'unsubscribe nameofmylist {email}' : $this->getVar('mailinglist_unsubscribe');
        $form->addElement(
            new XoopsFormText(
                _AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE . "<br/><span style='font-size:0,75em'>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE_DESC . '</span>', 'mailinglist_unsubscribe',
                50,
                255,
                $mailinglist_unsubscribe
            ),
            true
        );

        $time = ($this->isNew()) ? time() : $this->getVar('mailinglist_created');
        $form->addElement(new XoopsFormHidden('mailinglist_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new XoopsFormHidden('mailinglist_created', $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ACCOUNTS_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_ACCOUNTS_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new XoopsFormSelectUser(_AM_XNEWSLETTER_MAILINGLIST_SUBMITTER, "mailinglist_submitter", false, $this->getVar("mailinglist_submitter"), 1, false), true);
        //$form->addElement(new XoopsFormTextDateSelect(_AM_XNEWSLETTER_MAILINGLIST_CREATED, "mailinglist_created", "", $this->getVar("mailinglist_created")));

        $form->addElement(new XoopsFormHidden('op', 'save_mailinglist'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * Class XnewsletterMailinglist
 */
class XnewsletterMailinglistHandler extends XoopsPersistableObjectHandler
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
        parent::__construct($db, 'xnewsletter_mailinglist', 'XnewsletterMailinglist', 'mailinglist_id', 'mailinglist_email');
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
