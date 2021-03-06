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
xoops_loadLanguage('admin', 'xnewsletter');

/**
 * Class Bmh
 */
class Bmh extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('bmh_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('bmh_rule_no', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('bmh_rule_cat', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('bmh_bouncetype', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('bmh_remove', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('bmh_email', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('bmh_subject', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('bmh_measure', XOBJ_DTYPE_INT, null, false);
        $this->initVar('bmh_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('bmh_created', XOBJ_DTYPE_INT, time(), false);
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_BMH_ADD) : sprintf(_AM_XNEWSLETTER_BMH_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $account_default = $this->getVar('bmh_accounts_id');
        $accontsCriteria = new \CriteriaCompo();
        $accontsCriteria->setSort('accounts_id');
        $accontsCriteria->setOrder('ASC');
        $opt_accounts = new \XoopsFormSelect(_AM_XNEWSLETTER_BMH_ACCOUNTS_ID, 'bmh_accounts_id', $account_default);
        $opt_accounts->addOptionArray($this->helper->getHandler('Accounts')->getList($accontsCriteria));
        $form->addElement($opt_accounts, false);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_BMH_RULE_NO, 'bmh_rule_no', 50, 255, $this->getVar('bmh_rule_no')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_BMH_RULE_CAT, 'bmh_rule_cat', 50, 255, $this->getVar('bmh_rule_cat')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_BMH_BOUNCETYPE, 'bmh_bouncetype', 50, 255, $this->getVar('bmh_bouncetype')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_BMH_REMOVE, 'bmh_remove', 50, 255, $this->getVar('bmh_remove')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_BMH_EMAIL, 'bmh_email', 50, 255, $this->getVar('bmh_email')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_BMH_SUBJECT, 'bmh_subject', 50, 255, $this->getVar('bmh_subject')), false);

        $measure_select = new \XoopsFormSelect(_AM_XNEWSLETTER_BMH_MEASURE, 'bmh_measure', $this->getVar('bmh_measure'));
        $measure_select->addOption(_XNEWSLETTER_BMH_MEASURE_VAL_PENDING, _AM_XNEWSLETTER_BMH_MEASURE_PENDING);
        $measure_select->addOption(_XNEWSLETTER_BMH_MEASURE_VAL_NOTHING, _AM_XNEWSLETTER_BMH_MEASURE_NOTHING);
        $measure_select->addOption(_XNEWSLETTER_BMH_MEASURE_VAL_QUIT, _AM_XNEWSLETTER_BMH_MEASURE_QUIT);
        $form->addElement($measure_select, true);

        $time = $this->isNew() ? time() : $this->getVar('bmh_created');
        $form->addElement(new \XoopsFormHidden('bmh_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new \XoopsFormHidden('bmh_created', $time));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new \XoopsFormSelectUser(_AM_XNEWSLETTER_SUBMITTER, "bmh_submitter", false, $this->getVar("bmh_submitter"), 1, false), true);
        //$form->addElement(new \XoopsFormTextDateSelect(_AM_XNEWSLETTER_CREATED, "bmh_created", "", $this->getVar("bmh_created")));

        $form->addElement(new \XoopsFormHidden('op', 'save_bmh'));
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
    public function getValuesBmh($keys = null, $format = null, $maxDepth = null)
    {
        $arr_measure_type = [
            _XNEWSLETTER_BMH_MEASURE_VAL_ALL     => _AM_XNEWSLETTER_BMH_MEASURE_ALL,
            _XNEWSLETTER_BMH_MEASURE_VAL_PENDING => _AM_XNEWSLETTER_BMH_MEASURE_PENDING,
            _XNEWSLETTER_BMH_MEASURE_VAL_NOTHING => _AM_XNEWSLETTER_BMH_MEASURE_NOTHING,
            _XNEWSLETTER_BMH_MEASURE_VAL_QUIT    => _AM_XNEWSLETTER_BMH_MEASURE_QUITED,
            _XNEWSLETTER_BMH_MEASURE_VAL_DELETE  => _AM_XNEWSLETTER_BMH_MEASURE_DELETED,
        ];
        $ret['id']           = $this->getVar('bmh_id');
        $ret['rule_no']      = $this->getVar('bmh_rule_no');
        $ret['rule_cat']     = $this->getVar('bmh_rule_cat');
        $ret['bouncetype']   = $this->getVar('bmh_bouncetype');
        $ret['remove']       = $this->getVar('bmh_remove') == '0' ? '' : $this->getVar('bmh_remove');
        $ret['email']        = $this->getVar('bmh_email');
        $ret['subject']      = $this->getVar('bmh_subject');
        $ret['measure']      = $this->getVar('bmh_measure');
        $ret['measure_text'] = $arr_measure_type[$this->getVar('bmh_measure')];
        $ret['created']      = formatTimestamp($this->getVar('bmh_created'), 'L');
        $ret['submitter']    = \XoopsUser::getUnameFromId($this->getVar('bmh_submitter'));
        return $ret;
    }
}
