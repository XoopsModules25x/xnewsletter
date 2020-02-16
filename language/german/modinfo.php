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
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */
// Admin
define('_MI_XNEWSLETTER_NAME', 'xNewsletter');
define('_MI_XNEWSLETTER_DESC', 'Newsletter-Modul für Xoops');
//Menu
define('_MI_XNEWSLETTER_ADMENU1', 'Übersicht');
define('_MI_XNEWSLETTER_ADMENU2', 'Sender-Konten');
define('_MI_XNEWSLETTER_ADMENU3', 'Newsletter-Kategorien');
define('_MI_XNEWSLETTER_ADMENU4', 'Abonnenten');
define('_MI_XNEWSLETTER_ADMENU5', 'Abonnenten / Kategorien');
define('_MI_XNEWSLETTER_ADMENU6', 'Newsletter');
define('_MI_XNEWSLETTER_ADMENU7', 'Anhänge');
define('_MI_XNEWSLETTER_ADMENU8', 'Protokolle');
define('_MI_XNEWSLETTER_ADMENU9', 'Mailinglisten');
define('_MI_XNEWSLETTER_ADMENU10', 'Bounced-Mail');
define('_MI_XNEWSLETTER_ADMENU11', 'Wartung');
define('_MI_XNEWSLETTER_ADMENU12', 'Import');
define('_MI_XNEWSLETTER_ADMENU13', 'Aufgabenliste');
define('_MI_XNEWSLETTER_ADMENU99', 'Über');
define('_MI_XNEWSLETTER_SUBSCRIBE', 'An-/Abmelden');
define('_MI_XNEWSLETTER_LIST', 'Liste der Newsletter');
define('_MI_XNEWSLETTER_LIST_SUBSCR', 'Liste der Abonnenten');
define('_MI_XNEWSLETTER_CREATE', 'Newsletter erstellen');
//Blocks
define('_MI_XNEWSLETTER_CATSUBSCR_BLOCK_RECENT', 'Aktuelle Anmeldungen');
define('_MI_XNEWSLETTER_CATSUBSCR_BLOCK_DAY', 'Heutige Anmeldungen');
define('_MI_XNEWSLETTER_LETTER_BLOCK_RECENT', 'Aktuelle Newsletter');
define('_MI_XNEWSLETTER_LETTER_BLOCK_DAY', 'Heutige Newsletter');
define('_MI_XNEWSLETTER_LETTER_BLOCK_RANDOM', 'Zufällige Newsletter');
//Config
define('_MI_XNEWSLETTER_EDITOR', 'Editor');
define('_MI_XNEWSLETTER_KEYWORDS', 'Keywords');
define('_MI_XNEWSLETTER_KEYWORDS_DESC', 'Keywords eingeben (getrennt mit einem Komma)');
define('_MI_XNEWSLETTER_ADMINPERPAGE', 'Anzahl Listeneinträge bei Admin-Seiten');
define('_MI_XNEWSLETTER_ADMINPERPAGE_DESC', 'Legen Sie bitte fest, wieviele Einträge die Listen auf den Admin-Seiten haben sollen.');
define('_MI_XNEWSLETTER_ADVERTISE', 'Code für Werbung');
define('_MI_XNEWSLETTER_ADVERTISE_DESC', 'Bitte Code für Ihre Werbung hier eingeben');
define('_MI_XNEWSLETTER_SOCIALACTIVE', 'Socialnetworks anzeigen?');
define('_MI_XNEWSLETTER_SOCIALACTIVE_DESC', "Wenn Schaltflächen für Soziale Netzwerke anzeigen wollen, wählen Sie bitte 'Ja'");
define('_MI_XNEWSLETTER_SOCIALCODE', 'Code für Schaltflächen für Soziale Netzwerke');
define('_MI_XNEWSLETTER_SOCIALCODE_DESC', 'Bitte Code für Schaltflächen für Soziale Netzwerke hier eingeben');
define('_MI_XNEWSLETTER_ATTACHMENT_MAXSIZE', 'Maximale Dateigröße');
define('_MI_XNEWSLETTER_ATTACHMENT_MAXSIZE_DESC', 'Bitte hier die maximale Dateigröße für Newsletteranhänge eingeben');
define('_MI_XNEWSLETTER_ATTACHMENT_MIMETYPES', 'Mime-types');
define('_MI_XNEWSLETTER_ATTACHMENT_MIMETYPES_DESC', 'Bitte hier die erlaubten Dateitypen für Newsletteranhänge eingeben');
define('_MI_XNEWSLETTER_ATTACHMENT_PATH', 'Upload-Pfad');
define('_MI_XNEWSLETTER_ATTACHMENT_PATH_DESC', 'Pfad festlegen, wo die Newsletteranhänge gespeichert werden sollen ( Ordner nach {XOOPS_ROOT_PATH}/uploads ), <b>mit Slash am Anfang</b> und <b>Slash am Ende</b>.');
define('_MI_XNEWSLETTER_USE_MAILINGLIST', 'Verwende Zusatzfeature Mailinglisten');
define('_MI_XNEWSLETTER_USE_MAILINGLIST_DESC', 
        'Wenn Sie existierende Mailinglisten haben, können Sie An- und Abmeldungen von einem Newsletter mit einer Mailingliste synchronisieren. <b>xNewsletter kann keine Mailingliste erstellen</b>. Wenn Sie dieses Feature aktivieren, erhalten Sie einen zusätzlichen Tab auf der Admin-Seite von xNewsletter.');
