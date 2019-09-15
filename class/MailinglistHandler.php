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

use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Mailinglist
 */
class MailinglistHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var Helper
     * @access public
     */
    public $helper = null;

    /**
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, 'xnewsletter_mailinglist', Mailinglist::class, 'mailinglist_id', 'mailinglist_email');
        $this->helper = Xnewsletter\Helper::getInstance();
    }
}
