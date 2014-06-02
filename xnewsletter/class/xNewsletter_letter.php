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
 *  @package    xNewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : $Id $
 * ****************************************************************************
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");
include_once dirname(dirname(__FILE__)) . '/include/common.php';
class xnewsletter_letter extends XoopsObject
{
    public $xnewsletter = null;

    //Constructor
    public function __construct()
    {
        $this->xnewsletter = xNewsletterxNewsletter::getInstance();
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar("letter_id", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("letter_title", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("letter_content", XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar("letter_template", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("letter_cats", XOBJ_DTYPE_TXTBOX, null, false, 100); // IN PROGRESS: AN ARRAY SHOULD BE BETTER
        $this->initVar("letter_attachment", XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar("letter_account", XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar("letter_email_test", XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar("letter_submitter", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar("letter_created", XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('att_to_add', XOBJ_DTYPE_ARRAY, array() );
    }

    public function getForm($action = false, $admin_aerea = false)
    {
        global $xoopsDB, $xoopsModule, $xoopsUser, $pathImageIcon;

        if ($action === false) {
            $action = $_SERVER["REQUEST_URI"];
        }

        //read available templates
        $template_path = XOOPS_ROOT_PATH.'/modules/xNewsletter/language/'.$GLOBALS['xoopsConfig']['language'].'/templates/';
        if (!is_dir($template_path)) $template_path = XOOPS_ROOT_PATH.'/modules/xNewsletter/language/english/templates/';
        $exempted = "index.html";
        $arr_templates = array();
        $template_dir = @opendir($template_path) or die(str_replace("%p",$template_path, _AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH));
        while ($filename = readdir($template_dir)) {
            if (($filename!=".") and ($filename!="..") and ($filename!=$exempted )) {
                $info = pathinfo($filename);
                $arr_templates[] =  basename($filename,'.'.$info['extension']);
            }
        }
        closedir($template_dir);

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_LETTER_ADD) : sprintf(_AM_XNEWSLETTER_LETTER_EDIT);

        include_once(XOOPS_ROOT_PATH . "/class/xoopsformloader.php");
        $form = new XoopsThemeForm($title, "form", $action, "post", true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormText(_AM_XNEWSLETTER_LETTER_TITLE, "letter_title", 50, 255, $this->getVar("letter_title", 'e')), true);

        $editor_configs=array();
        $editor_configs["name"] ="letter_content";
        $editor_configs["value"] = $this->getVar("letter_content", "e");
        $editor_configs["rows"] = 10;
        $editor_configs["cols"] = 80;
        $editor_configs["width"] = "100%";
        $editor_configs["height"] = "400px";
        $editor_configs["editor"] = $this->xnewsletter->getConfig('xnewsletter_editor');
        $form->addElement( new XoopsFormEditor(_AM_XNEWSLETTER_LETTER_CONTENT, "letter_content", $editor_configs), true);

        $letter_template = $this->getVar("letter_template")=="" ? "basic" : $this->getVar("letter_template");

        $template_select = new XoopsFormSelect(_AM_XNEWSLETTER_LETTER_TEMPLATE, "letter_template", $letter_template);
        foreach ($arr_templates as $template) {
            $template_select->addOption($template, $template) ;
        }

        $form->addElement($template_select, false);

        $gperm_handler =& xoops_gethandler('groupperm');
        $member_handler =& xoops_gethandler('member');
        $my_group_ids = $member_handler->getGroupsByUser($xoopsUser->uid());

        $crit_cat = new CriteriaCompo();
        $crit_cat->setSort('cat_id');
        $crit_cat->setOrder('ASC');
        $letter_cats = explode("|", $this->getVar("letter_cats"));
        $cat_select = new XoopsFormCheckBox(_AM_XNEWSLETTER_LETTER_CATS, "letter_cats", $letter_cats);
        $cat_arr = $this->xnewsletter->getHandler('xNewsletter_cat')->getall($crit_cat);
        foreach (array_keys($cat_arr) as $i) {
            $cat_id = $cat_arr[$i]->getVar("cat_id");
            $cat_name = $cat_arr[$i]->getVar("cat_name");
            $show = $gperm_handler->checkRight('newsletter_create_cat', $cat_id, $my_group_ids, $xoopsModule->mid());
            if ($show == 1) $cat_select->addOption($cat_id, $cat_name);
        }
        $form->addElement($cat_select,true);

        $att_tray = new XoopsFormElementTray(_AM_XNEWSLETTER_LETTER_ATTACHMENT, '<br>');
        if ($this->isNew()) {
            $attachment_arr = array();
        } else {
            $crit_att = new CriteriaCompo();
            $crit_att->add(new Criteria('attachment_letter_id', $this->getVar("letter_id")));
            $crit_att->setSort("attachment_id");
            $crit_att->setOrder("ASC");
            $attachment_arr = $this->xnewsletter->getHandler('xNewsletter_attachment')->getall($crit_att);
        }
        $i = 1;
        $remove_att_tray = array();
        foreach (array_keys($attachment_arr) as $att) {
            $remove_att_tray[$att] = new XoopsFormElementTray("",'&nbsp;&nbsp;');
            $remove_att_tray[$att]->addElement(new XoopsFormLabel("", $attachment_arr[$att]->getVar("attachment_name")));
            $remove_att_tray[$att]->addElement(new XoopsFormButton("", "delete_attachment_" . $i, _DELETE, "submit"));
            $remove_att_tray[$att]->addElement(new XoopsFormHidden("attachment_" . $i, $att));
            $att_tray->addElement($remove_att_tray[$att]);
            $i++;
        }
        //$add_att_tray = array();
        for ($j = $i ;$j < 6; $j++) {
            $att_tray->addElement(new XoopsFormFile("", "letter_attachment_" . $j, $this->xnewsletter->getConfig('xn_maxsize')));
        }
        $form->addElement($att_tray);

        $opt_nextaction = new XoopsFormRadio("", "letter_action", "0", "<br />");
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_NO, _AM_XNEWSLETTER_LETTER_ACTION_NO);
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_PREVIEW, _AM_XNEWSLETTER_LETTER_ACTION_PREVIEW);
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_SEND, _AM_XNEWSLETTER_LETTER_ACTION_SEND);
        $opt_nextaction->addOption(_AM_XNEWSLETTER_LETTER_ACTION_VAL_SENDTEST, _AM_XNEWSLETTER_LETTER_ACTION_SENDTEST);
        $opt_tray = new XoopsFormElementTray(_AM_XNEWSLETTER_LETTER_ACTION, '<br/>');
        $opt_tray->addElement($opt_nextaction);

        $letter_email_test = $this->isNew() ? $xoopsUser->email() : $this->getVar("letter_email_test");
        if ($letter_email_test =='') $letter_email_test = $xoopsUser->email();
        $opt_tray->addElement(new XoopsFormText(_AM_XNEWSLETTER_LETTER_EMAIL_TEST . ":&nbsp;", "letter_email_test", 50, 255, $letter_email_test), false);
        $form->addElement($opt_tray);

        $crit_accounts = new CriteriaCompo();
        $crit_accounts->setSort("accounts_id");
        $crit_accounts->setOrder("ASC");
        $numrows_accounts = $this->xnewsletter->getHandler('xNewsletter_accounts')->getCount($crit_accounts);
        $account_default = 0;
        if ($this->isNew()) {
            $accounts_arr = $this->xnewsletter->getHandler('xNewsletter_accounts')->getall($crit_accounts);
            foreach ($accounts_arr as $account) {
                if ($account->getVar("accounts_default") == 1) $account_default = $account->getVar("accounts_id");
            }
        } else {
            $account_default =  $this->getVar("letter_account");
        }
        if ($numrows_accounts == 1) {
            $form->addElement(new XoopsFormHidden("letter_account", $account_default));
        } else {
            $accounts_list = $this->xnewsletter->getHandler('xNewsletter_accounts')->getList($crit_accounts);

            if ($admin_aerea == true) {
                $opt_accounts = new XoopsFormRadio(_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL, "letter_account", $account_default);
                $opt_accounts->addOptionArray($accounts_list);
                $form->addElement($opt_accounts, false);
            } else {
                $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL, $accounts_list[$account_default]));
                $form->addElement(new XoopsFormHidden("letter_account", $account_default));
            }
        }

        if ($this->isNew()) {
            $time = time();
            $submitter_uid = $GLOBALS['xoopsUser']->uid();
            $submitter_name = $GLOBALS['xoopsUser']->uname();
        } else {
            $time = $this->getVar("letter_created");
            $submitter_uid = $this->getVar("letter_submitter");
            xoops_load("xoopsuserutility");
            $submitter_name = XoopsUserUtility::getUnameFromId($submitter_uid);
        }

        $form->addElement(new XoopsFormHidden("letter_submitter", $submitter_uid));
        $form->addElement(new XoopsFormHidden("letter_created", $time));

        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_LETTER_SUBMITTER, $submitter_name));
        $form->addElement(new XoopsFormLabel(_AM_XNEWSLETTER_LETTER_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new XoopsFormHidden("op", "save_letter"));
        $form->addElement(new XoopsFormButton("", "submit", _AM_XNEWSLETTER_SAVE, "submit"));

        return $form;
    }
}

class xNewsletterxnewsletter_letterHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var xNewsletterxNewsletter
     * @access public
     */
    public $xnewsletter = null;

    /**
     * @param null|object $db
     */
    public function __construct(&$db)
    {
        parent::__construct($db, "mod_xnewsletter_letter", "xnewsletter_letter", "letter_id", "letter_title");
        $this->xnewsletter = xNewsletterxNewsletter::getInstance();
    }
}
