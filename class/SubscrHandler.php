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
 * Class SubscrHandler
 */
class SubscrHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var Helper
     * @access public
     */
    public $helper = null;

    /**
     * @param null|\XoopsDatabase                   $db
     * @param \XoopsModules\Xnewsletter\Helper|null $helper
     */
    public function __construct(\XoopsDatabase $db = null, Helper $helper = null)
    {
        parent::__construct($db, 'xnewsletter_subscr', Subscr::class, 'subscr_id', 'subscr_email');
        /** @var Helper $this->helper */
        if (null === $helper) {
            $this->helper = Helper::getInstance();
        } else {
            $this->helper = $helper;
        }
    }

    /**
     * Delete subscriber ({@link subscr} object), subscriptions ({@link catsubscr} objects) and mailinglist (subscribingMLHandler function)
     *
     * @param \XoopsObject $subscrObj
     * @param bool   $force
     *
     * @internal param object $object
     * @return bool
     */
    public function delete(\XoopsObject $subscrObj, $force = false)
    {
        $res       = true;
        $subscr_id = (int)$subscrObj->getVar('subscr_id');
        // delete subscriptions ({@link catsubscr} objects)
        if ($this->helper->getHandler('Catsubscr')->getCount(new \Criteria('catsubscr_subscrid', $subscr_id)) > 0) {
            $catsubscrObjs = $this->helper->getHandler('Catsubscr')->getAll(new \Criteria('catsubscr_subscrid', $subscr_id));
            foreach ($catsubscrObjs as $catsubscr_id => $catsubscrObj) {
                $catObj          = $this->helper->getHandler('Cat')->get($catsubscrObj->getVar('catsubscr_catid'));
                $cat_mailinglist = $catObj->getVar('cat_mailinglist');
                if ($this->helper->getHandler('Catsubscr')->delete($catsubscrObj, $force)) {
                    // handle mailinglists
                    if (0 != $cat_mailinglist) {
                        require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';
                        subscribingMLHandler(_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE, $subscr_id, $cat_mailinglist);
                    }
                } else {
                    $res = false;
                    $subscrObj->setErrors($catsubscrObj->getErrors());
                }
            }
        }
        // delete subscriber ({@link subscr} object)
        if (true === $res) {
            $res = parent::delete($subscrObj, $force);
        }

        return $res;
    }
}
