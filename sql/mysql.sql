#
# Table structure for table `xnewsletter_accounts` 19
#
CREATE TABLE `xnewsletter_accounts` (
  `accounts_id`             INT(8)       NOT NULL  AUTO_INCREMENT,
  `accounts_type`           INT(8)       NOT NULL  DEFAULT '0',
  `accounts_name`           VARCHAR(100) NOT NULL  DEFAULT ' ',
  `accounts_yourname`       VARCHAR(100) NOT NULL  DEFAULT ' ',
  `accounts_yourmail`       VARCHAR(100) NOT NULL  DEFAULT ' ',
  `accounts_username`       VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_password`       VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_server_in`      VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_port_in`        VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_securetype_in`  VARCHAR(20)  NULL      DEFAULT ' ',
  `accounts_server_out`     VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_port_out`       VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_securetype_out` TEXT         NULL,
  `accounts_default`        TINYINT(1)   NOT NULL  DEFAULT '0',
  `accounts_use_bmh`        TINYINT(1)   NOT NULL  DEFAULT '0',
  `accounts_inbox`          VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_hardbox`        VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_movehard`       TINYINT(1)   NULL      DEFAULT '0',
  `accounts_softbox`        VARCHAR(100) NULL      DEFAULT ' ',
  `accounts_movesoft`       TINYINT(1)   NOT NULL  DEFAULT '0',
  `accounts_submitter`      INT(8)       NOT NULL  DEFAULT '0',
  `accounts_created`        INT(8)       NOT NULL  DEFAULT '0',
  PRIMARY KEY (`accounts_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_cat` 7
#
CREATE TABLE `xnewsletter_cat` (
  `cat_id`          INT(8)       NOT NULL  AUTO_INCREMENT,
  `cat_name`        VARCHAR(100) NOT NULL  DEFAULT ' ',
  `cat_info`        TEXT,
  `cat_mailinglist` INT(8)       NOT NULL  DEFAULT '0',
  `cat_submitter`   INT(8)       NOT NULL  DEFAULT '0',
  `cat_created`     INT(8)       NOT NULL  DEFAULT '0',
  PRIMARY KEY (`cat_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_subscr` 10
#
CREATE TABLE `xnewsletter_subscr` (
  `subscr_id`         INT(8)       NOT NULL  AUTO_INCREMENT,
  `subscr_email`      VARCHAR(100) NOT NULL  DEFAULT ' ',
  `subscr_firstname`  VARCHAR(100) NOT NULL  DEFAULT ' ',
  `subscr_lastname`   VARCHAR(100) NOT NULL  DEFAULT ' ',
  `subscr_uid`        INT(10)      NOT NULL  DEFAULT '0',
  `subscr_sex`        VARCHAR(100) NULL      DEFAULT ' ',
  `subscr_submitter`  INT(8)       NOT NULL  DEFAULT '0',
  `subscr_created`    INT(8)       NOT NULL  DEFAULT '0',
  `subscr_actkey`     VARCHAR(255) NOT NULL  DEFAULT '',
  `subscr_actoptions` TEXT,
  `subscr_ip`         VARCHAR(32)  NOT NULL  DEFAULT '',
  `subscr_activated`  INT(8)       NOT NULL,
  PRIMARY KEY (`subscr_id`),
  KEY `idx_subscr_email` (`subscr_email`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_catsubscr` 6
#
CREATE TABLE `xnewsletter_catsubscr` (
  `catsubscr_id`        INT(8)  NOT NULL  AUTO_INCREMENT,
  `catsubscr_catid`     INT(8)  NOT NULL  DEFAULT '0',
  `catsubscr_subscrid`  INT(8)  NOT NULL  DEFAULT '0',
  `catsubscr_quited`    INT(10) NOT NULL  DEFAULT '0',
  `catsubscr_submitter` INT(8)  NOT NULL  DEFAULT '0',
  `catsubscr_created`   INT(8)  NOT NULL  DEFAULT '0',
  PRIMARY KEY (`catsubscr_id`),
  UNIQUE KEY `idx_subscription` (`catsubscr_catid`, `catsubscr_subscrid`),
  KEY `idx_catsubscr_catid` (`catsubscr_catid`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_letter` 9
#
CREATE TABLE `xnewsletter_letter` (
  `letter_id`         INT(8)       NOT NULL  AUTO_INCREMENT,
  `letter_title`      VARCHAR(100) NOT NULL  DEFAULT ' ',
  `letter_content`    TEXT         NOT NULL,
  `letter_template`   VARCHAR(100) NOT NULL  DEFAULT ' ',
  `letter_cats`       VARCHAR(100) NULL      DEFAULT ' ',
  `letter_account`    INT(8)       NOT NULL  DEFAULT '0',
  `letter_email_test` VARCHAR(100) NULL      DEFAULT ' ',
  `letter_submitter`  INT(8)       NOT NULL  DEFAULT '0',
  `letter_created`    INT(8)       NOT NULL  DEFAULT '0',
  PRIMARY KEY (`letter_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_protocol` 7
#
CREATE TABLE `xnewsletter_protocol` (
  `protocol_id`            INT(8)       NOT NULL  AUTO_INCREMENT,
  `protocol_letter_id`     INT(8)       NOT NULL  DEFAULT '0',
  `protocol_subscriber_id` INT(8)       NOT NULL  DEFAULT '0',
  `protocol_status`        VARCHAR(200) NULL      DEFAULT ' ',
  `protocol_submitter`     INT(8)       NOT NULL  DEFAULT '0',
  `protocol_created`       INT(8)       NOT NULL  DEFAULT '0',
  `protocol_success`       INT(8)       NOT NULL,
  PRIMARY KEY (`protocol_id`),
  KEY `idx_protocol_letter_id` (`protocol_letter_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_attachment` 6
#
CREATE TABLE `xnewsletter_attachment` (
  `attachment_id`        INT(8)       NOT NULL  AUTO_INCREMENT,
  `attachment_letter_id` INT(8)       NOT NULL  DEFAULT '0',
  `attachment_name`      VARCHAR(200) NULL      DEFAULT ' ',
  `attachment_type`      VARCHAR(100) NULL      DEFAULT ' ',
  `attachment_submitter` INT(8)       NOT NULL  DEFAULT '0',
  `attachment_created`   INT(8)       NOT NULL  DEFAULT '0',
  PRIMARY KEY (`attachment_id`),
  KEY `idx_attachment_letter_id` (`attachment_letter_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_mailinglist` 8
#
CREATE TABLE `xnewsletter_mailinglist` (
  `mailinglist_id`          INT(8)       NOT NULL  AUTO_INCREMENT,
  `mailinglist_name`        VARCHAR(100) NOT NULL  DEFAULT ' ',
  `mailinglist_email`       VARCHAR(100) NOT NULL  DEFAULT ' ',
  `mailinglist_listname`    VARCHAR(100) NOT NULL  DEFAULT ' ',
  `mailinglist_subscribe`   VARCHAR(100) NOT NULL  DEFAULT ' ',
  `mailinglist_unsubscribe` VARCHAR(100) NOT NULL  DEFAULT ' ',
  `mailinglist_submitter`   INT(10)      NOT NULL  DEFAULT '0',
  `mailinglist_created`     INT(10)      NOT NULL  DEFAULT '0',
  PRIMARY KEY (`mailinglist_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_bmh` 12
#
CREATE TABLE `xnewsletter_bmh` (
  `bmh_id`          INT(8)       NOT NULL  AUTO_INCREMENT,
  `bmh_accounts_id` INT(8)       NOT NULL  DEFAULT '0',
  `bmh_rule_no`     VARCHAR(10)  NOT NULL  DEFAULT ' ',
  `bmh_rule_cat`    VARCHAR(50)  NOT NULL  DEFAULT ' ',
  `bmh_bouncetype`  VARCHAR(50)  NOT NULL  DEFAULT ' ',
  `bmh_remove`      VARCHAR(50)  NULL      DEFAULT ' ',
  `bmh_email`       VARCHAR(100) NOT NULL  DEFAULT ' ',
  `bmh_subject`     VARCHAR(100) NULL      DEFAULT ' ',
  `bmh_measure`     INT(10)      NOT NULL  DEFAULT '0',
  `bmh_submitter`   INT(10)      NOT NULL  DEFAULT '0',
  `bmh_created`     INT(10)      NOT NULL  DEFAULT '0',
  PRIMARY KEY (`bmh_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_import` 8
#
CREATE TABLE `xnewsletter_import` (
  `import_id`           INT(8)       NOT NULL  AUTO_INCREMENT,
  `import_email`        VARCHAR(100) NOT NULL  DEFAULT ' ',
  `import_firstname`    VARCHAR(100) NULL      DEFAULT ' ',
  `import_lastname`     VARCHAR(100) NULL      DEFAULT ' ',
  `import_sex`          VARCHAR(100) NULL      DEFAULT ' ',
  `import_cat_id`       INT(8)       NOT NULL  DEFAULT '0',
  `import_subscr_id`    INT(8)       NOT NULL  DEFAULT '0',
  `import_catsubscr_id` INT(8)       NOT NULL  DEFAULT '0',
  `import_status`       TINYINT(1)   NOT NULL  DEFAULT '0',
  PRIMARY KEY (`import_id`),
  KEY `idx_import_email` (`import_email`),
  KEY `idx_import_subscr_id` (`import_subscr_id`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_task` 6
#
CREATE TABLE `xnewsletter_task` (
  `task_id`        INT(8) NOT NULL AUTO_INCREMENT,
  `task_letter_id` INT(8) NOT NULL DEFAULT '0',
  `task_subscr_id` INT(8) NOT NULL DEFAULT '0',
  `task_starttime` INT(8) NOT NULL DEFAULT '0',
  `task_submitter` INT(8) NOT NULL DEFAULT '0',
  `task_created`   INT(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `idx_task_starttime` (`task_starttime`)
)
  ENGINE = MyISAM;

#
# Table structure for table `xnewsletter_template` 5
#
CREATE TABLE `xnewsletter_template` (
  `template_id`          INT(8)       NOT NULL  AUTO_INCREMENT,
  `template_title`       VARCHAR(100) NOT NULL  DEFAULT '',
  `template_description` TEXT         NOT NULL,
  `template_content`     TEXT         NOT NULL,
  `template_submitter`   INT(8)       NOT NULL  DEFAULT '0',
  `template_created`     INT(8)       NOT NULL  DEFAULT '0',
  PRIMARY KEY (`template_id`)
)
  ENGINE = MyISAM;
