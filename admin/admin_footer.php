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
 *  @copyright  Goffy ( wedega.com )
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

echo "
<br /><br /><div align='center'><a href='http://www.xoops.org' target='_blank'>
<img src='" . XNEWSLETTER_ICONS_URL . "/xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS' /></a>
</div>";
echo "
<div class='center small italic pad5'>
<strong>" . $xnewsletter->getModule()->getVar('name') . "</strong> " . _AM_XNEWSLETTER_MAINTAINEDBY . "
<a href='http://www.xoops.org' title='Visit' class='tooltip' rel='external'>XOOPS Community</a>
</div>";
xoops_cp_footer();