define('_MI_XNEWSLETTER_GROUPS_WITHOUT_ACTKEY', 'Gruppen mit An-/Abmeldung ohne Bestätigungsmail');
define('_MI_XNEWSLETTER_GROUPS_WITHOUT_ACTKEY_DESC', 'Bestimmen Sie bitte die Gruppen, die An-/Abmeldungen für Newsletter direkt durchführen dürfen, ohne dass sie eine Bestätigungsmail zurücksenden müssen');
define('_MI_XNEWSLETTER_GROUPS_CHANGE_OTHER', 'Gruppen mit der Berechtigung zum Ändern von An-/Abmeldung');
define('_MI_XNEWSLETTER_GROUPS_CHANGE_OTHER_DESC', 
        'Bestimmen Sie bitte die Gruppen, die An-/Abmeldungen von anderen Personen bearbeiten dürfen. Ein Löschen der Registrierung ist nicht möglich. Diese Gruppen müssen auch die Berechtigung zum Auflisten der Abonnenten zu einer Newsletterkategorie haben. Diese Gruppen sollten außerdem die Berechtigung zum Erstellen eines Newsletters haben.');
define('_MI_XNEWSLETTER_USE_SALUTATION', "Feld 'Anrede' verwenden");
define('_MI_XNEWSLETTER_USE_SALUTATION_DESC', "Bitte festlegen, ob Anreden wie 'Herr', 'Frau',... verwendet werden sollen");
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES', 'E-Mails paketweise versenden');
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES_DESC', 'Anzahl der E-Mails, die in einem Paket gesammelt versendet werden sollen. 0 bedeutet, dass alle E-Mails immer sofort versendet werden. Sie können diese Option nur verwenden, wenn Sie Cronjobs von einem externen Programm aus starten können.');
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES_TIME', 'Zeitspanne für paketweises E-Mail versenden');
define('_MI_XNEWSLETTER_SEND_IN_PACKAGES_TIME_DESC', "Zeitspanne in Minuten,bis das nächste Paket versendet werden soll. Wird nur berücksichtigt, wenn 'E-Mails paketweise versenden' größer 0 ist.");
define('_MI_XNEWSLETTER_UPGRADEFAILED', 'Fehler beim Modulupdate');
// version 1.2
define('_MI_XNEWSLETTER_SUBSCRINFO_BLOCK', 'Info Newsletter');
define('_MI_XNEWSLETTER_SUBSCRINFO_TEXT_BLOCK', 'Wenn Sie aktuell informiert werden möchten, dann registrieren Sie sich bei unserem Newsletter');
// version 1.3
define('_MI_XNEWSLETTER_WELCOME_MESSAGE','Willkommensnachricht');
define('_MI_XNEWSLETTER_WELCOME_MESSAGE_DESC','Html Format');
define('_MI_XNEWSLETTER_WELCOME',
        '<h2>Willkommen bei unserem Newslettersystem</h2>Wir hoffen, das wir Sie damit immer auf dem aktuellen Stand halten. Seien Sie so frei und melden sich für den einen oder mehrere von unseren Newslettern an. Wenn Sie einen Newsletter nicht mehr erhalten wollen, können Sie sich hier auch auf einfachem Wege wieder abmelden. Sie können sich aber auch über einen Link, der bei jedem Newsletter angegeben ist, abmelden.');
