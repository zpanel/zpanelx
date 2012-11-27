<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @package phpMyAdmin
 */
if (!defined('PHPMYADMIN')) {
    exit;
}

/**
 *
 */
if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
    die("GLOBALS overwrite attempt");
}

/**
 * Sends http headers
 */
$GLOBALS['now'] = gmdate('D, d M Y H:i:s') . ' GMT';
/* Prevent against ClickJacking by allowing frames only from same origin */
if (!$GLOBALS['cfg']['AllowThirdPartyFraming']) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Security-Policy: allow \'self\'; options inline-script eval-script; frame-ancestors \'self\'; img-src \'self\' data:; script-src \'self\' www.phpmyadmin.net');
}
header('Expires: ' . $GLOBALS['now']); // rfc2616 - Section 14.21
header('Last-Modified: ' . $GLOBALS['now']);
header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
if (!defined('IS_TRANSFORMATION_WRAPPER')) {
    // Define the charset to be used
    header('Content-Type: text/html; charset=' . $GLOBALS['charset']);
}
?>
