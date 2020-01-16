<?php   
/**
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

include "common.php";

//General
define('_AM_XNEWSLETTER_FORMOK', 'Daten erfolgreich gespeichert');
define('_AM_XNEWSLETTER_FORMDELOK', 'Daten erfolgreich gelöscht');
define('_AM_XNEWSLETTER_FORMDELNOTOK', 'Fehler beim Löschen');
define('_AM_XNEWSLETTER_FORMSUREDEL', "Wollen Sie <span class='bold red'>%s</span> wirklich löschen?");
define('_AM_XNEWSLETTER_FORMSUREDEL_LIST', "Wollen Sie alle Protokolleinträge zu <span class='bold red'>%s</span> wirklich löschen?");
define('_AM_XNEWSLETTER_FORMSURERENEW', "Wollen Sie <span class='bold red'>%s</span> wirklich ändern?");
define('_AM_XNEWSLETTER_FORMUPLOAD', 'Hochladen');
define('_AM_XNEWSLETTER_FORMIMAGE_PATH', 'In %s vorhandene Dateien');
define('_AM_XNEWSLETTER_FORMACTION', 'Aktion');
define('_AM_XNEWSLETTER_ERROR_NO_VALID_ID', 'Fehler: Keine gültige ID!');
define('_AM_XNEWSLETTER_OK', 'Erfolgreich');
define('_AM_XNEWSLETTER_FAILED', 'Fehlgeschlagen');
define('_AM_XNEWSLETTER_SAVE', 'Speichern');
define('_AM_XNEWSLETTER_DETAILS', 'Details anzeigen');
define('_AM_XNEWSLETTER_SEARCH', 'Suche');
define('_AM_XNEWSLETTER_SEARCH_EQUAL', '=');
define('_AM_XNEWSLETTER_SEARCH_CONTAINS', 'enthält');
define('_AM_XNEWSLETTER_SUBMITTER', 'Einsender');
define('_AM_XNEWSLETTER_CREATED', 'Erstellt am');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPMAIL', 'php mail()');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPSENDMAIL', 'php sendmail()');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3', 'POP3');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP', 'Imap');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL', 'Gmail');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_NOTREQUIRED', 'Nicht erforderlich');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_NAME', 'Mein Kontoname');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_YOURNAME', 'Max Mustermann');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL', 'mustermann@yourdomain.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_USERNAME', 'Benutzername');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_PWD', 'Passwort');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_IN', 'pop3.yourdomain.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_IN', '110');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_SERVER_OUT', 'mail.yourdomain.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3_PORT_OUT', '25');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_IN', 'imap.yourdomain.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_IN', '143');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_SERVER_OUT', 'mail.yourdomain.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP_PORT_OUT', '25');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_USERNAME', 'yourusername@gmail.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_IN', 'imap.gmail.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_IN', '993');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_IN', 'tls');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_SERVER_OUT', 'smtp.gmail.com');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL_PORT_OUT', '465');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_SECURETYPE_OUT', 'ssl');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK', 'Bitte Einstellungen überprüfen');
define('_AM_XNEWSLETTER_LETTER_ACTION', 'Aktion nach dem Speichern');
define('_AM_XNEWSLETTER_LETTER_ACTION_SAVED', 'Gespeichert');
define('_AM_XNEWSLETTER_LETTER_ACTION_NO', 'Keine Aktion');
define('_AM_XNEWSLETTER_LETTER_ACTION_COPYNEW', 'Kopie als neuen Newsletter');
define('_AM_XNEWSLETTER_LETTER_ACTION_PREVIEW', 'Vorschau anzeigen');
define('_AM_XNEWSLETTER_LETTER_ACTION_SEND', 'Newsletter an alle Abonnenten versenden');
define('_AM_XNEWSLETTER_LETTER_ACTION_RESEND', 'Newsletter neuerlich an alle Abonnenten versenden, bei denen der Sendevorgang fehlgeschlagen ist');
define('_AM_XNEWSLETTER_LETTER_ACTION_SENDTEST', 'Newsletter testweise verschicken');
define('_AM_XNEWSLETTER_LETTER_EMAIL_TEST', 'E-Mail zum Testen des Newsletters');
define('_AM_XNEWSLETTER_LETTER_EMAIL_ALTBODY', 'Um den Inhalt korrekt anzeigen zu lassen, verwenden Sie bitte einen HTML-kompatiblen E-Mail-Client!');
define('_AM_XNEWSLETTER_LETTER_ERROR_INVALID_ATT_ID', 'Fehler beim Löschen des Anhanges (ungültige ID)');
define('_AM_XNEWSLETTER_SEND_SUCCESS', 'Newsletter versendet');
define('_AM_XNEWSLETTER_SEND_SUCCESS_TEST', 'Newsletter zum Testen versendet');
define('_AM_XNEWSLETTER_SEND_SUCCESS_NUMBER', 'Senden von %t Newsletter(n) erfolgreich');
define('_AM_XNEWSLETTER_SEND_SUCCESS_ML', 'Verarbeitung Mailinglist erfolgreich');
define('_AM_XNEWSLETTER_SEND_SUCCESS_ML_DETAIL', "Senden '%a' an Mailinglist '%m' erfolgreich");
define('_AM_XNEWSLETTER_SEND_ERROR_NUMBER', 'Fehler beim Senden des Newsletters: %e von %t Newslettern wurden nicht gesendet');
define('_AM_XNEWSLETTER_SEND_ERROR_PHPMAILER', 'Fehler phpmailer: ');
define('_AM_XNEWSLETTER_SEND_ERROR_NO_EMAIL', 'Fehler: Keine gültige E-Mail-Adresse vorhanden');
define('_AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID', 'Fehler: Kein gültiger Newsletter ausgewählt');
define('_AM_XNEWSLETTER_SEND_ERROR_INALID_TEMPLATE_PATH', "Fehler: Vorlagenpfad '%p' nicht gefunden");
define('_AM_XNEWSLETTER_SEND_SURE_SENT', 'Dieser Newsletter wurde bereits an alle Abonnenten versendet.<br/>Wollen Sie diesen Newsletter wirklich noch einmal an alle Abonnenten versenden?');
define('_AM_XNEWSLETTER_SEND_ERROR_NO_SUBSCR', 'Fehler: Keine gültige Anmeldungen für den (die) ausgewählten Newsletter vorhanden');
//Index
define('_AM_XNEWSLETTER_LETTER', 'Statistik Newsletter');
define('_AM_XNEWSLETTER_THEREARE_ACCOUNTS', "Es sind <span class='bold'>%s</span> Sender-Konten vorhanden");
define('_AM_XNEWSLETTER_THEREARE_CAT', "Es sind <span class='bold'>%s</span> Newsletterkategorien vorhanden");
define('_AM_XNEWSLETTER_THEREARE_SUBSCR', "Es sind <span class='bold'>%s</span> Abonnenten vorhanden");
define('_AM_XNEWSLETTER_THEREARE_CATSUBSCR', "Es sind <span class='bold'>%s</span> Abonnenten zu Newsletterkategorien vorhanden");
define('_AM_XNEWSLETTER_THEREARE_LETTER', "Es sind <span class='bold'>%s</span> Newsletter vorhanden");
define('_AM_XNEWSLETTER_THEREARE_PROTOCOL', "Es sind <span class='bold'>%s</span> Protokolleinträge vorhanden");
define('_AM_XNEWSLETTER_THEREARE_ATTACHMENT', "Es sind <span class='bold'>%s</span> Anhänge vorhanden");
define('_AM_XNEWSLETTER_THEREARE_MAILINGLIST', "Es sind <span class='bold'>%s</span> Mailinglisten vorhanden");
define('_AM_XNEWSLETTER_THEREARE_BMH', "Es sind <span class='bold'>%s</span> Bounced-Mail-Handler-Einträge vorhanden");
define('_AM_XNEWSLETTER_THEREARE_TASK', "Es sind <span class='bold'>%s</span> Aufgaben vorhanden");
define('_AM_XNEWSLETTER_THEREARE_TEMPLATE', "Es sind <span class='bold'>%s</span> Vorlagen vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_ACCOUNTS', "Es sind keine Sender-Konten vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_CAT', "Es sind keine Newsletterkategorien vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_SUBSCR', "Es sind keine Abonnenten vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_CATSUBSCR', "Es sind keine Abonnenten zu Newsletterkategorien vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_LETTER', "Es sind keine Newsletter vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_PROTOCOL', "Es sind keine Protokolleinträge vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_ATTACHMENT', "Es sind keine Anhänge vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_MAILINGLIST', "Es sind keine Mailinglisten vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_BMH', "Es sind keine Bounced-Mail-Handler-Einträge vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_TASK', "Es sind keine Aufgaben vorhanden");
define('_AM_XNEWSLETTER_THEREARENT_TEMPLATE', "Es sind keine Templates vorhanden");
//Buttons
define('_AM_XNEWSLETTER_NEWACCOUNTS', 'Sender-Konto hinzufügen');
define('_AM_XNEWSLETTER_ACCOUNTSLIST', 'Sender-Konten auflisten');
define('_AM_XNEWSLETTER_ACCOUNTSWAIT', 'Offene Sender-Konten');
define('_AM_XNEWSLETTER_NEWCAT', 'Newsletterkategorie hinzufügen');
define('_AM_XNEWSLETTER_CATLIST', 'Newsletterkategorien auflisten');
define('_AM_XNEWSLETTER_CATWAIT', 'Offene Newsletterkategorien');
define('_AM_XNEWSLETTER_NEWSUBSCR', 'Abonnent hinzufügen');
define('_AM_XNEWSLETTER_SUBSCRLIST', 'Abonnenten auflisten');
define('_AM_XNEWSLETTER_SUBSCRWAIT', 'Offene Abonnenten');
define('_AM_XNEWSLETTER_NEWCATSUBSCR', 'Abonnent zu Newsletterkategorie hinzufügen');
define('_AM_XNEWSLETTER_CATSUBSCRLIST', 'Abonnenten zu Newsletterkategorien auflisten');
define('_AM_XNEWSLETTER_CATSUBSCRWAIT', 'Offene Abonnenten zu Newsletterkategorien');
define('_AM_XNEWSLETTER_NEWLETTER', 'Newsletter hinzufügen');
define('_AM_XNEWSLETTER_LETTERLIST', 'Newsletter auflisten');
define('_AM_XNEWSLETTER_LETTERWAIT', 'Offene Newsletter');
define('_AM_XNEWSLETTER_LETTER_DELETE_ALL', 'Protokoll von diesem Newsletter löschen');
define('_AM_XNEWSLETTER_NEWPROTOCOL', 'Protokolleintrag hinzufügen');
define('_AM_XNEWSLETTER_PROTOCOLLIST', 'Protokolleinträge auflisten');
define('_AM_XNEWSLETTER_PROTOCOLWAIT', 'Offene Protokolleinträge');
define('_AM_XNEWSLETTER_NEWATTACHMENT', 'Anhang hinzufügen');
define('_AM_XNEWSLETTER_ATTACHMENTLIST', 'Anhänge auflisten');
define('_AM_XNEWSLETTER_ATTACHMENTWAIT', 'Offene Anhänge');
define('_AM_XNEWSLETTER_NEWMAILINGLIST', 'Mailingliste hinzufügen');
define('_AM_XNEWSLETTER_MAILINGLISTLIST', 'Mailinglisten auflisten');
define('_AM_XNEWSLETTER_MAILINGLISTWAIT', 'Offene Mailinglisten');
define('_AM_XNEWSLETTER_RUNBMH', 'Bounced-Mail-Handler starten');
define('_AM_XNEWSLETTER_BMHLIST', 'Ergebnisse Bounced-Mail-Handler auflisten');
define('_AM_XNEWSLETTER_BMHWAIT', 'Offene Ergebnisse Bounced-Mail-Handler');
define('_AM_XNEWSLETTER_ACCOUNTS_ADD', 'E-Mail-Konto hinzufügen');
define('_AM_XNEWSLETTER_ACCOUNTS_EDIT', 'E-Mail-Konto bearbeiten');
define('_AM_XNEWSLETTER_ACCOUNTS_DELETE', 'E-Mail-Konto löschen');
define('_AM_XNEWSLETTER_ACCOUNTS_ID', 'Id');
define('_AM_XNEWSLETTER_ACCOUNTS_TYPE', 'Typ');
define('_AM_XNEWSLETTER_ACCOUNTS_NAME', 'Name');
define('_AM_XNEWSLETTER_ACCOUNTS_YOURNAME', 'Anzeigename');
define('_AM_XNEWSLETTER_ACCOUNTS_YOURMAIL', 'E-Mail-Adresse');
define('_AM_XNEWSLETTER_ACCOUNTS_USERNAME', 'Benutzername');
define('_AM_XNEWSLETTER_ACCOUNTS_PASSWORD', 'Passwort');
define('_AM_XNEWSLETTER_ACCOUNTS_INCOMING', 'Eingangsserver');
define('_AM_XNEWSLETTER_ACCOUNTS_SERVER_IN', 'Mailserver');
define('_AM_XNEWSLETTER_ACCOUNTS_PORT_IN', 'Port');
define('_AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_IN', 'Sicherheitstyp');
define('_AM_XNEWSLETTER_ACCOUNTS_OUTGOING', 'Ausgangsserver');
define('_AM_XNEWSLETTER_ACCOUNTS_SERVER_OUT', 'Mailserver');
define('_AM_XNEWSLETTER_ACCOUNTS_PORT_OUT', 'Port');
define('_AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_OUT', 'Sicherheitstyp');
define('_AM_XNEWSLETTER_ACCOUNTS_DEFAULT', 'Standard E-Mail-Konto');
define('_AM_XNEWSLETTER_ACCOUNTS_BOUNCE_INFO', 'Zusätzliche Infos für Bounced-Mail-Handler');
define('_AM_XNEWSLETTER_ACCOUNTS_USE_BMH', 'Bounced-Mail-Handler verwenden');
define('_AM_XNEWSLETTER_ACCOUNTS_INBOX', 'Mailbox für Überprüfung durch Bounced-Mail-Handler');
define('_AM_XNEWSLETTER_ACCOUNTS_HARDBOX', "Verwende diese Mailbox als 'hardbox'");
define('_AM_XNEWSLETTER_ACCOUNTS_HARDBOX_DESC', 
        "Der Mailboxname muss mit 'INBOX.' beginnen. Sie können entweder einen Standard-Ordner in Ihrer Mailbox verwenden (z.B. INBOX.Trash) oder Sie erstellen einen eigenen Odner wie z.B.'hard' oder 'soft' (dies wird empfohlen). Wenn Sie einen neuen Ordnernamen angeben, wird dieser automatisch erstellt (diese Funktion wird jedoch von Gmail-Konten aus Sicherheitsgründen nicht unterstützt).");
define('_AM_XNEWSLETTER_ACCOUNTS_MOVEHARD', "Als 'hard' klassifizierte Bounced Mails in 'hardbox' verschieben");
define('_AM_XNEWSLETTER_ACCOUNTS_SOFTBOX', "Verwende diese Mailbox als 'softbox'");
define('_AM_XNEWSLETTER_ACCOUNTS_MOVESOFT', "Als 'soft' klassifizierte Bounced Mails in 'softbox' verschieben");
define('_AM_XNEWSLETTER_ACCOUNTS_ERROR_OPEN_MAILBOX', 'Fehler beim Öffnen der Mailbox! Bitte Einstellungen überprüfen!');
define('_AM_XNEWSLETTER_SAVE_AND_CHECK', 'Speichern und Einstellungen überprüfen');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_OK', 'erfolgreich  ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED', 'fehlgeschlagen  ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_SKIPPED', 'übersprungen ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_INFO', 'Zusätzliche Infos');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX', 'Öffnen Mailbox ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS', 'Öffnen Ordner ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH', 'Bounced-Mail-Handler ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_INBOX', 'Mailbox');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_HARDBOX', 'Hardbox');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_SOFTBOX', 'Softbox');
define('_AM_XNEWSLETTER_CAT_ADD', 'Newsletterkategorie hinzufügen');
define('_AM_XNEWSLETTER_CAT_EDIT', 'Newsletterkategorie bearbeiten');
define('_AM_XNEWSLETTER_CAT_DELETE', 'Newsletterkategorie löschen');
define('_AM_XNEWSLETTER_CAT_ID', 'Id');
define('_AM_XNEWSLETTER_CAT_NAME', 'Newsletter Name');
define('_AM_XNEWSLETTER_CAT_INFO', 'Zusätzliche Infos');
define('_AM_XNEWSLETTER_CAT_GPERMS_CREATE', 'Berechtigung zum Erstellen');
define('_AM_XNEWSLETTER_CAT_GPERMS_CREATE_DESC', "<br/><span style='font-weight:normal'>- Erstellen neuer Newsletter<br/>- Bearbeiten, Löschen und Senden der eigenen Newsletter</span>");
define('_AM_XNEWSLETTER_CAT_GPERMS_ADMIN', 'Berechtigung zum Verwalten');
define('_AM_XNEWSLETTER_CAT_GPERMS_ADMIN_DESC', "<br/><span style='font-weight:normal'>Bearbeiten, Löschen und Senden aller Newsletter dieser Kategorie</span>");
define('_AM_XNEWSLETTER_CAT_GPERMS_READ', 'Berechtigung zum Lesen/Abonnieren');
define('_AM_XNEWSLETTER_CAT_GPERMS_LIST', 'Berechtigung zum Anzeigen der Liste der Abonnenten');
define('_AM_XNEWSLETTER_CAT_MAILINGLIST', 'Mailingliste');
define('_AM_XNEWSLETTER_SUBSCR_ADD', 'Abonnenten hinzufügen');
define('_AM_XNEWSLETTER_SUBSCR_EDIT', 'Abonnenten bearbeiten');
define('_AM_XNEWSLETTER_SUBSCR_DELETE', 'Abonnenten löschen');
define('_AM_XNEWSLETTER_SUBSCR_ID', 'Id');
define('_AM_XNEWSLETTER_SUBSCR_EMAIL', 'E-Mail-Adresse');
define('_AM_XNEWSLETTER_SUBSCR_FIRSTNAME', 'Vorname');
define('_AM_XNEWSLETTER_SUBSCR_LASTNAME', 'Familienname');
define('_AM_XNEWSLETTER_SUBSCR_UID', 'Mitgliedsname');
define('_AM_XNEWSLETTER_SUBSCR_SEX', 'Anrede');
define('_AM_XNEWSLETTER_SUBSCR_SEX_EMPTY', '');
define('_AM_XNEWSLETTER_SUBSCR_SEX_MALE', 'Herr');											
define('_AM_XNEWSLETTER_SUBSCR_SEX_FEMALE', 'Frau');
define('_AM_XNEWSLETTER_SUBSCR_SEX_FAMILY', 'Familie');
define('_AM_XNEWSLETTER_SUBSCR_SEX_COMP', 'Firma');
define('_AM_XNEWSLETTER_SUBSCR_ACTIVATED', 'aktiviert?');
define('_AM_XNEWSLETTER_SUBSCR_SHOW_ALL', 'Alle anzeigen'); 
define('_AM_XNEWSLETTER_CATSUBSCR_ADD', 'Abonnent zu Newsletterkategorie hinzufügen');
define('_AM_XNEWSLETTER_CATSUBSCR_EDIT', 'Abonnent/Newsletterkategorie bearbeiten');
define('_AM_XNEWSLETTER_CATSUBSCR_DELETE', 'Abonnent/Newsletterkategorie löschen');
define('_AM_XNEWSLETTER_CATSUBSCR_ID', 'Id');
define('_AM_XNEWSLETTER_CATSUBSCR_CATID', 'Newsletter');
define('_AM_XNEWSLETTER_CATSUBSCR_SUBSCRID', 'Abonnenten');
define('_AM_XNEWSLETTER_CATSUBSCR_QUITED', 'Beendet');
define('_AM_XNEWSLETTER_CATSUBSCR_SUREDELETE', "Wollen Sie <br>'%s'<br> wirklich von<br>'%c' entfernen?");
define('_AM_XNEWSLETTER_CATSUBSCR_QUIT_NONE', 'Nein');
define('_AM_XNEWSLETTER_CATSUBSCR_QUIT_NOW', 'Jetzt beenden');
define('_AM_XNEWSLETTER_CATSUBSCR_QUIT_REMOVE', 'Beendigungsdatum wieder entfernen');
define('_AM_XNEWSLETTER_LETTER_ADD', 'Newsletter hinzufügen');
define('_AM_XNEWSLETTER_LETTER_EDIT', 'Newsletter bearbeiten');
define('_AM_XNEWSLETTER_LETTER_DELETE', 'Newsletter löschen');
define('_AM_XNEWSLETTER_LETTER_ID', 'Id');
define('_AM_XNEWSLETTER_LETTER_TITLE', 'Titel');
define('_AM_XNEWSLETTER_LETTER_CONTENT', 'Inhalt');
define('_AM_XNEWSLETTER_LETTER_TEMPLATE', 'Vorlage');
define('_AM_XNEWSLETTER_LETTER_CATS', 'Newsletterkategorie');
define('_AM_XNEWSLETTER_LETTER_ATTACHMENT', 'Anhänge');
define('_AM_XNEWSLETTER_LETTER_STATUS', 'Status');
define('_AM_XNEWSLETTER_LETTER_ACCOUNTS_AVAIL', 'Verfügbare Sende-Konten');
define('_AM_XNEWSLETTER_LETTER_ACCOUNT', 'Konto');
define('_AM_XNEWSLETTER_LETTER_MAILINGLIST', 'Mailingliste verwenden');
define('_AM_XNEWSLETTER_LETTER_MAILINGLIST_NO', 'Nein');
define('_AM_XNEWSLETTER_ATTACHMENT_ADD', 'Anhang hinzufügen');
define('_AM_XNEWSLETTER_ATTACHMENT_EDIT', 'Anhang bearbeiten');
define('_AM_XNEWSLETTER_ATTACHMENT_DELETE', 'Anhang löschen');
define('_AM_XNEWSLETTER_ATTACHMENT_ID', 'Id');
define('_AM_XNEWSLETTER_ATTACHMENT_LETTER_ID', 'Newsletter');
define('_AM_XNEWSLETTER_ATTACHMENT_NAME', 'Name');
define('_AM_XNEWSLETTER_ATTACHMENT_TYPE', 'Dateityp');
define('_AM_XNEWSLETTER_PROTOCOL_ADD', 'Protokolleintrag hinzufügen');
define('_AM_XNEWSLETTER_PROTOCOL_EDIT', 'Protokolleintrag bearbeiten');
define('_AM_XNEWSLETTER_PROTOCOL_DELETE', 'Protokolleintrag löschen');
define('_AM_XNEWSLETTER_PROTOCOL_ID', 'Id');
define('_AM_XNEWSLETTER_PROTOCOL_LETTER_ID', 'Newsletter');
define('_AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID', 'Abonnent');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS', 'Status');
define('_AM_XNEWSLETTER_PROTOCOL_SUCCESS', 'Erfolgreich');
define('_AM_XNEWSLETTER_PROTOCOL_LAST_STATUS', 'Letzter Status');
define('_AM_XNEWSLETTER_PROTOCOL_MISC', 'Diverse Protokolleinträge');
define('_AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL', 'E-Mail-Adresse des Empfängers nicht mehr vorhanden');
define('_AM_XNEWSLETTER_MAILINGLIST_ADD', 'Mailingliste hinzufügen');
define('_AM_XNEWSLETTER_MAILINGLIST_EDIT', 'Mailingliste bearbeiten');
define('_AM_XNEWSLETTER_MAILINGLIST_DELETE', 'Mailingliste löschen');
define('_AM_XNEWSLETTER_MAILINGLIST_ID', 'Id');
define('_AM_XNEWSLETTER_MAILINGLIST_NAME', 'Name');
define('_AM_XNEWSLETTER_MAILINGLIST_EMAIL', 'E-Mail');
define('_AM_XNEWSLETTER_MAILINGLIST_EMAIL_DESC', 'An-/Abmeldungen an folgende E-Mail-Adresse senden');
define('_AM_XNEWSLETTER_MAILINGLIST_LISTNAME', 'Listenname');
define('_AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE', 'Code für Anmeldung');
define('_AM_XNEWSLETTER_MAILINGLIST_SUBSCRIBE_DESC', '{nameofmylist} wird durch den oben angegeben Listennamen ersetzt<br>{email} wird durch die E-Mail-Adresse des Abonnenten ersetzt');
define('_AM_XNEWSLETTER_MAILINGLIST_UNSUBSCRIBE', 'Code für Abmeldung');
define('_AM_XNEWSLETTER_BOUNCETYPE', 'Bounce-Typ');
define('_AM_XNEWSLETTER_BMH_EDIT', 'Bounced-Mail-Handler-Eintrag bearbeiten');
define('_AM_XNEWSLETTER_BMH_DELETE', 'Bounced-Mail-Handler-Eintrag löschen');
define('_AM_XNEWSLETTER_BMH_ID', 'Id');
define('_AM_XNEWSLETTER_BMH_RULE_NO', 'Regel Nr.');
define('_AM_XNEWSLETTER_BMH_RULE_CAT', 'Regelkategorie');
define('_AM_XNEWSLETTER_BMH_BOUNCETYPE', 'Bounce-Typ');
define('_AM_XNEWSLETTER_BMH_REMOVE', 'Gelöscht');
define('_AM_XNEWSLETTER_BMH_EMAIL', 'E-Mail');
define('_AM_XNEWSLETTER_BMH_SUBJECT', 'Betreff');
define('_AM_XNEWSLETTER_BMH_MEASURE', 'Maßnahme');
define('_AM_XNEWSLETTER_BMH_MEASURE_PENDING', 'Offen');
define('_AM_XNEWSLETTER_BMH_MEASURE_NOTHING', 'Keine Maßnahme');
define('_AM_XNEWSLETTER_BMH_MEASURE_QUIT', 'Diesen Abonnenten vorübergehend stilllegen');
define('_AM_XNEWSLETTER_BMH_MEASURE_DELETE', 'Diesen Abonnenten löschen');
define('_AM_XNEWSLETTER_BMH_MEASURE_QUITED', 'Abonnent vorübergehend stillgelegt');
define('_AM_XNEWSLETTER_BMH_MEASURE_DELETED', 'Abonnent gelöscht');
define('_AM_XNEWSLETTER_BMH_MEASURE_ALREADY_DELETED', 'Abonnenten bereits gelöscht! Aktion nicht möglich!');
define('_AM_XNEWSLETTER_BMH_MEASURE_DELETE_SURE', 'Sind Sie sicher, dass Sie diese Registrierung mit allen Newsletteranmeldungen löschen wollen?<br><br>Eine spätere Reaktivierung durch den Abonnenten ist nicht mehr möglich!<br/><br>');
define('_AM_XNEWSLETTER_BMH_ERROR_NO_SUBSCRID', 'Für die angegebene E-Mail-Adresse konnte keine Registrierung gefunden werden!');
define('_AM_XNEWSLETTER_BMH_ERROR_NO_ACTIVE', 'Bounced-Mail-Handler ist bei keinem E-Mail-Konto aktiviert');
define('_AM_XNEWSLETTER_BMH_RSLT', 'Ergebnis der Überprüfung der Mailbox %b<br/>Anzahl gelesene Nachrichten: %r<br/>Anzahl durchgeführte Aktionen: %a<br/>Anzahl ohne Aktionen: %n<br/>Anzahl verschobene Mails: %m<br/>Anzahl gelöschte Mails: %d<br/><br/><br/>');
define('_AM_XNEWSLETTER_BMH_SUCCESSFUL', 'Bounced-Mail-Handler erfolgreich beendet');
define('_AM_XNEWSLETTER_BMH_MEASURE_ALL', 'Alle anzeigen');
define('_AM_XNEWSLETTER_BMH_MEASURE_SHOW_NONE', "Keine Bounced-Mail-Handler-Einträge für '%s' vorhanden");
define('_AM_XNEWSLETTER_MAINTENANCE_CAT', 'Kategorie');
define('_AM_XNEWSLETTER_MAINTENANCE_DESCR', 'Beschreibung');
define('_AM_XNEWSLETTER_MAINTENANCE_PARAM', 'Parameter');
define('_AM_XNEWSLETTER_MAINTENANCE_ERROR', 'Bei der Wartung ist ein Fehler aufgetreten');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEDATE', 'Alle unbestätigten Anmeldung vor diesem Datum löschen.<br>Achtung! Aktion kann nicht rückgängig gemacht werden! Bitte Datum korrekt prüfen!');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEUSER', 'Soll(en) die <b>%s</b> unbestätigte(n) Anmeldung(en) vor dem %s werden jetzt gelöscht.<br>Aktion ist unwiderruflich!');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOCOL', 'alle Protokolle löschen und Tabelle zurücksetzen');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOK', 'Protokolltabelle wurde gewartet.');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETENOTHING', 'Derzeit keine Aktion notwendig.');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETEUSEROK', 'Es wurden %s User entfernt');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR', 'Anmeldungen zu Newslettern löschen, wenn keine aufrechte Registrierung besteht');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK', 'Es wurden %s fehlerhafte Anmeldungen entfernt');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_NODATA', 'Keine fehlerhaften Datensätze in Tabelle catsubsr vorhanden');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML', 'Daten Newsletterkategorien und Mailinglisten abgleichen und fehlerhafte Datensätze bereinigen');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_OK', '%s fehlerhafte Daten Mailinglisten wurden bereinigt');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_NODATA', 'Keine fehlerhaften Datensätze Mailinglisten vorhanden');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL', 'Daten Newsletterkategorien und Newsletter abgleichen und fehlerhafte Datensätze bereinigen');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_OK', '%s fehlerhafte Daten bei Newslettern wurden bereinigt');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_NODATA', 'Keine fehlerhaften Datensätze bei Newslettern vorhanden');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT', 'Importtabelle leeren und Tabelle zurücksetzen');
define('_AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT_OK', 'Importtabelle wurde gewartet.');
define('_AM_XNEWSLETTER_IMPORT_SEARCH', 'Suche verfügbare E-Mail-Adressen für Import');
define('_AM_XNEWSLETTER_IMPORT_PRESELECT_CAT', 'Vorauswahl Newsletterkategorie');
define('_AM_XNEWSLETTER_IMPORT_PLUGINS_AVAIL', 'Verfügbare Plugins');
define('_AM_XNEWSLETTER_IMPORT_CONTINUE', 'Weiter');
define('_AM_XNEWSLETTER_IMPORT_AFTER_READ', 'Aktion nach dem Einlesen der Daten');
define('_AM_XNEWSLETTER_IMPORT_READ_CHECK', 'Daten zur Überprüfung anzeigen');
define('_AM_XNEWSLETTER_IMPORT_CHECK_LIMIT', 'Limit an E-Mail-Adressen für Import');
define('_AM_XNEWSLETTER_IMPORT_CHECK_LIMIT_PACKAGE', 'Limit an E-Mail-Adressen pro Verarbeitungschritt');
define('_AM_XNEWSLETTER_IMPORT_NOLIMIT', 'Ohne Limit');
define('_AM_XNEWSLETTER_IMPORT_READ_IMPORT', 'Daten ohne Überprüfung sofort importieren');
define('_AM_XNEWSLETTER_IMPORT_SHOW', 'Zeige %s bis %l von %n verfügbaren E-Mail-Adressen');
define('_AM_XNEWSLETTER_IMPORT_NODATA', 'Keine Daten gefunden');
define('_AM_XNEWSLETTER_IMPORT_EMAIL_EXIST', 'E-Mail bereits registriert');
define('_AM_XNEWSLETTER_IMPORT_CATSUBSCR_EXIST', 'Anmeldung bereits vorhanden');
define('_AM_XNEWSLETTER_IMPORT_NOIMPORT', '-- Kein Import --');
define('_AM_XNEWSLETTER_IMPORT_EXEC', 'Import E-Mail-Adressen laut Einstellungen');
define('_AM_XNEWSLETTER_IMPORT_RESULT_SKIP', 'Import E-Mail-Adresse %e übersprungen');
define('_AM_XNEWSLETTER_IMPORT_RESULT_FAILED', 'Import E-Mail-Adresse %e fehlgeschlagen');
define('_AM_XNEWSLETTER_IMPORT_RESULT_REG_OK', 'Registrierung erfolgreich');
define('_AM_XNEWSLETTER_IMPORT_RESULT_SUBSCR_OK', 'Anmeldung zu Newsletter erfolgreich');
define('_AM_XNEWSLETTER_IMPORT_SKIP_EXISTING', 'Existierende Newsletteranmeldungen überspringen');
define('_AM_XNEWSLETTER_IMPORT_FINISHED', 'Verarbeitung %p von %t E-Mail-Adressen erfolgreich beendet');
define('_AM_XNEWSLETTER_IMPORT_INFO', 'Alle User aus den gewählten Gruppen zum Newsletter hinzufügen');
define('_AM_XNEWSLETTER_IMPORT_CSV_OPT', 'Optionen für CSV-Datei');
define('_AM_XNEWSLETTER_IMPORT_CSV_FILE', 'CSV-Datei:');
define('_AM_XNEWSLETTER_IMPORT_CSV_DELIMITER', 'Delimiter:');
define('_AM_XNEWSLETTER_IMPORT_CSV_HEADER', 'CSV-Datei mit Kopfzeile');
define('_AM_XNEWSLETTER_IMPORT_CSV', 'Eine Spalte ( email ) oder vier Spalten ( email | anrede | vorname | nachname)<br>siehe sample1col.csv und sample4col.csv in /plugins');
define('_AM_XNEWSLETTER_IMPORT_XOOPSUSER', 'Optionen für Import/Syncronisation XoopsUsers');
define('_AM_XNEWSLETTER_IMPORT_XOOPSUSER_GROUPS', 'Gruppe wählen');
define('_AM_XNEWSLETTER_NEWTASK', 'Neue Aufgabe erstellen');
define('_AM_XNEWSLETTER_TASKLIST', 'Aufgaben auflisten');
define('_AM_XNEWSLETTER_TASK_ADD', 'Aufgabe hinzufügen');
define('_AM_XNEWSLETTER_TASK_EDIT', 'Aufgabe bearbeiten');
define('_AM_XNEWSLETTER_TASK_DELETE', 'Aufgabe löschen');
define('_AM_XNEWSLETTER_TASK_ID', 'Id');
define('_AM_XNEWSLETTER_TASK_LETTER_ID', 'Newsletter');
define('_AM_XNEWSLETTER_TASK_SUBSCR_ID', 'Empfänger');
define('_AM_XNEWSLETTER_TASK_STATUS', 'Status');
define('_AM_XNEWSLETTER_TASK_STARTTIME', 'Startzeit');
define('_AM_XNEWSLETTER_TASK_ERROR_CREATE', 'Fehler beim Erstellen des Aufgabenliste');
define('_AM_XNEWSLETTER_TASK_NO_DATA', 'Keine Aufgaben vorhanden');
//Error NoFrameworks
define('_AM_XNEWSLETTER_NOFRAMEWORKS', 'Fehler: Sie verwenden das Frameworks \'admin module\' nicht. Bitte installieren Sie dieses Frameworks');
define('_AM_XNEWSLETTER_MAINTAINEDBY', 'wird unterstützt von ');
define('_AM_XNEWSLETTER_SEND_ERROR_NO_LETTERCONTENT', 'Kein Text zum Drucken vorhanden');
define('_AM_XNEWSLETTER_FORMSEARCH_SUBSCR_EXIST', 'Suche vorhandene Registrierungen anhand einer E-Mail-Adresse');
define('_AM_XNEWSLETTER_SUBSCR_NO_CATSUBSCR', 'Für diese E-Mail-Adresse sind keine Anmeldungen zu Newsletterkategorien vorhanden');
//version 1.2
define('_AM_XNEWSLETTER_IMPORT_ERROR_NO_PLUGIN', "Fehler: Erforderliche Datei 'plugins/%p.php' nicht gefunden!");
define('_AM_XNEWSLETTER_IMPORT_ERROR_NO_FUNCTION', "Fehler: Erforderliche Funktion 'xnewsletter_plugin_getdata_%f' nicht vorhanden!");
//version 1.3
//General
define('_AM_XNEWSLETTER_LETTER_CONTENT_DESC','HTML-Format');
//
define('_AM_XNEWSLETTER_SUBSCR_SEX_PREVIEW','Mr.');
define('_AM_XNEWSLETTER_SUBSCR_FIRSTNAME_PREVIEW','John');
define('_AM_XNEWSLETTER_SUBSCR_LASTNAME_PREVIEW','Doe');
define('_AM_XNEWSLETTER_SUBSCR_EMAIL_PREVIEW','username@example.com');
//
define('_AM_XNEWSLETTER_TEMPLATE_ADD','Fügen Sie eine Vorlage hinzu');
define('_AM_XNEWSLETTER_TEMPLATE_EDIT','Bearbeiten Sie eine Vorlage');
define('_AM_XNEWSLETTER_TEMPLATE_DELETE','Löschen Sie eine Vorlage');
define('_AM_XNEWSLETTER_TEMPLATE_ID','Id');
define('_AM_XNEWSLETTER_TEMPLATE_TITLE','Title');
define('_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION','Beschreibung');
define('_AM_XNEWSLETTER_TEMPLATE_DESCRIPTION_DESC','');
define('_AM_XNEWSLETTER_TEMPLATE_CONTENT','Inhalt');
define('_AM_XNEWSLETTER_TEMPLATE_CONTENT_DESC','
    Html format
    <br>
    Dieses Modul verwendet die Xoops <a href="http://www.smarty.net/">Smarty template engine</a> zum Rendern von E-Mail-Newslettern.
    <br><br>
    Verfügbare Smarty-Variable sind:
    <ul>
    <li>&lt;{$salutation}> oder &lt;{$sex}>: Begrüßungsfeld</li>
    <li>&lt;{$firstname}>: Vorname</li>
    <li>&lt;{$lastname}>: Familienname</li>
    <li>&lt;{$email}> oder &lt;{$subscr_email}>: E-Mail</li>
    </ul>
    <ul>
    <li>&lt;{$title}>: Newsletter Titel</li>
    <li>&lt;{$content}>: Newsletter Inhalt</li>
    <li>&lt;{$attachments}>: Auflistung Anhänge
        <br>
        <span style="font-size:0.9em">
        z.B.:
        <br>
        &lt;ul>
        <br>
        &lt;{foreach item="attachment" from=$attachments}>
        <br>
        &lt;li>&lt;a href="&lt;{$attachment.attachment_url}>">&lt;{$attachment.attachment_name}>&lt;/a>&lt;/li>
        <br>
        &lt;{/foreach}>
        <br>
        &lt;/ul>
        <br>
        gibt eine Liste der Anhänge aus
        </span>
    </li>
    </ul>
    <ul>
    <li>&lt;{$date}>: Sendedatum als Timestamp
        <br>
        <span style="font-size:0.9em">
        e.g.:
        <br>
        &lt;{$date|date_format:"%Y/%m/%d"}> formatiert das Datum als ' . date('Y/m/d') . '
        </span>
        </li>
    <li>&lt;{$unsubscribe_url}>: Link für Abbestellung des Newsletters</li>
    </ul>
    <ul>
    <li>&lt;{$xoops_url}>: Url Ihrer Webseite (z.B. http://localhost/)</li>
    <li>&lt;{$xoops_langcode}>: Sprachcode Ihrer Webseite (z.B. en)</li>
    <li>&lt;{$xoops_charset}>: Zeichensatz Ihrer Webseite (z.B. UTF-8)</li>
    </ul>');
define('_AM_XNEWSLETTER_LETTER_CLONED', 'Kopie erstellt: %s');
define('_AM_XNEWSLETTER_CAT_INFO_DESC', '');
define('_AM_XNEWSLETTER_TEXTOPTIONS', 'Textoptionen');
define('_AM_XNEWSLETTER_TEXTOPTIONS_DESC', 'Beschreibung Textoptionen');
define('_AM_XNEWSLETTER_ALLOWHTML', 'HTML tags erlauben');
define('_AM_XNEWSLETTER_ALLOWSMILEY', 'Smiley icons erlauben');
define('_AM_XNEWSLETTER_ALLOWXCODE', 'XOOPS codes erlauben');
define('_AM_XNEWSLETTER_ALLOWIMAGES', 'Bilder erlauben');
define('_AM_XNEWSLETTER_ALLOWBREAK', 'Verwenden XOOPS line break conversion');
define('_AM_XNEWSLETTER_LETTER_ACTION_PRINT', 'Drucken');
define('_AM_XNEWSLETTER_LETTER_SENDER', 'Sender');
define('_AM_XNEWSLETTER_LETTER_SENT', 'Gesendet am');
define('_AM_XNEWSLETTER_THEREARE_NOT_ACCOUNTS', "<span style='color:#FF0000;'>Achtung: derzeit existieren keine Sender-Konten, bitte erstellen Sie als erstes ein Sendekonto.</span>");
define('_AM_XNEWSLETTER_LETTER_SIZE', 'Größe');
define('_AM_XNEWSLETTER_LETTER_EMAIL_SIZE', 'Geschätzte Größe');
define('_AM_XNEWSLETTER_LETTER_EMAIL_SIZE_DESC', '');
define('_AM_XNEWSLETTER_LETTER_ATTACHMENT_SIZE', 'Größe');
define('_AM_XNEWSLETTER_LETTER_ATTACHMENT_TOTALSIZE', 'Größe Anhhänge');

define('_AM_XNEWSLETTER_ATTACHMENT_SIZE', 'Größe');
define('_AM_XNEWSLETTER_ATTACHMENT_MODE', 'Angehängt als');
define('_AM_XNEWSLETTER_ATTACHMENT_MODE_ASATTACHMENT', 'Anhang');
define('_AM_XNEWSLETTER_ATTACHMENT_MODE_ASLINK', 'Link');
define('_AM_XNEWSLETTER_ATTACHMENT_MODE_AUTO', 'Auto');

define('_AM_XNEWSLETTER_ACTIONS_ACTIVATE', 'Aktivieren');
define('_AM_XNEWSLETTER_ACTIONS_UNACTIVATE', 'Deaktivieren');
define('_AM_XNEWSLETTER_ACTIONS_EXEC', 'Ausführen');
define('_AM_XNEWSLETTER_FORMACTIVATEOK', 'Erfolgreich aktiviert');
define('_AM_XNEWSLETTER_FORMUNACTIVATEOK', 'Erfolgreich deaktiviert');
//Buttons
define('_AM_XNEWSLETTER_NEWTEMPLATE','Neue Vorlage hinzufügen');
define('_AM_XNEWSLETTER_TEMPLATELIST','Listenvorlage');

define('_AM_XNEWSLETTER_PROTOCOL_STATUS_SAVED', 'Gespeichert');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_CREATE_TASK', 'Fehler beim Erstellen Eintrag in Aufgabenliste');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND_TEST', 'Newsletter testweise versendet (%recipient)');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_OK_SEND', 'Newsletter gesendet');
define('_AM_XNEWSLETTER_PROTOCOL_STATUS_ERROR_SEND', 'Newsletter senden fehlgeschlagen -> %error');
// IN PROGRESS

//1.3
define('_AM_XNEWSLETTER_UPGRADEFAILED0', "Update fehlgeschlagen - konnte Feld '%s' nicht umbenennen");
define('_AM_XNEWSLETTER_UPGRADEFAILED1', "Update fehlgeschlagen - konnte neues Feld '%s' nicht erstellen");
define('_AM_XNEWSLETTER_UPGRADEFAILED2', "Update fehlgeschlagen - konnte Feld '%s' nicht umbenennen");
define('_AM_XNEWSLETTER_ERROR_COLUMN', 'Konnte Spalte in Datenbasis nicht erstellen: %s');
define('_AM_XNEWSLETTER_ERROR_BAD_XOOPS', 'Dieses Modul benötigt XOOPS %s+ (%s installiert)');
define('_AM_XNEWSLETTER_ERROR_BAD_PHP', 'Dieses Modul benötigt PHP Version %s+ (%s installiert)');
define('_AM_XNEWSLETTER_ERROR_TAG_REMOVAL', 'Konnte Tags vom Tag-Modul nicht entfernen');

//1.4
define('_AM_XNEWSLETTER_BMH_ADD', 'Einen Bounced-Email-Handler hinzufügen');
define('_AM_XNEWSLETTER_BMH_ACCOUNTS_ID', 'Account-ID von Bounced-Email-Handler');
define('_AM_XNEWSLETTER_UPGRADEFAILED', 'Update fehlgeschlagen - konnte Tabelle nicht löschen');

//1.41
define('_AM_XNEWSLETTER_MAILINGLIST_SYSTEM', 'Art Mailingliste');
define('_AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAILMAN', 'Phyton Mailman');
define('_AM_XNEWSLETTER_MAILINGLIST_SYSTEM_MAJORDOMO', 'Majordomo');
define('_AM_XNEWSLETTER_MAILINGLIST_SYSTEM_DEFAULT', 'Andere');
define('_AM_XNEWSLETTER_MAILINGLIST_PWD', 'Passwort');
define('_AM_XNEWSLETTER_MAILINGLIST_NOTIFYOWNER', 'Mailinglistinhaber benachrichtigen');
define('_AM_XNEWSLETTER_MAILINGLIST_PARAMS', 'Parameter');
define('_AM_XNEWSLETTER_MAILINGLIST_TARGET', 'Target url for (un)subscribe');
define('_AM_XNEWSLETTER_MAILINGLIST_CSUCCESS', 'Mailinglist hat Status OK zurückgemeldet');
define('_AM_XNEWSLETTER_MAILINGLIST_CFAILED', 'Bei der Überprüfung der Verbindung zur Mailinglist ist ein Fehler aufgetreten: HTTP Response Code ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_RESULT', 'Überprüfungsergebnis');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_LIST_FOLDERS', 'Einlesen Ordnerliste');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_MAILBOX_CREATE_SUCCESS', 'Mailbox erfolgreich erstellt: ');
define('_AM_XNEWSLETTER_ACCOUNTS_CHECK_MAILBOX_CREATE_FAILED', 'Erstellen von Mailbox fehlgeschlagen: ');
define('_AM_XNEWSLETTER_MAILINGLIST_MEMBERS', 'Mitglieder der Mailingliste anzeigen');
define('_AM_XNEWSLETTER_TEMPLATE_ONLINE', 'Online');
define('_AM_XNEWSLETTER_TEMPLATE_TYPE', 'Art der Vorlage');
define('_AM_XNEWSLETTER_TEMPLATE_TYPE_FILE', 'Dateivorlage');
define('_AM_XNEWSLETTER_TEMPLATE_TYPE_CUSTOM', 'Selbst erstellte Vorlage');
define('_AM_XNEWSLETTER_TEMPLATE_ERR_TABLE', 'Vorlage in Tabelle xnewsletter_templates nicht gefunden');
define('_AM_XNEWSLETTER_TEMPLATE_ERR_FILE', 'Vorlage in Verzeichnis nicht gefunden: %s');
define('_AM_XNEWSLETTER_TEMPLATE_ERR', 'Vorlage in nicht gefunden');