define('_MI_XNEWSLETTER_DATEFORMAT', 'Zeitstempel');
define('_MI_XNEWSLETTER_DATEFORMATDSC', "Standardzeitstempel für das Modul-Frontend. <br> Weitere Infos hier: <a href='http://www.php.net/manual/en/function.date.php'>http://www.php.net/manual/en/function.date.php</a>");
define('_MI_XNEWSLETTER_CONFIRMATION_TIME', 'Zeitspanne Bestätigungsmail');
define('_MI_XNEWSLETTER_CONFIRMATION_TIME_DESC', 'Registierung mit Bestätigungsmail ist innerhalb folgenden Zeitraumes möglich...');
define('_MI_XNEWSLETTER_CONFIRMATION_TIME_0', 'Kein Limit');
define('_MI_XNEWSLETTER_CONFIRMATION_TIME_1', '1 Stunde');
define('_MI_XNEWSLETTER_CONFIRMATION_TIME_6', '6 Stunden');
define('_MI_XNEWSLETTER_CONFIRMATION_TIME_24', '1 Tag');
define('_MI_XNEWSLETTER_CONFIRMATION_TIME_48', '2 Tage');
define('_MI_XNEWSLETTER_MAXATTACHMENTS', 'Maximale Anzahl an Anhängen');
define('_MI_XNEWSLETTER_MAXATTACHMENTS_DESC', 'Standardwert ist 5');
//
define('_MI_XNEWSLETTER_ADMENU_TEMPLATES','Templates');
define('_MI_XNEWSLETTER_EDITOR_DESC','');
define('_MI_XNEWSLETTER_TEMPLATE_EDITOR','Templateeditor');
define('_MI_XNEWSLETTER_TEMPLATE_EDITOR_DESC','');

//1.3
//Help
define('_MI_XNEWSLETTER_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_XNEWSLETTER_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_XNEWSLETTER_BACK_2_ADMIN', 'Zurück zur Administration von ');
define('_MI_XNEWSLETTER_OVERVIEW', 'Übersicht');

//define('_MI_XNEWSLETTER_HELP_DIR', __DIR__);

//help multi-page
define('_MI_XNEWSLETTER_DISCLAIMER', 'Disclaimer');
define('_MI_XNEWSLETTER_LICENSE', 'License');
define('_MI_XNEWSLETTER_SUPPORT', 'Support');
define('_MI_XNEWSLETTER_INSTALL', 'Installation');

//1.41
define('_MI_XNEWSLETTER_CRON_PROTOCOL', 'Level der Protokollierung für Cron-Jobs');
define('_MI_XNEWSLETTER_CRON_PROTOCOL_DESC', 'Wird nur angewendet wenn ein entsprechender Cron-Job existiert');
define('_MI_XNEWSLETTER_CRON_PROTOCOL_0', 'Keine Protokolleinträge erstellen');
define('_MI_XNEWSLETTER_CRON_PROTOCOL_1', 'Protokolleinträge erstellen wenn ein Fehler aufgetreten ist (empfohlen)');
define('_MI_XNEWSLETTER_CRON_PROTOCOL_2', 'Protokolleinträge für alle Ereignisse erstellen (für Testzwecke)');
define('_MI_XNEWSLETTER_SUBSCRIPTION_SEARCH', 'Suche Newsletteranmeldungen');
