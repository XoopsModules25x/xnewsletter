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
 * Class Letter
 */
class Letter extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('letter_id',        XOBJ_DTYPE_INT,    null, false);
        $this->initVar('letter_title',     XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('letter_content',   XOBJ_DTYPE_TXTAREA,null, true);
        $this->initVar('letter_templateid',XOBJ_DTYPE_INT,    null, false);
        $this->initVar('letter_cats',      XOBJ_DTYPE_TXTBOX, null, false, 100); // IN PROGRESS: AN ARRAY SHOULD BE BETTER
        $this->initVar('letter_attachment',XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar('letter_account',   XOBJ_DTYPE_INT,    null, false);
        $this->initVar('letter_email_test',XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('letter_submitter', XOBJ_DTYPE_INT,    null, false);
        $this->initVar('letter_created',   XOBJ_DTYPE_INT,     time(), false); // timestamp
        $this->initVar('letter_sender',    XOBJ_DTYPE_INT,    null, false);
        $this->initVar('letter_sent',      XOBJ_DTYPE_INT,    false, false); // timestamp or false
    }

    /**
     * @param bool $action
     * @param bool $admin_aerea
     *
     * @return null|\XoopsThemeForm
     */
    public function getForm($action = false, $admin_aerea = false)
    {
        global $xoopsDB, $xoopsUser, $pathImageIcon;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_LETTER_ADD) : sprintf(_AM_XNEWSLETTER_LETTER_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        // letter_title
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_LETTER_TITLE, 'letter_title', 50, 255, $this->getVar('letter_title', 'e')), true);

        // letter_content
        $editor_configs           = [];
        $editor_configs['name']   = 'letter_content';
        $editor_configs['value']  = $this->getVar('letter_content', 'e');
        $editor_configs['rows']   = 40;
        $editor_configs['cols']   = 80;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '800px';
        $editor_configs['editor'] = $this->helper->getConfig('xnewsletter_editor');
        $letter_content_editor    = new \XoopsFormEditor(_AM_XNEWSLETTER_LETTER_CONTENT, 'letter_content', $editor_configs);
        $letter_content_editor->setDescription(_AM_XNEWSLETTER_LETTER_CONTENT_DESC);
        $form->addElement($letter_content_editor, true);

        // letter_template
        $letterTemplateid = $this->isNew() ? 1 : $this->getVar('letter_templateid');
        $template_select = new \XoopsFormSelect(_AM_XNEWSLETTER_LETTER_TEMPLATE, 'letter_templateid', $letterTemplateid);
        // get template objects from database
        $templateCriteria = new \CriteriaCompo();
        $templateCriteria->add(new \Criteria('template_online', 1));
        $templateCriteria->setSort('template_title ASC, template_id');
        $templateCriteria->setOrder('DESC');
        $templateCount = $this->helper->getHandler('Template')->getCount($templateCriteria);
        if ($templateCount > 0) {
            $template_select->addOptionArray($this->helper->getHandler('Template')->getList($templateCriteria));
        } else {
            redirect_header('letter.php?op=list', 3, _MA_XNEWSLETTER_NOTEMPLATE_ONLINE);
        }
        $form->addElement($template_select, false);

        // letter_cats
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler    = xoops_getHandler('member');
        $groups           = $memberHandler->getGroupsByUser($xoopsUser->uid());
        $catCriteria      = new \CriteriaCompo();
        $catCriteria->setSort('cat_id');
        $catCriteria->setOrder('ASC');
        $letter_cats = explode('|', $this->getVar('letter_cats'));
        $cat_select  = new \XoopsFormCheckBox(_AM_XNEWSLETTER_LETTER_CATS, 'letter_cats', $letter_cats);
        $catObjs     = $this->helper->getHandler('Cat')->getAll($catCriteria);
        foreach ($catObjs as $cat_id => $catObj) {
            $cat_name = $catObj->getVar('cat_name');
            $show     = $grouppermHandler->checkRight('newsletter_create_cat', $cat_id, $groups, $this->helper->getModule()->mid());
            if (1 == $show) {
                $cat_select->addOption($cat_id, $cat_name);
            }
        }
        $form->addElement($cat_select, true);

        // attachments
        $attachment_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_LETTER_ATTACHMENT, '<br>');
        $attachment_tray->addElement(new \XoopsFormHidden('deleted_attachment_id', ''));
        // existing_attachments
        if ($this->isNew()) {
            $attachmentObjs = [];
        } else {
            $attachmentCriteria = new \CriteriaCompo();
            $attachmentCriteria->add(new \Criteria('attachment_letter_id', $this->getVar('letter_id')));
            $attachmentCriteria->setSort('attachment_id');
            $attachmentCriteria->setOrder('ASC');
            $attachmentObjs = $this->helper->getHandler('Attachment')->getAll($attachmentCriteria);
        }
        $i                      = 1;
        $remove_attachment_tray = [];
        foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
            $delete_attachment_tray = new \XoopsFormElementTray('', '&nbsp;&nbsp;');
            $mode_select            = new \XoopsFormRadio(_AM_XNEWSLETTER_ATTACHMENT_MODE, "existing_attachments_mode[{$attachment_id}]", $attachmentObj->getVar('attachment_mode'), '&nbsp;');
            $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASATTACHMENT);
            $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASLINK, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASLINK);
            //$mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_AUTO, _AM_XNEWSLETTER_ATTACHMENT_MODE_AUTO); // for future features
            $delete_attachment_tray->addElement($mode_select);
            $delete_attachment_tray->addElement(new \XoopsFormLabel('', $attachmentObj->getVar('attachment_name')));
            $delete_button = new \XoopsFormButton('', "delete_attachment_{$i}", _DELETE, 'submit');
            $delete_button->setExtra("onclick='this.form.elements.op.value=\"delete_attachment\";this.form.elements.deleted_attachment_id.value=\"{$attachment_id}\";'");
            $delete_attachment_tray->addElement($delete_button);
            $attachment_tray->addElement($delete_attachment_tray);
            ++$i;
            unset($mode_select);
            unset($delete_attachment_tray);
        }
        // new_attachments
        for ($j = $i; $j < ($this->helper->getConfig('xn_maxattachments') + 1); ++$j) {
            $add_attachment_tray = new \XoopsFormElementTray('', '&nbsp;&nbsp;');
            $mode_select         = new \XoopsFormRadio(_AM_XNEWSLETTER_ATTACHMENT_MODE, "new_attachments_mode[{$j}]", _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, '&nbsp;');
            $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASATTACHMENT);
            $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASLINK, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASLINK);
            //$mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_AUTO, _AM_XNEWSLETTER_ATTACHMENT_MODE_AUTO); // for future features
            $add_attachment_tray->addElement($mode_select);
            $add_attachment_tray->addElement(new \XoopsFormFile('', "new_attachment_index={$j}", $this->helper->getConfig('xn_maxsize')));
            $attachment_tray->addElement($add_attachment_tray);
            unset($mode_select);
            unset($add_attachment_tray);
        }
        $form->addElement($attachment_tray);

        // letter_action
        $opt_nextaction = new \XoopsFormRadio('', 'letter_action', _AM_XNEWSLETTER_LETTER_ACTION_NO, '<br>');
        $opt_nextaction->addOption(_XNEWSLETTER_LETTER_ACTION_VAL_NO, _AM_XNEWSLETTER_LETTER_ACTION_NO);
        $opt_nextaction->addOption(_XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW, _AM_XNEWSLETTER_LETTER_ACTION_PREVIEW);
        $opt_nextaction->addOption(_XNEWSLETTER_LETTER_ACTION_VAL_SEND, _AM_XNEWSLETTER_LETTER_ACTION_SEND);
        $opt_nextaction->addOption(_XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST, _AM_XNEWSLETTER_LETTER_ACTION_SENDTEST);
        $opt_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_LETTER_ACTION, '<br>');
        $opt_tray->addElement($opt_nextaction);

        // letter_email_test
        $letter_email_test = $this->isNew() ? $xoopsUser->email() : $this->getVar('letter_email_test');
        if ('' == $letter_email_test) {
            $letter_email_test = $xoopsUser->email();
        }
        $opt_tray->addElement(new \XoopsFormText(_AM_XNEWSLETTER_LETTER_EMAIL_TEST . ':&nbsp;', 'letter_email_test', 50, 255, $letter_email_test), false);
        $form->addElement($opt_tray);

        // letter_account
        $accountsCriteria = new \CriteriaCompo();
        $accountsCriteria->setSort('accounts_id');
        $accountsCriteria->setOrder('ASC');
        $accountsCount   = $this->helper->getHandler('Accounts')->getCount($accountsCriteria);
        $account_default = 0;
        if ($this->isNew()) {
            $accountsObjs = $this->helper->getHandler('Accounts')->getAll($accountsCriteria);
            foreach ($accountsObjs as $accountsObj) {
                if (1 == $accountsObj->getVar('accounts_default')) {
                    $account_default = $accountsObj->getVar('accounts_id');
                }
            }
        } else {
            $account_default = $this->getVar('letter_account');
        }
        if (1 == $accountsCount) {
            $form->addElement(new \XoopsFormHidden('letter_account', $account_default));
        } else {
            $accounts_list = $this->helper->getHandler('Accounts')->getList($accountsCriteria);

            if (true === $admin_aerea) {
                $opt_accounts = new \XoopsFormRadio(_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL, 'letter_account', $account_default);
                $opt_accounts->addOptionArray($accounts_list);
                $form->addElement($opt_accounts, false);
            } else {
                $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL, $accounts_list[$account_default]));
                $form->addElement(new \XoopsFormHidden('letter_account', $account_default));
            }
        }

        if ($this->isNew()) {
            $time           = time();
            $submitter_uid  = $GLOBALS['xoopsUser']->uid();
            $submitter_name = $GLOBALS['xoopsUser']->uname();
        } else {
            $time          = $this->getVar('letter_created');
            $submitter_uid = $this->getVar('letter_submitter');
            xoops_load('xoopsuserutility');
            $submitter_name = \XoopsUserUtility::getUnameFromId($submitter_uid);
        }

        $form->addElement(new \XoopsFormHidden('letter_submitter', $submitter_uid));
        $form->addElement(new \XoopsFormHidden('letter_created', $time));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $submitter_name));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new \XoopsFormHidden('op', 'save_letter'));
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
    public function getValuesLetter($keys = null, $format = null, $maxDepth = null)
    {
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id']             = $this->getVar('letter_id');
        $ret['title']          = $this->getVar('letter_title');
        $ret['content']        = $this->getVar('letter_content');
        $ret['templateid']     = $this->getVar('letter_templateid');
        $templateObj           = $this->helper->getHandler('Template')->get($this->getVar('letter_templateid'));
        if (is_object($templateObj)) {
            $ret['template_title'] = $templateObj->getVar('template_title');
        }
        $ret['cats']           = $this->getVar('letter_cats');
        $ret['attachment']     = $this->getVar('letter_attachment');
        $ret['account']        = $this->getVar('letter_account');
        $ret['email_test']     = $this->getVar('letter_email_test');
        $ret['sender']         = $this->getVar('letter_sender') > 0 ? \XoopsUser::getUnameFromId($this->getVar('letter_sender')) : '-';
        $ret['sent']           = $this->getVar('letter_sent') > 1 ? formatTimestamp($this->getVar('letter_sent'), 's') : '-';
        $ret['created']        = formatTimestamp($this->getVar('letter_created'), 's');
        $ret['submitter']      = \XoopsUser::getUnameFromId($this->getVar('letter_submitter'));
        return $ret;
    }

}
