<?php
/* use POST to send name
 * back: bad, lightname & filepath & write
 */
header('Content-Type:application/json;charset=utf8');
header('cache-control:no-store');

function bad()
{
    echo 'bad';
    exit;
}

// check POST input
isset($_POST['name']) || bad();
$name = $_POST['name'];

require_once '../../function/inputcheck.php';
checkStr($name) || bad();

// seek information
require_once '../../function/mysqli.php';
if (strlen($name) == 16) {
    // uuid
    $row = sqliSelect($name, 'uuid', 'user');
    if (sqliNumRow($row) == 1) {
        $arr = sqliGetArray($row);
        $return = array('lightname' => $arr['lightname'], 'filepath' => $arr['filepath'], 'write' => true);

        // start session
        require_once '../../function/session.php';
        sessionStart(true);

        $_SESSION['lightname'] = $arr['lightname'];
        $_SESSION['filepath'] = $arr['filepath'];
        $_SESSION['write'] = true;

        // clean file
        is_dir('../../../storage/' . $_SESSION['filepath'] . '/up/') && (require_once '../../function/file.php') . (delDir('../../../storage/' . $_SESSION['filepath'] . '/up/'));

        // return
        echo json_encode($return);
    } else {
        echo 'bad';
    }
} elseif (strlen($name) <= 10) {
    // lightname
    $row = sqliSelect($name, 'lightname', 'user');
    if (sqliNumRow($row) == 1) {
        $arr = sqliGetArray($row);
        $return = array('lightname' => $arr['lightname'], 'filepath' => $arr['filepath'], 'write' => false);
        echo json_encode($return);
    } else {
        echo 'bad';
    }
} else {
    echo 'bad';
}
sqliClose();
exit;
