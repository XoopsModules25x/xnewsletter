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
 * Class Mailinglist
 */
class Mailinglist extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('mailinglist_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mailinglist_name', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_email', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_listname', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_subscribe', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_unsubscribe', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mailinglist_created', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('mailinglist_system', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_target', XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar('mailinglist_pwd', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('mailinglist_notifyowner', XOBJ_DTYPE_INT, null, false);
    }

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getForm($action = false, $system = 0)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_MAILINGLIST_ADD) : sprintf(_AM_XNEWSLETTER_MAILINGLIST_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'xn_ml_form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new \XoopsFormHidden('mailinglist_system', $system));

        if ($system === _XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL) {
            $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_MAILINGLIST_SYSTEM, _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAILMAN));

            $mailinglist_name = $this->isNew() ? 'myname' : $this->getVar('mailinglist_name');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_NAME, 'mailinglist_name', 50, 255, $mailinglist_name), true);

            $mailinglist_listname = $this->isNew() ? 'nameofmylist' : $this->getVar('mailinglist_listname');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_LISTNAME, 'mailinglist_listname', 50, 255, $mailinglist_listname), true);

            $form->addElement(new \XoopsFormHidden('mailinglist_email', ''));
            $form->addElement(new \XoopsFormHidden('mailinglist_subscribe', ''));
            $form->addElement(new \XoopsFormHidden('mailinglist_unsubscribe', ''));

            $mailinglist_target = $this->isNew() ? 'https://lists.mydomain.com' : $this->getVar('mailinglist_target');
            $form->addElement(new \XoopsFormText( _AM_XNEWSLETTER_MAILINGLIST_TARGET, 'mailinglist_target', 50, 255, $mailinglist_target));

            $mailinglist_pwd = $this->isNew() ? '' : $this->getVar('mailinglist_pwd');
            $form->addElement(new \XoopsFormText( _AM_XNEWSLETTER_MAILINGLIST_PWD, 'mailinglist_pwd', 50, 255, $mailinglist_pwd));

            $mailinglist_notifyowner = $this->isNew() ? 1 : $this->getVar('mailinglist_notifyowner');
            $form->addElement(new \XoopsFormRadioYN(_AM_XNEWSLETTER_MAILINGLIST_NOTIFYOWNER, 'mailinglist_notifyowner', $mailinglist_notifyowner, _YES, _NO));
        }
        if ($system === _XNEWSLETTER_MAILINGLIST_TYPE_MAJORDOMO_VAL) {
            $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_MAILINGLIST_SYSTEM, _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAJORDOMO));

            $mailinglist_name = $this->isNew() ? 'myname' : $this->getVar('mailinglist_name');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_NAME, 'mailinglist_name', 50, 255, $mailinglist_name), true);

            $mailinglist_listname = $this->isNew() ? 'nameofmylist' : $this->getVar('mailinglist_listname');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_LISTNAME, 'mailinglist_listname', 50, 255, $mailinglist_listname), true);

            $mailinglist_email = $this->isNew() ? 'nameofmylist@mydomain.com' : $this->getVar('mailinglist_email');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_EMAIL_DESC, 'mailinglist_email', 50, 255, $mailinglist_email));

            $mailinglist_subscribe = $this->isNew() ? 'subscribe nameofmylist {email}' : $this->getVar('mailinglist_subscribe');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE . "<br><span style='font-size:0,75em'>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE_DESC . '</span>', 'mailinglist_subscribe', 50, 255, $mailinglist_subscribe));

            $mailinglist_unsubscribe = $this->isNew() ? 'unsubscribe nameofmylist {email}' : $this->getVar('mailinglist_unsubscribe');
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE . "<br><span style='font-size:0,75em'>" . _AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE_DESC . '</span>', 'mailinglist_unsubscribe', 50, 255, $mailinglist_unsubscribe));

            $form->addElement(new \XoopsFormHidden('mailinglist_target', ''));
            $form->addElement(new \XoopsFormHidden('mailinglist_pwd', ''));
            $form->addElement(new \XoopsFormHidden('mailinglist_notifyowner', 0));
        }

        $time = $this->isNew() ? time() : $this->getVar('mailinglist_created');
        $form->addElement(new \XoopsFormHidden('mailinglist_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new \XoopsFormHidden('mailinglist_created', $time));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new \XoopsFormHidden('mailinglist_id', $this->getVar('mailinglist_id')));
        $form->addElement(new \XoopsFormHidden('op', 'save_mailinglist'));
        $form->addElement(new \XoopsFormButtonTray('save', _SUBMIT, 'submit', '', false));

        return $form;
    }

    /**
     * Get Values
     * @param null $keys
     * @param string|null $format
     * @param int|null $maxDepth
     * @return array
     */
    public function getValuesMailinglist($keys = null, $format = null, $maxDepth = null)
    {
        $ret['id']          = $this->getVar('mailinglist_id');
        $ret['name']        = $this->getVar('mailinglist_name');
        $ret['email']       = $this->getVar('mailinglist_email');
        $ret['listname']    = $this->getVar('mailinglist_listname');
        $ret['subscribe']   = $this->getVar('mailinglist_subscribe');
        $ret['unsubscribe'] = $this->getVar('mailinglist_unsubscribe');
        $ret['system']      = $this->getVar('mailinglist_system');
        switch ($this->getVar('mailinglist_system')) {
            case _XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL:
                $ret['system_text'] = _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAILMAN;
                break;
            case _XNEWSLETTER_MAILINGLIST_TYPE_MAJORDOMO_VAL:
                $ret['system_text'] = _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAJORDOMO;
                break;
            case _XNEWSLETTER_MAILINGLIST_TYPE_DEFAULT_VAL:
            default:
            $ret['system_text'] = _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_DEFAULT;
                break;
        }
        $ret['target']           = $this->getVar('mailinglist_target');
        $ret['pwd']              = $this->getVar('mailinglist_pwd');
        $ret['notifyowner']      = $this->getVar('mailinglist_notifyowner');
        $ret['notifyowner_text'] = $this->getVar('mailinglist_notifyowner') == 1 ? _YES : _NO;
        $ret['created']          = formatTimestamp($this->getVar('mailinglist_created'), 's');
        $ret['submitter']        = \XoopsUser::getUnameFromId($this->getVar('mailinglist_submitter'));
        return $ret;
    }
}
