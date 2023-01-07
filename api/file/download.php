<?php
/* use GET to send filepath & filename
 * back: bad, file(download)
 */

$filepath = $_GET['filepath'];
$filename = $_GET['filename'];
$download_path = "../../../storage/$filepath/";

if (!file_exists($download_path . $filename)) {
    header('Content-Type:text/html;charset=utf8');
    header('cache-control:public,max-age=3600');
    echo 'bad';
    exit;
} else {
    header('Content-Type:application/octet-stream;charset=utf8');
    header('cache-control:public,max-age=172800');
    header('Accept-Ranges:bytes');
    header('Content-Disposition:attachment;filename=' . str_replace('+',' ',urlencode($filename)));
    header('Content-Length:' . filesize($download_path . $filename));
    readfile($download_path . $filename);
}
