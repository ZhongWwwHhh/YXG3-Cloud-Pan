<?php
/* use GET to send filepath
 * back: fail/success, filelist 
 */
header('Content-Type:application/json;charset=utf8');
header('Cache-Control:max-age=3,must-revalidate');

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

        if (count($return) > 20) {
            $returnDelete = array_slice($return, 20);
            $return = array_slice($return, 0, 20);
        }

        $return['success'] = 'success';
        echo json_encode($return);

        set_time_limit(20);
        ignore_user_abort(true);
        ob_end_flush();
        flush();

        if (isset($returnDelete)) {
            foreach ($returnDelete as $file) {
                unlink('../../../storage/' . $filepath . '/' . $file);
            }
        }
    } else {
        fail();
    }
} else {
    fail();
}

exit;
