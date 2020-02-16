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
 * @license    GPL 2.0
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
$templateMain = 'xnewsletter_admin_protocols.tpl';

// We recovered the value of the argument op in the URL$
$op = Request::getString('op', 'list');

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    case 'list_protocols':
        $GLOBALS['xoopsTpl']->assign('list_protocols', true);
        $adminObject->displayNavigation($currentFile);
        
        $limit = $helper->getConfig('adminperpage');
        $start = Request::getInt('start', 0);

        //first show misc protocol items
        $protocolCriteria = new \CriteriaCompo();
        $protocolCriteria->add(new \Criteria('protocol_letter_id', '0'));
        $protocolCriteria->setSort('protocol_id');
        $protocolCriteria->setOrder('DESC');
        $protocolCount = $helper->getHandler('Protocol')->getCount($protocolCriteria);
        $protocolCriteria->setLimit(2);
        $protocolsAll               = $helper->getHandler('Protocol')->getAll($protocolCriteria);
        $protocol_status            = '';
        $protocol_created           = '';
        $protocol_created_formatted = '';
        $p                          = 0;
        foreach ($protocolsAll as $id => $protocolObj) {
            ++$p;
            if (count($protocolsAll) > 1) {
                $protocol_status .= "($p) ";
            }
            $protocol_status            .= $protocolObj->getVar('protocol_status') . '<br>';
            $protocol_created_formatted .= formatTimestamp($protocolObj->getVar('protocol_created'), 'M') . '<br>';
        }
        if ($protocolCount > 2) {
            $protocol_status .= '...';
        }
        $GLOBALS['xoopsTpl']->assign('protocol_status', $protocol_status);
        $GLOBALS['xoopsTpl']->assign('protocol_created_formatted', $protocol_created_formatted);

