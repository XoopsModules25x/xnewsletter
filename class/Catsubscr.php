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
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

//use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Catsubscr
 */
class Catsubscr extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('catsubscr_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('catsubscr_catid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('catsubscr_subscrid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('catsubscr_quited', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('catsubscr_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('catsubscr_created', XOBJ_DTYPE_INT, time(), false);
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_CATSUBSCR_ADD) : sprintf(_AM_XNEWSLETTER_CATSUBSCR_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $criteria = new \CriteriaCompo();
        $criteria->setSort('cat_id ASC, cat_name');
        $criteria->setOrder('ASC');
        $cat_select = new \XoopsFormSelect(_AM_XNEWSLETTER_CATSUBSCR_CATID, 'catsubscr_catid', $this->getVar('catsubscr_catid'));
        $cat_select->addOptionArray($this->helper->getHandler('Cat')->getList());
        $form->addElement($cat_select, true);

        $subscrCriteria = new \CriteriaCompo();
        $subscrCriteria->setSort('subscr_email ');
        $subscrCriteria->setOrder('ASC');
        $subscr_select = new \XoopsFormSelect(_AM_XNEWSLETTER_CATSUBSCR_SUBSCRID, 'catsubscr_subscrid', $this->getVar('catsubscr_subscrid'));
        $subscr_select->addOptionArray($this->helper->getHandler('Subscr')->getList($subscrCriteria));
        $form->addElement($subscr_select, true);

        $quited_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_CATSUBSCR_QUITED, '&nbsp;');

        $quit_now = new \XoopsFormRadio('', 'catsubscr_quit_now', _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NONE);
        $quit_now->addOptionArray([
                                      _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NONE   => _AM_XNEWSLETTER_CATSUBSCR_QUIT_NONE,
                                      _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_NOW    => _AM_XNEWSLETTER_CATSUBSCR_QUIT_NOW,
                                      _XNEWSLETTER_CATSUBSCR_QUIT_NO_VAL_REMOVE => _AM_XNEWSLETTER_CATSUBSCR_QUIT_REMOVE,
                                  ]);
        $quited_tray->addElement($quit_now, false);
        $quited_tray->addElement(new \XoopsFormLabel('', $this->getVar('catsubscr_quited')));
        $form->addElement($quited_tray, false);

        $time = $this->isNew() ? time() : $this->getVar('catsubscr_created');
        $form->addElement(new \XoopsFormHidden('catsubscr_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new \XoopsFormHidden('catsubscr_created', $time));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        //$form->addElement(new \XoopsFormSelectUser(_AM_XNEWSLETTER_SUBMITTER, 'catsubscr_submitter', false, $this->getVar('catsubscr_submitter'), 1, false), true);
        //$form->addElement(new \XoopsFormTextDateSelect(_AM_XNEWSLETTER_CREATED, 'catsubscr_created', '', $this->getVar('catsubscr_created')));

        $form->addElement(new \XoopsFormHidden('op', 'save_catsubscr'));
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
    public function getValuesCatsubscr($keys = null, $format = null, $maxDepth = null)
    {
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id']        = $this->getVar('catsubscr_id');
        $ret['catid']     = $this->getVar('catsubscr_catid');
        $ret['subscrid']  = $this->getVar('catsubscr_subscrid');
        $ret['quited']    = $this->getVar('catsubscr_quited');
        $ret['created']   = formatTimestamp($this->getVar('catsubscr_created'), 's');
        $ret['submitter'] = \XoopsUser::getUnameFromId($this->getVar('catsubscr_submitter'));
        return $ret;
    }
}
