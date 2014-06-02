#
# Table structure for table `mod_xnewsletter_accounts` 19
#
CREATE TABLE  `mod_xnewsletter_accounts` (
    `accounts_id` int (8)   NOT NULL  auto_increment,
    `accounts_type` int (8)   NOT NULL default '0',
    `accounts_name` varchar (100)   NOT NULL default ' ',
    `accounts_yourname` varchar (100)   NOT NULL default ' ',
    `accounts_yourmail` varchar (100)   NOT NULL default ' ',
    `accounts_username` varchar (100)   NULL default ' ',
    `accounts_password` varchar (100)   NULL default ' ',
    `accounts_server_in` varchar (100)   NULL default ' ',
    `accounts_port_in` varchar (100)   NULL default ' ',
    `accounts_securetype_in` varchar (20)   NULL default ' ',
    `accounts_server_out` varchar (100)   NULL default ' ',
    `accounts_port_out` varchar (100)   NULL default ' ',
    `accounts_securetype_out` text   NULL ,
    `accounts_default` tinyint (1)   NOT NULL default '0',
    `accounts_use_bmh` tinyint (1)   NOT NULL default '0',
    `accounts_inbox` varchar (100)   NULL default ' ',
    `accounts_hardbox` varchar (100)   NULL default ' ',
    `accounts_movehard` tinyint (1)   NULL default '0',
    `accounts_softbox` varchar (100)   NULL default ' ',
    `accounts_movesoft` tinyint (1)   NOT NULL default '0',
    `accounts_submitter` int (8)   NOT NULL default '0',
    `accounts_created` int (8)   NOT NULL default '0',
    PRIMARY KEY (`accounts_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_cat` 7
#
CREATE TABLE  `mod_xnewsletter_cat` (
    `cat_id` int (8)   NOT NULL  auto_increment,
    `cat_name` varchar (100)   NOT NULL default ' ',
    `cat_info` text,
    `cat_mailinglist` int (8)   NOT NULL default '0',
    `cat_submitter` int (8)   NOT NULL default '0',
    `cat_created` int (8)   NOT NULL default '0',
    PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_subscr` 10
#
CREATE TABLE  `mod_xnewsletter_subscr` (
    `subscr_id` int (8)   NOT NULL  auto_increment,
    `subscr_email` varchar (100)   NOT NULL default ' ',
    `subscr_firstname` varchar (100)   NOT NULL default ' ',
    `subscr_lastname` varchar (100)   NOT NULL default ' ',
    `subscr_uid` int ( 10)   NOT NULL default '0',
    `subscr_sex` varchar ( 100)   NULL default ' ',
    `subscr_submitter` int (8)   NOT NULL default '0',
    `subscr_created` int (8)   NOT NULL default '0',
    `subscr_actkey` varchar (255)   NOT NULL default '',
    `subscr_actoptions` text,
    `subscr_ip` varchar(32) NOT NULL default '',
    `subscr_activated` int(8) NOT NULL,
    PRIMARY KEY (`subscr_id`),
    KEY `idx_subscr_email` (`subscr_email`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_catsubscr` 6
#
CREATE TABLE  `mod_xnewsletter_catsubscr` (
    `catsubscr_id` int (8)   NOT NULL  auto_increment,
    `catsubscr_catid` int (8)   NOT NULL default '0',
    `catsubscr_subscrid` int (8)   NOT NULL default '0',
    `catsubscr_quited` int (10)   NOT NULL default '0',
    `catsubscr_submitter` int (8)   NOT NULL default '0',
    `catsubscr_created` int (8)   NOT NULL default '0',
    PRIMARY KEY (`catsubscr_id`),
    UNIQUE KEY `idx_subscription` (`catsubscr_catid`,`catsubscr_subscrid`),
    KEY `idx_catsubscr_catid` (`catsubscr_catid`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_letter` 9
#
CREATE TABLE  `mod_xnewsletter_letter` (
    `letter_id` int (8)   NOT NULL  auto_increment,
    `letter_title` varchar (100)   NOT NULL default ' ',
    `letter_content` text   NOT NULL ,
    `letter_template` varchar (100)   NOT NULL default ' ',
    `letter_cats` varchar (100)   NULL default ' ',
    `letter_account` int (8)   NOT NULL default '0',
    `letter_email_test` varchar (100)   NULL default ' ',
    `letter_submitter` int (8)   NOT NULL default '0',
    `letter_created` int (8)   NOT NULL default '0',
    PRIMARY KEY (`letter_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_protocol` 7
#
CREATE TABLE  `mod_xnewsletter_protocol` (
    `protocol_id` int (8)   NOT NULL  auto_increment,
    `protocol_letter_id` int (8)   NOT NULL default '0',
    `protocol_subscriber_id` int (8)   NOT NULL default '0',
    `protocol_status` varchar (200)   NULL default ' ',
    `protocol_submitter` int (8)   NOT NULL default '0',
    `protocol_created` int (8)   NOT NULL default '0',
    `protocol_success` INT( 8 ) NOT NULL,
    PRIMARY KEY (`protocol_id`),
    KEY `idx_protocol_letter_id` (`protocol_letter_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_attachment` 6
#
CREATE TABLE  `mod_xnewsletter_attachment` (
    `attachment_id` int (8)   NOT NULL  auto_increment,
    `attachment_letter_id` int (8)   NOT NULL default '0',
    `attachment_name` varchar (200)   NULL default ' ',
    `attachment_type` varchar (100)   NULL default ' ',
    `attachment_submitter` int (8)   NOT NULL default '0',
    `attachment_created` int (8)   NOT NULL default '0',
    PRIMARY KEY (`attachment_id`),
    KEY `idx_attachment_letter_id` (`attachment_letter_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_mailinglist` 8
#
CREATE TABLE  `mod_xnewsletter_mailinglist` (
    `mailinglist_id` int (8)   NOT NULL  auto_increment,
    `mailinglist_name` varchar (100)   NOT NULL default ' ',
    `mailinglist_email` varchar (100)   NOT NULL default ' ',
    `mailinglist_listname` varchar (100)   NOT NULL default ' ',
    `mailinglist_subscribe` varchar (100)   NOT NULL default ' ',
    `mailinglist_unsubscribe` varchar (100)   NOT NULL default ' ',
    `mailinglist_submitter` int (10)   NOT NULL default '0',
    `mailinglist_created` int (10)   NOT NULL default '0',
    PRIMARY KEY (`mailinglist_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_bmh` 12
#
CREATE TABLE  `mod_xnewsletter_bmh` (
    `bmh_id` int (8)   NOT NULL  auto_increment,
    `bmh_accounts_id` int (8)   NOT NULL default '0',
    `bmh_rule_no` varchar ( 10)   NOT NULL default ' ',
    `bmh_rule_cat` varchar ( 50)   NOT NULL default ' ',
    `bmh_bouncetype` varchar ( 50)   NOT NULL default ' ',
    `bmh_remove` varchar ( 50)   NULL default ' ',
    `bmh_email` varchar ( 100)   NOT NULL default ' ',
    `bmh_subject` varchar (100)   NULL default ' ',
    `bmh_measure` int (10)   NOT NULL default '0',
    `bmh_submitter` int (10)   NOT NULL default '0',
    `bmh_created` int (10)   NOT NULL default '0',
    PRIMARY KEY (`bmh_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_import` 8
#
CREATE TABLE `mod_xnewsletter_import` (
    `import_id` int (8)   NOT NULL  auto_increment,
    `import_email` varchar (100)   NOT NULL default ' ',
    `import_firstname` varchar (100)   NULL default ' ',
    `import_lastname` varchar (100)   NULL default ' ',
    `import_sex` varchar (100)   NULL default ' ',
    `import_cat_id` int (8)   NOT NULL default '0',
    `import_subscr_id` int (8)   NOT NULL default '0',
    `import_catsubscr_id` int (8)   NOT NULL default '0',
    `import_status` tinyint (1)   NOT NULL default '0',
    PRIMARY KEY (`import_id`),
    KEY `idx_import_email` (`import_email`),
    KEY `idx_import_subscr_id` (`import_subscr_id`)
) ENGINE=MyISAM;



#
# Table structure for table `mod_xnewsletter_task` 6
#
CREATE TABLE `mod_xnewsletter_task` (
    `task_id` int(8) NOT NULL AUTO_INCREMENT,
    `task_letter_id` int(8) NOT NULL DEFAULT '0',
    `task_subscr_id` int(8) NOT NULL DEFAULT '0',
    `task_starttime` int(8) NOT NULL DEFAULT '0',
    `task_submitter` int(8) NOT NULL DEFAULT '0',
    `task_created` int(8) NOT NULL DEFAULT '0',
    PRIMARY KEY (`task_id`),
    KEY `idx_task_starttime` (`task_starttime`)
) ENGINE=MyISAM;