//        letter details

        $sql = 'SELECT protocol_letter_id FROM ' . $xoopsDB->prefix('xnewsletter_protocol') . ' GROUP BY protocol_letter_id';
        $prot_letters = $xoopsDB->query($sql);
        $protocol_letters_total = $prot_letters->num_rows;

        $sql = 'SELECT protocol_letter_id FROM ' . $xoopsDB->prefix('xnewsletter_protocol') . ' GROUP BY protocol_letter_id LIMIT ' . $start.', ' . $limit;
        $prot_letters = $xoopsDB->query($sql);

        while (false !== ($prot_letter = $xoopsDB->fetchArray($prot_letters))) {
            $protocol_letter_id = $prot_letter['protocol_letter_id'];
            $letterCriteria = new \CriteriaCompo();
            $letterCriteria->add(new \Criteria('letter_id', $protocol_letter_id));
            $letterCount = $helper->getHandler('Letter')->getCount();
            $letterObjs = $helper->getHandler('Letter')->getAll($letterCriteria);

            if ($letterCount > 0) {
                $GLOBALS['xoopsTpl']->assign('letters_count', $letterCount);
                foreach (array_keys($letterObjs) as $i) {
                    $protocolCriteria = new \CriteriaCompo();
                    $protocolCriteria->add(new \Criteria('protocol_letter_id', $letterObjs[$i]->getVar('letter_id')));
                    $protocolCriteria->setSort('protocol_id');
                    $protocolCriteria->setOrder('DESC');
                    $protocolCount = $helper->getHandler('Protocol')->getCount($protocolCriteria);
                    if ($protocolCount > 0) {
                        $protocolCriteria->setLimit(2);
                        $protocolsAll     = $helper->getHandler('Protocol')->getAll($protocolCriteria);
                        $protocol_status  = '';
                        $protocol_created = '';

                        $protocol_item['letter_title'] = $letterObjs[$i]->getVar('letter_title');

                        $p = 0;
                        foreach ($protocolsAll as $protocol) {
                            ++$p;
                            if (count($protocolsAll) > 1) {
                                $protocol_status .= "($p) ";
                            }
                            $protocol_status  .= $protocol->getVar('protocol_status') . '<br>';
                            $protocol_created .= formatTimestamp($protocol->getVar('protocol_created'), 'M') . '<br>';
                        }
                        if ($protocolCount > 2) {
                            $protocol_status .= '...';
                        }
                        $protocol_item['letter_id'] = $letterObjs[$i]->getVar('letter_id');
                        $protocol_item['status'] = $protocol_status;
                        $protocol_item['created'] = $protocol_created;

                        $GLOBALS['xoopsTpl']->append('protocols_list', $protocol_item);
                        unset($protocol);
                    }
                }
            }
        }
        if ($protocol_letters_total > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($protocol_letters_total, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }
        break;
    case 'list_letter':
        $GLOBALS['xoopsTpl']->assign('list_letter', true);
        $letter_id = isset($_REQUEST['letter_id']) ? $_REQUEST['letter_id'] : '0';
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, '?op=list', 'list');

        $adminObject->addItemButton(_AM_XNEWSLETTER_LETTER_DELETE_ALL, '?op=delete_protocol_list&letter_id=' . $letter_id, 'delete');
        
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));
        $limit = $helper->getConfig('adminperpage');

        $protocolCriteria = new \CriteriaCompo();
        $protocolCriteria->add(new \Criteria('protocol_letter_id', $letter_id));
        $protocolCriteria->setSort('protocol_id');
        $protocolCriteria->setOrder('DESC');
        $protocolCount = $helper->getHandler('Protocol')->getCount($protocolCriteria);
        $start         = Request::getInt('start', 0);
        $protocolCriteria->setStart($start);
        $protocolCriteria->setLimit($limit);
        $protocolsAll = $helper->getHandler('Protocol')->getAll($protocolCriteria);
        if ($protocolCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($protocolCount, $limit, $start, 'start', 'op=list_letter&letter_id=' . $letter_id);
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }

        // View Table
        $letterObj = $helper->getHandler('Letter')->get($letter_id);
        $GLOBALS['xoopsTpl']->assign('letter_title', $letterObj->getVar('letter_title'));
        if ($protocolCount > 0) {
            $GLOBALS['xoopsTpl']->assign('protocols_count', $protocolCount);
            $class = 'odd';
            foreach ($protocolsAll as $id => $protocolObj) {
                $protocol = $protocolObj->getValuesProtocol();
                $subscrObj  = $helper->getHandler('Subscr')->get($protocolObj->getVar('protocol_subscriber_id'));
                $subscriber = $subscrObj ? $subscrObj->getVar('subscr_email') : _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                if ('' == $subscriber) {
                    $subscriber = '-';
                }
                $protocol['subscriber'] = $subscriber;
                $success_text = (true === (bool)$protocolObj->getVar('protocol_success')) ? XNEWSLETTER_IMG_OK : XNEWSLETTER_IMG_FAILED;
                $protocol['success_text'] = $success_text;
                $GLOBALS['xoopsTpl']->append('protocols_list2', $protocol);
                unset($protocol);
            }
        }
        break;
    case 'new_protocol':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $protocolObj = $helper->getHandler('Protocol')->create();
        $form        = $protocolObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_protocol':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (Request::hasVar('protocol_id', 'REQUEST')) {
            $protocolObj = $helper->getHandler('Protocol')->get(Request::getInt('protocol_id', 0));
        } else {
            $protocolObj = $helper->getHandler('Protocol')->create();
        }

        $protocolObj->setVar('protocol_letter_id',     Request::getInt('protocol_letter_id', 0));
        $protocolObj->setVar('protocol_subscriber_id', Request::getInt('protocol_subscriber_id', 0));
        $protocolObj->setVar('protocol_status',        Request::getString('protocol_status', ''));
        $protocolObj->setVar('protocol_success',       Request::getString('protocol_success', ''));
        $protocolObj->setVar('protocol_submitter',     Request::getInt('protocol_submitter', 0));
        $protocolObj->setVar('protocol_created', strtotime(Request::getInt('protocol_created', 0)));

        if ($helper->getHandler('Protocol')->insert($protocolObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        $GLOBALS['xoopsTpl']->assign('error', $protocolObj->getHtmlErrors());
        $form = $protocolObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_protocol':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWPROTOCOL, '?op=new_protocol', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $protocolObj = $helper->getHandler('Protocol')->get(Request::getInt('protocol_id', 0));
        $form        = $protocolObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_protocol':
        $protocolId = Request::getInt('protocol_id', 0);
        $protocolObj = $helper->getHandler('Protocol')->get($protocolId);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Protocol')->delete($protocolObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $protocolObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'protocol_id' => $protocolId, 'op' => 'delete_protocol'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $protocolId));
        }
        break;
    case 'delete_protocol_list':
        $letter_id = Request::getInt('letter_id', -1, 'REQUEST');
        if ($letter_id >= 0) {
            $letterObj = $helper->getHandler('Letter')->get($letter_id);
            if (true === Request::getBool('ok', false, 'POST')) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                $sql    = "DELETE FROM `{$xoopsDB->prefix('xnewsletter_protocol')}` WHERE `protocol_letter_id`={$letter_id}";
                $result = $xoopsDB->query($sql);
                if ($result) {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
                } else {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELNOTOK);
                }
            } else {
                if ($letter_id > 0) {
                    xoops_confirm(['ok' => true, 'letter_id' => $letter_id, 'op' => 'delete_protocol_list'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL_LIST, $letterObj->getVar('letter_title')));
                } else {
                    xoops_confirm(['ok' => true, 'letter_id' => $letter_id, 'op' => 'delete_protocol_list'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL_LIST, _AM_XNEWSLETTER_PROTOCOL_MISC));
                }

            }
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
