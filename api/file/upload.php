<?php
/* use POST to send fileName & blobNum & totalBlobNum & fileupload
 * back: bad, continue, fail, success
 * warn: login first, provide correct information
 * !!!: should prevent attack
 */
header('Content-Type:text/plain;charset=utf8');

function bad()
{
    echo 'bad';
    exit;
}

// POST input check
isset($_POST['filename'], $_POST['blobnum'], $_POST['totalblobnum'], $_FILES['fileupload']) || bad();
(is_numeric($_POST['blobnum']) && is_numeric($_POST['totalblobnum'])) || bad();
(strlen($_POST['filename']) <= 100 && $_POST['totalblobnum'] <= 200 && $_POST['blobnum'] <= $_POST['totalblobnum']) || bad();

// session
require_once '../../function/session.php';
sessionStart();

isset($_SESSION['lightname'], $_SESSION['write']) || (session_destroy()) . (bad());
$_SESSION['write'] == true || (session_destroy()) . (bad());

// file size, less than 1MB
$_FILES['fileupload']['size'] <= 1048576 || bad();


$filepath = $_SESSION['filepath'];
$fileName = $_POST['filename'];
$blobNum = $_POST['blobnum'];
$totalBlobNum = $_POST['totalblobnum'];
$upDir = '../../../storage/' . $filepath . '/up/';
$upFileDir = $upDir . $fileName . '_';
$upFile = $upFileDir . $blobNum;

session_write_close();

// move from tmp to uploadpath
if (!is_dir($upDir)) {
    mkdir($upDir);
}
move_uploaded_file($_FILES['fileupload']['tmp_name'], $upFile);

if ($blobNum >= $totalBlobNum) {
    // the last blob

    function fail()
    {
        require_once '../../function/file.php';
        delDir($upDir);
        echo 'fail';
        exit;
    }

    // marge
    // can't open
    if (!$outFile = @fopen('../../../storage/' . $filepath . '/' . date('YmdHis') . '-' . $fileName, "wb")) {
        fail();
    }

    // try to marge
    // lock
    if (flock($outFile, LOCK_EX)) {

        for ($i = 1; $i <= $totalBlobNum; $i++) {
            $in = $upFileDir . $i;

            // check blob exist and readable
            if (!file_exists($in) || !$inFile = @fopen($in, "rb")) {
                flock($outFile, LOCK_UN);
                fail();
            }

            // write
            while ($buff = fread($inFile, 4096)) {
                fwrite($outFile, $buff);
            }

            // close and delete
            @fclose($inFile);
            @unlink($in);
        }


        // unlock
        flock($outFile, LOCK_UN);
    } else {
        fail();
    }

    // write done, close
    @fclose($outFile);

    // delete upDir
    require_once '../../function/file.php';
    delDir($upDir);

    echo 'success';
    exit;
} else {
    // need next blob
    echo 'continue';
    exit;
}
