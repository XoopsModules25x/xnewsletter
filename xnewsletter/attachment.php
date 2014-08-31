<?php
$currentFile = basename(__FILE__);
include_once 'header.php';

// We recovered the value of the argument op in the URL$
$attachment_id 	= xnewsletterRequest::getInt('attachment_id', 0);
if(!$attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id)) {
    redirect_header('index.php', 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
    exit();
}
$letter_id = $attachmentObj->getVar('attachment_letter_id');
if(!$letterObj = $xnewsletter->getHandler('letter')->get($letter_id)) {
    redirect_header('index.php', 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
    exit();
}
$userPermissions = xnewsletter_getUserPermissionsByLetter($letter_id);
if (
    ($userPermissions['read'] && $letterObj->getVar('letter_sent') > 0) ||
    ($userPermissions['send'] == true)
) {
    // download attachment
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }
    // get file informations from filesystem
    $attachmentFilename = $attachmentObj->getVar('attachment_name');
    $attachmentPath = XOOPS_UPLOAD_PATH . $xnewsletter->getConfig('xn_attachment_path') . $letter_id . '/' . $attachmentFilename;
    $attachmentMimetype = ($attachmentObj->getVar('attachment_type') != '') ? $attachmentObj->getVar('attachment_type') : "application/octet-stream";
    $attachmentFilesize = $attachmentObj->getVar('attachment_size');

    // MSIE Bug fix
    $headerFilename = $attachmentFilename;
    if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
        $headerFilename = preg_replace('/\./', '%2e', $headerFilename, substr_count($headerFilename, '.') - 1);
    }
    //
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Length: " . (string) ($attachmentFilesize));
    header("Content-Transfer-Encoding: binary");
    header("Content-Type: {$attachmentMimetype}");
    header("Content-Disposition: attachment; filename={$headerFilename}");
    if (strstr($attachmentMimetype, 'text/')) {
        // downladed file is not binary
        xnewsletter_download($attachmentPath, false, true);
    } else {
        // downladed file is binary
        xnewsletter_download($attachmentPath, true, true);
    }
    exit();
} else {
    //
    redirect_header('index.php', 3, _CO_XNEWSLETTER_WARNING_NOPERMISSIONS);
    exit();
}
