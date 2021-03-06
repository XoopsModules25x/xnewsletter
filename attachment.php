<?php

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/header.php';

// We recovered the value of the argument op in the URL$
$attachment_id = \Xmf\Request::getInt('attachment_id', 0);
if (!$attachmentObj = $helper->getHandler('Attachment')->get($attachment_id)) {
    redirect_header('index.php', 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
}
$letter_id = $attachmentObj->getVar('attachment_letter_id');
if (!$letterObj = $helper->getHandler('Letter')->get($letter_id)) {
    redirect_header('index.php', 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
}
$userPermissions = xnewsletter_getUserPermissionsByLetter($letter_id);
if (($userPermissions['read'] && $letterObj->getVar('letter_sent') > 0) || (true === $userPermissions['send'])) {
    // download attachment
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }
    // get file informations from filesystem
    $attachmentFilename = $attachmentObj->getVar('attachment_name');
    $attachmentPath     = XOOPS_UPLOAD_PATH . $helper->getConfig('xn_attachment_path') . $letter_id . '/' . $attachmentFilename;
    $attachmentMimetype = ('' != $attachmentObj->getVar('attachment_type')) ? $attachmentObj->getVar('attachment_type') : 'application/octet-stream';
    $attachmentFilesize = $attachmentObj->getVar('attachment_size');

    // MSIE Bug fix
    $headerFilename = $attachmentFilename;
    if (false !== mb_strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
        $headerFilename = preg_replace('/\./', '%2e', $headerFilename, mb_substr_count($headerFilename, '.') - 1);
    }

    header('Pragma: public');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Length: ' . (string)$attachmentFilesize);
    header('Content-Transfer-Encoding: binary');
    header("Content-Type: {$attachmentMimetype}");
    header("Content-Disposition: attachment; filename={$headerFilename}");
    if (false !== mb_strpos($attachmentMimetype, 'text/')) {
        // downladed file is not binary
        xnewsletter_download($attachmentPath, false, true);
    } else {
        // downladed file is binary
        xnewsletter_download($attachmentPath, true, true);
    }
    exit();
}

redirect_header('index.php', 3, _CO_XNEWSLETTER_WARNING_NOPERMISSIONS);
