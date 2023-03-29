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
$fullpath = $download_path . $filename;

if (!file_exists($fullpath)) {
    bad();
} else {
    $chunksize = 5 * (1024 * 1024);
    $size = filesize($fullpath);

    header('Content-Type:application/octet-stream;charset=utf8');
    // cache 2 days
    header('cache-control:public,max-age=172800');
    header('Accept-Ranges:bytes');
    header('Content-Disposition:attachment;filename=' . str_replace('+', ' ', urlencode($filename)));
    header('Content-Length:' . $size);

    if ($size > $chunksize) {
        $handle = fopen($fullpath, "rb");
        while (!feof($handle)) {
            print(@fread($handle, $chunksize));
            ob_flush();
            flush();
        }
        fclose($handle);
    } else {
        readfile($fullpath);
    }
    exit;
}
