<?php
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

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_mailinglists.tpl';

require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/mailinglist.php';

// We recovered the value of the argument op in the URL$
$op                 = Request::getString('op', 'list');
$mailinglistId      = Request::getInt('mailinglist_id', 0);
$mailinglist_system = Request::getInt('mailinglist_system', _XNEWSLETTER_MAILINGLIST_TYPE_DEFAULT_VAL);

$GLOBALS['xoopsTpl']->assign('val_mailman',           _XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL);
$GLOBALS['xoopsTpl']->assign('val_majordomo',         _XNEWSLETTER_MAILINGLIST_TYPE_MAJORDOMO_VAL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_url',       XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST . ': '. _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAILMAN, '?op=new_mailinglist&amp;mailinglist_system=' . _XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL, 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST . ': ' . _AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAJORDOMO, '?op=new_mailinglist&amp;mailinglist_system=' . _XNEWSLETTER_MAILINGLIST_TYPE_MAJORDOMO_VAL, 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));
        $limit               = $helper->getConfig('adminperpage');
        $mailinglistCriteria = new \CriteriaCompo();
        $mailinglistCriteria->setSort('mailinglist_id ASC, mailinglist_email');
        $mailinglistCriteria->setOrder('ASC');
        $mailinglistCount = $helper->getHandler('Mailinglist')->getCount();
        $start            = Request::getInt('start', 0);
        $mailinglistCriteria->setStart($start);
        $mailinglistCriteria->setLimit($limit);
        $mailinglistsAll = $helper->getHandler('Mailinglist')->getAll($mailinglistCriteria);
        if ($mailinglistCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($mailinglistCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }

        if ($mailinglistCount > 0) {
            $GLOBALS['xoopsTpl']->assign('mailinglistCount', $mailinglistCount);

            foreach ($mailinglistsAll as $ml_id => $mailinglistObj) {
                $mailinglist = $mailinglistObj->getValuesMailinglist();
                if (_XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL === (int)$mailinglistObj->getVar('mailinglist_system')) {
                    $mailinglist['check_list']=true;
                }
                $GLOBALS['xoopsTpl']->append('mailinglists_list', $mailinglist);
                unset($mailinglist);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_MAILINGLIST);
        }
        break;
    case 'new_mailinglist':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $mailinglistObj = $helper->getHandler('Mailinglist')->create();
        $mailinglistObj->setVar('mailinglist_system', 0);
        $form           = $mailinglistObj->getForm(false, $mailinglist_system);
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_mailinglist':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (Request::hasVar('mailinglist_id', 'REQUEST')) {
            $mailinglistObj = $helper->getHandler('Mailinglist')->get($mailinglistId);
        } else {
            $mailinglistObj = $helper->getHandler('Mailinglist')->create();
        }
        $mailinglistObj->setVar('mailinglist_system',      Request::getString('mailinglist_system'));
        $mailinglistObj->setVar('mailinglist_name',        Request::getString('mailinglist_name'));
        $mailinglistObj->setVar('mailinglist_email',       Request::getString('mailinglist_email'));
        $mailinglistObj->setVar('mailinglist_listname',    Request::getString('mailinglist_listname'));
        $mailinglistObj->setVar('mailinglist_subscribe',   Request::getString('mailinglist_subscribe'));
        $mailinglistObj->setVar('mailinglist_unsubscribe', Request::getString('mailinglist_unsubscribe'));
        $mailinglistObj->setVar('mailinglist_target',      Request::getString('mailinglist_target'));
        $mailinglistObj->setVar('mailinglist_pwd',         Request::getString('mailinglist_pwd'));
        $mailinglistObj->setVar('mailinglist_notifyowner', Request::getInt('mailinglist_notifyowner'));
        $mailinglistObj->setVar('mailinglist_submitter',   Request::getString('mailinglist_submitter'));
        $mailinglistObj->setVar('mailinglist_created',     Request::getInt('mailinglist_created'));

        if ($helper->getHandler('Mailinglist')->insert($mailinglistObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        $GLOBALS['xoopsTpl']->assign('error', $mailinglistObj->getHtmlErrors());
        $form = $mailinglistObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_mailinglist':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWMAILINGLIST, '?op=new_mailinglist', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_MAILINGLISTLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $mailinglistObj = $helper->getHandler('Mailinglist')->get($mailinglistId);
        $form           = $mailinglistObj->getForm(false, (int)$mailinglistObj->getVar('mailinglist_system'));
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'check_list':
        $mailinglistObj = $helper->getHandler('Mailinglist')->get($mailinglistId);
        if (_XNEWSLETTER_MAILINGLIST_TYPE_MAILMAN_VAL === (int)$mailinglistObj->getVar('mailinglist_system')) {
            $actioncode = getActioncode($mailinglistObj->getVar('mailinglist_id'));
            $c = curl_init($actioncode);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($c);
            if (curl_error($c)) {
                $GLOBALS['xoopsTpl']->assign('error', curl_error($c));
            }
            $status = (int)curl_getinfo($c, CURLINFO_HTTP_CODE);
            curl_close($c);

            if ($status === 200) {
                $GLOBALS['xoopsTpl']->assign('success', _AM_XNEWSLETTER_MAILINGLIST_CSUCCESS);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAILINGLIST_CFAILED . $status);
            }
            $mailinglistObj = $helper->getHandler('Mailinglist')->get($mailinglistId);
            $form           = $mailinglistObj->getForm(false, (int)$mailinglistObj->getVar('mailinglist_system'));
            $GLOBALS['xoopsTpl']->assign('form', $form->render());
        }

        break;
    case 'delete_mailinglist':
        $mailinglistObj = $helper->getHandler('Mailinglist')->get($mailinglistId);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Mailinglist')->delete($mailinglistObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $mailinglistObj->getHtmlErrors());
            }
        } else {
            xoops_confirm([
                              'ok'             => true,
                              'mailinglist_id' => $mailinglistId,
                              'op'             => 'delete_mailinglist',
                          ], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $mailinglistObj->getVar('mailinglist_name')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
