<?php
/* use GET to send filepath
 * back: fail/success, filelist 
 */
header('Content-Type:application/json;charset=utf8');
header('Cache-Control:max-age=10,must-revalidate');

function fail()
{
    $return = array('success' => 'fail');
    echo json_encode($return);
    exit;
}

isset($_GET['filepath']) ? $filepath = $_GET['filepath'] : fail();
//strlen($filepath) == 5 || fail();

require_once '../../function/inputcheck.php';
if (checkStr($filepath)) {
    $dir = '../../../storage/' . $filepath;
    if (is_dir($dir)) {
        $return = array();
        $data = scandir($dir);
        foreach ($data as $value) {
            if ($value != '.' && $value != '..' && $value != 'up') {
                $return[] = $value;
            }
        }
        $return['success'] = 'success';
        echo json_encode($return);
    } else {
        fail();
    }
} else {
    fail();
}
