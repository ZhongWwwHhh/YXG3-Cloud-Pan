<?php
/* use GET to send filepath
 * back: bad, none, filelist 
 */
header('Content-Type:application/json;charset=utf8');
header('Cache-Control:max-age=10,must-revalidate');

isset($_GET['filepath']) ? $filepath = $_GET['filepath'] : (print 'bad') . (exit);
//strlen($filepath) == 5 || (print 'bad') . (exit);

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
        echo json_encode($return);
    } else {
        echo 'none';
    }
} else {
    echo 'bad';
}
