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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class XnewsletterLetter
 */
class XnewsletterLetter extends XoopsObject
{
    public $xnewsletter = null;

    //Constructor
    /**
     *
     */
    public function __construct()
    {
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('letter_id', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('letter_title', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('letter_content', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('letter_template', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('letter_cats', XOBJ_DTYPE_TXTBOX, null, false, 100); // IN PROGRESS: AN ARRAY SHOULD BE BETTER
        $this->initVar('letter_attachment', XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar('letter_account', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('letter_email_test', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('letter_submitter', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('letter_created', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('att_to_add', XOBJ_DTYPE_ARRAY, []);
    }

    /**
     * @param bool $action
     * @param bool $admin_aerea
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false, $admin_aerea = false)
    {
        global $xoopsDB, $xoopsUser, $pathImageIcon;

        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_LETTER_ADD) : sprintf(_AM_XNEWSLETTER_LETTER_EDIT);

        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_LETTER_TITLE, 'letter_title', 50, 255, $this->getVar('letter_title', 'e')), true);

        $editor_configs           = [];
        $editor_configs['name']   = 'letter_content';
        $editor_configs['value']  = $this->getVar('letter_content', 'e');
        $editor_configs['rows']   = 10;
        $editor_configs['cols']   = 80;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';
        $editor_configs['editor'] = $this->xnewsletter->getConfig('xnewsletter_editor');
        $letter_content_editor    = new XoopsFormEditor(_AM_XNEWSLETTER_LETTER_CONTENT, 'letter_content', $editor_configs);
        $letter_content_editor->setDescription(_AM_XNEWSLETTER_LETTER_CONTENT_DESC);
        $form->addElement($letter_content_editor, true);

        $letter_template = $this->getVar('letter_template') == '' ? 'basic' : $this->getVar('letter_template');

        $template_select = new XoopsFormSelect(_AM_XNEWSLETTER_LETTER_TEMPLATE, 'letter_template', $letter_template);
        // get template files in /modules/xnewsletter/language/{language}/templates/ directory
        $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/' . $GLOBALS['xoopsConfig']['language'] . '/templates/';
        if (!is_dir($template_path)) {
            $template_path = XOOPS_ROOT_PATH . '/modules/xnewsletter/language/english/templates/';
        }
        $templateFiles = [];
        if (!$dirHandler = @opendir($template_path)) die(str_replace('%p', $template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH));
        while ($filename = readdir($dirHandler)) {
            if (($filename != '.') and ($filename != '..') and ($filename != 'index.html')) {
                $info            = pathinfo($filename);
                $templateFiles[] = basename($filename, '.' . $info['extension']);
            }
        }
        closedir($dirHandler);
        foreach ($templateFiles as $templateFile) {
            $template_select->addOption($templateFile, 'file:' . $templateFile);
        }
        // get template objects from database
        $templateCriteria = new CriteriaCompo();
        $templateCriteria->setSort('template_title DESC, template_id');
        $templateCriteria->setOrder('DESC');
        $templateCount = $this->xnewsletter->getHandler('template')->getCount();
        if($templateCount > 0) {
            $templateObjs = $this->xnewsletter->getHandler('template')->getAll($templateCriteria);
            foreach($templateObjs as $templateObj) {
                $template_select->addOption('db:' . $templateObj->getVar('template_id'), 'db:' . $templateObj->getVar('template_title'));
            }
        }
        $form->addElement($template_select, false);

        $gperm_handler  = xoops_getHandler('groupperm');
        $member_handler = xoops_getHandler('member');
        $my_group_ids   = $member_handler->getGroupsByUser($xoopsUser->uid());

        $catCriteria = new CriteriaCompo();
        $catCriteria->setSort('cat_id');
        $catCriteria->setOrder('ASC');
        $letter_cats = explode('|', $this->getVar('letter_cats'));
        $cat_select  = new XoopsFormCheckBox(_AM_XNEWSLETTER_LETTER_CATS, 'letter_cats', $letter_cats);
        $catObjs     = $this->xnewsletter->getHandler('cat')->getAll($catCriteria);
        foreach ($catObjs as $cat_id => $catObj) {
            $cat_name = $catObj->getVar('cat_name');
            $show     = $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $my_group_ids, $this->xnewsletter->getModule()->mid());
            if ($show == 1) {
                $cat_select->addOption($cat_id, $cat_name);
            }
        }
        $form->addElement($cat_select, true);

        $attachment_tray = new XoopsFormElementTray(_AM_XNEWSLETTER_LETTER_ATTACHMENT, '<br>');
        if ($this->isNew()) {
            $attachmentObjs = [];
        } else {
            $attachmentCriteria = new CriteriaCompo();
            $attachmentCriteria->add(new Criteria('attachment_letter_id', $this->getVar('letter_id')));
            $attachmentCriteria->setSort('attachment_id');
            $attachmentCriteria->setOrder('ASC');
            $attachmentObjs = $this->xnewsletter->getHandler('attachment')->getAll($attachmentCriteria);
        }
        $i = 1;
        $remove_attachment_tray = [];
        foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
            $remove_attachment_tray[$attachment_id] = new XoopsFormElementTray('', '&nbsp;&nbsp;');
            $remove_attachment_tray[$attachment_id]->addElement(new XoopsFormLabel('', $attachmentObj->getVar('attachment_name')));
            $remove_attachment_tray[$attachment_id]->addElement(new XoopsFormButton('', "delete_attachment_{$i}", _DELETE, 'submit'));
            $remove_attachment_tray[$attachment_id]->addElement(new XoopsFormHidden("attachment_{$i}", $attachment_id));
            $attachment_tray->addElement($remove_attachment_tray[$attachment_id]);
            ++$i;
        }
        //$add_att_tray = array();
        for ($j = $i; $j < 6; ++$j) {
            $attachment_tray->addElement(new XoopsFormFile('', "letter_attachment_{$j}", $this->xnewsletter->getConfig('xn_maxsize')));
        }
        $form->addElement($attachment_tray);

        $opt_nextaction = new XoopsFormRadio('', 'letter_action', '0', '<br />');
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_NO, _AM_XNEWSLETTER_LETTER_ACTION_NO);
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW, _AM_XNEWSLETTER_LETTER_ACTION_PREVIEW);
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_SEND, _AM_XNEWSLETTER_LETTER_ACTION_SEND);
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST, _AM_XNEWSLETTER_LETTER_ACTION_SENDTEST);
        $opt_tray = new XoopsFormElementTray(_AM_XNEWSLETTER_LETTER_ACTION, '<br/>');
        $opt_tray->addElement($opt_nextaction);

        $letter_email_test = $this->isNew() ? $xoopsUser->email() : $this->getVar('letter_email_test');
        if ($letter_email_test == '') {
            $letter_email_test = $xoopsUser->email();
        }
        $opt_tray->addElement(new XoopsFormText(_AM_XNEWSLETTER_LETTER_EMAIL_TEST . ':&nbsp;', 'letter_email_test', 50, 255, $letter_email_test), false);
        $form->addElement($opt_tray);

        $accountsCriteria = new CriteriaCompo();
        $accountsCriteria->setSort('accounts_id');
        $accountsCriteria->setOrder('ASC');
        $numrows_accounts = $this->xnewsletter->getHandler('accounts')->getCount($accountsCriteria);
        $account_default  = 0;
        if ($this->isNew()) {
            $accountsObjs = $this->xnewsletter->getHandler('accounts')->getAll($accountsCriteria);
            foreach ($accountsObjs as $accountsObj) {
                if ($accountsObj->getVar('accounts_default') == 1) {
                    $account_default = $accountsObj->getVar('accounts_id');
                }
            }
        } else {
            $account_default = $this->getVar('letter_account');
        }
        if ($numrows_accounts == 1) {
            $form->addElement(new XoopsFormHidden('letter_account', $account_default));
        } else {
            $accounts_list = $this->xnewsletter->getHandler('accounts')->getList($accountsCriteria);

            if ($admin_aerea == true) {
                $opt_accounts = new XoopsFormRadio(_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL, 'letter_account', $account_default);
                $opt_accounts->addOptionArray($accounts_list);
                $form->addElement($opt_accounts, false);
            } else {
                $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL, $accounts_list[$account_default]));
                $form->addElement(new XoopsFormHidden('letter_account', $account_default));
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
            $submitter_name = XoopsUserUtility::getUnameFromId($submitter_uid);
        }

        $form->addElement(new XoopsFormHidden('letter_submitter', $submitter_uid));
        $form->addElement(new XoopsFormHidden('letter_created', $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_LETTER_SUBMITTER, $submitter_name));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_LETTER_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new XoopsFormHidden('op', 'save_letter'));
        $form->addElement(new XoopsFormButton('', 'submit', _AM_XNEWSLETTER_SAVE, 'submit'));

        return $form;
    }
}

/**
 * Class XnewsletterLetterHandler
 */
class XnewsletterLetterHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var xnewsletterxnewsletter
     * @access public
     */
    public $xnewsletter = null;

    /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, 'xnewsletter_letter', 'XnewsletterLetter', 'letter_id', 'letter_title');
        $this->xnewsletter = xnewsletterxnewsletter::getInstance();
    }
}
