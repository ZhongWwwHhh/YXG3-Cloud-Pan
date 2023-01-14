<?php
/* use GET to send filepath & filename
 * back: bad, file(download)
 */

function bad()
{
    header('Content-Type:text/plain;charset=utf8');
    header('cache-control:public,max-age=3600');
    echo 'bad';
    exit;
}

isset($_GET['filepath'], $_GET['filename']) || bad();
require_once '../../function/inputcheck.php';
checkStr($_GET['filepath']) || bad();

$filepath = $_GET['filepath'];
$filename = $_GET['filename'];
$download_path = "../../../storage/$filepath/";

if (!file_exists($download_path . $filename)) {
    bad();
} else {
    header('Content-Type:application/octet-stream;charset=utf8');
    // cache 2 days
    header('cache-control:public,max-age=172800');
    header('Accept-Ranges:bytes');
    header('Content-Disposition:attachment;filename=' . str_replace('+', ' ', urlencode($filename)));
    header('Content-Length:' . filesize($download_path . $filename));
    echo readfile($download_path . $filename);
}
