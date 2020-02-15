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
$templateMain = 'xnewsletter_admin_templates.tpl';

// We recovered the value of the argument op in the URL$
$op         = Request::getString('op', 'list');
$templateId = Request::getInt('template_id', 0);

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    case 'list_templates':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        //check file templates
        $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
        if (!is_dir($template_path)) {
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
        }
        $templateFiles = [];
        if (!$dirHandler = @opendir($template_path)) {
            die(str_replace('%p', $template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH));
        }
        while ($filename = readdir($dirHandler)) {
            if (('.' !== $filename) and ('..' !== $filename) and ('index.html' !== $filename)) {
                $template_title = str_replace('.tpl', '', $filename);
                $templateCriteria = new \CriteriaCompo();
                $templateCriteria->add(new \Criteria('template_title', $template_title));
                $templatesCount = $helper->getHandler('Template')->getCount($templateCriteria);
                if ($templatesCount == 0){
                    $templateObj = $helper->getHandler('Template')->create();
                    $templateObj->setVar('template_title',       Request::getString('template_title', $template_title));
                    $templateObj->setVar('template_description', Request::getString('template_description', '-'));
                    $templateObj->setVar('template_content',     Request::getText('template_content', '-'));
                    $templateObj->setVar('template_online',      Request::getInt('template_online', 1));
                    $templateObj->setVar('template_type',        Request::getInt('template_type', _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL));
                    $templateObj->setVar('template_submitter',   Request::getInt('template_submitter', $GLOBALS['xoopsUser']->uid()));
                    $templateObj->setVar('template_created',     Request::getInt('template_created', time()));

                    if ($helper->getHandler('Template')->insert($templateObj)) {
                        redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
                    }
                    $GLOBALS['xoopsTpl']->assign('error', $templateObj->getHtmlErrors());
                }
                unset($templateCriteria);
                unset($templateObj);
            }
        }
        closedir($dirHandler);

        // read template table
        $start          = Request::getInt('start', 0);
        $limit            = $helper->getConfig('adminperpage');
        $templateCriteria = new \CriteriaCompo();
        $templateCriteria->setSort('template_type ASC, template_id');
        $templateCriteria->setOrder('DESC');
        $templatesCount = $helper->getHandler('Template')->getCount();
        if ($templatesCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($templatesCount, $limit, $start, 'start', 'op=list');
            $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
        }
        if ($templatesCount > 0) {
            $templateCriteria->setStart($start);
            $templateCriteria->setLimit($limit);
            $templatesAll = $helper->getHandler('Template')->getAll($templateCriteria);
            $GLOBALS['xoopsTpl']->assign('templatesCount', $templatesCount);
            foreach ($templatesAll as $id => $templateObj) {
                $template = $templateObj->getValuesTemplate();
                // check whether template exist or not
                $template['template_err'] = false;
                if ( $templateObj->getVar('template_type') === _XNEWSLETTER_MAILINGLIST_TPL_FILE_VAL) {
                    $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
                    if (!is_dir($template_path)) {
                        $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
                    }
                    $filename = $template_path . $templateObj->getVar('template_title') . '.tpl';
                    if (!file_exists ( $filename )) {
                        $template['template_err'] = true;
                        $template['template_err_text'] = str_replace('%s', $template_path, _AM_XNEWSLETTER_TEMPLATE_ERR_FILE);
                    }
                    $template['type_text'] = $template['type_text'] . ' *';
                }
                $GLOBALS['xoopsTpl']->append('templates_list', $template);
                unset($template);
            }
        } else {
            $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_THEREARENT_TEMPLATE);
        }
        $GLOBALS['xoopsTpl']->assign('template_file_info', str_replace('%s', $template_path, _AM_XNEWSLETTER_TEMPLATE_TYPE_FILE_INFO));
        break;
    case 'new_template':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $templateObj = $helper->getHandler('Template')->create();
        $form        = $templateObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'save_template':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $templateObj = $helper->getHandler('Template')->get($templateId);
        $templateObj->setVar('template_title',       Request::getString('template_title', ''));
        $templateObj->setVar('template_description', Request::getString('template_description', ''));
        $templateObj->setVar('template_content',     Request::getText('template_content', ''));
        $templateObj->setVar('template_online',      Request::getInt('template_online', 0));
        $templateObj->setVar('template_type',        Request::getInt('template_type', 0));
        $templateObj->setVar('template_submitter',   Request::getInt('template_submitter', 0));
        $templateObj->setVar('template_created',     Request::getInt('template_created', time()));

        if ($helper->getHandler('Template')->insert($templateObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }

        $GLOBALS['xoopsTpl']->assign('error', $templateObj->getHtmlErrors());
        $form = $templateObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'edit_template':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWTEMPLATE, '?op=new_template', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_TEMPLATELIST, '?op=list', 'list');
        $GLOBALS['xoopsTpl']->assign('buttons', $adminObject->renderButton('left'));

        $templateObj = $helper->getHandler('Template')->get($templateId);
        $form        = $templateObj->getForm();
        $GLOBALS['xoopsTpl']->assign('form', $form->render());
        break;
    case 'delete_template':
        $templateObj = $helper->getHandler('Template')->get($templateId);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Template')->delete($templateObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                $GLOBALS['xoopsTpl']->assign('error', $templateObj->getHtmlErrors());
            }
        } else {
            xoops_confirm(['ok' => true, 'template_id' => $templateId, 'op' => 'delete_template'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $templateObj->getVar('template_title')));
        }
        break;
    case 'state_template':
        $templateObj = $helper->getHandler('Template')->get($templateId);
        $templateObj->setVar('template_online', Request::getInt('template_online', 0));
        if ($helper->getHandler('Template')->insert($templateObj)) {
            redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
        }
        $GLOBALS['xoopsTpl']->assign('error', $templateObj->getHtmlErrors());
        break;
    case 'set_template':
        $templateId = Request::getInt('template_id', 0);
        if ($templateId > 0) {
            $letterCount = $helper->getHandler('Letter')->getCount();
            if ($letterCount > 0) {
                $letterAll = $helper->getHandler('Letter')->getAll();
                foreach ($letterAll as $id => $letterObj) {
                    $letterObj->setVar('letter_templateid', $templateId);
                     if ($helper->getHandler('Letter')->insert($letterObj)) {
                        
                    } else {
                        $GLOBALS['xoopsTpl']->assign('error', $letterObj->getHtmlErrors());
                    }
                }
            }
            redirect_header('letter.php?op=list_letters', 3, _AM_XNEWSLETTER_FORMOK);
        } else {
            // should not be
            $GLOBALS['xoopsTpl']->assign('error', 'Invalid parameter template id');
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
