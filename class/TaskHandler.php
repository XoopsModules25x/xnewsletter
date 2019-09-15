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
 * Class TaskHandler
 */
class TaskHandler extends \XoopsPersistableObjectHandler
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
        parent::__construct($db, 'xnewsletter_task', Task::class, 'task_id', 'task_letter_id');
        $this->helper = Xnewsletter\Helper::getInstance();
    }
}
