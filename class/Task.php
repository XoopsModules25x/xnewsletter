<?php

namespace XoopsModules\Xnewsletter;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * xnewsletter module for xoops
 *
 * @copyright       The TXMod XOOPS Project http://sourceforge.net/projects/thmod/
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GPL 2.0 or later
 * @package         xnewsletter
 * @since           2.5.x
 * @author          XOOPS Development Team ( name@site.com ) - ( https://xoops.org )
 */

use XoopsModules\Xnewsletter;

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Task
 */
class Task extends \XoopsObject
{
    public $helper = null;

    //Constructor

    public function __construct()
    {
        $this->helper = Xnewsletter\Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        parent::__construct();
        $this->initVar('task_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('task_letter_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('task_subscr_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('task_starttime', XOBJ_DTYPE_INT, null, false);
        $this->initVar('task_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('task_created', XOBJ_DTYPE_INT, time(), false);
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_TASK_ADD) : sprintf(_AM_XNEWSLETTER_TASK_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $letterCriteria = new \CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $letter_select = new \XoopsFormSelect(_AM_XNEWSLETTER_TASK_LETTER_ID, 'task_letter_id', $this->getVar('task_letter_id'));
        $letter_select->addOptionArray($this->helper->getHandler('letter')->getList($letterCriteria));
        $form->addElement($letter_select, true);

        $subscrCriteria = new \CriteriaCompo();
        $subscrCriteria->setSort('subscr_id');
        $subscrCriteria->setOrder('ASC');
        $subscr_select = new \XoopsFormSelect(_AM_XNEWSLETTER_TASK_SUBSCR_ID, 'task_subscr_id', $this->getVar('task_subscr_id'));
        $subscr_select->addOptionArray($this->helper->getHandler('subscr')->getList($subscrCriteria));
        $form->addElement($subscr_select, true);

        $form->addElement(new \XoopsFormTextDateSelect(_AM_XNEWSLETTER_TASK_STARTTIME, 'task_starttime', '', $this->getVar('task_starttime')));

        $form->addElement(new \XoopsFormSelectUser(_AM_XNEWSLETTER_TASK_SUBMITTER, 'task_submitter', false, $this->getVar('task_submitter'), 1, false), true);

        $form->addElement(new \XoopsFormTextDateSelect(_AM_XNEWSLETTER_TASK_CREATED, 'task_created', '', $this->getVar('task_created')));

        $form->addElement(new \XoopsFormHidden('op', 'save_task'));
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}
