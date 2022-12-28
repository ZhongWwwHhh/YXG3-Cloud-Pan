<?php

// information for mysql
$host = 'localhost';
$db_username = 'pan';
$db_pwd = 'jA5R2P7fZySfT2Kt';
$db_name = 'pan';
// start connect to mysql db
$sqliConnection = mysqli_connect($host, $db_username, $db_pwd, $db_name);


// must use when finish
function sqliClose()
{
    global $sqliConnection;
    mysqli_close($sqliConnection);
    return true;
}

function sqliSelect($selectContent, $selectColumn, $table)
{
    global $sqliConnection;
    $sqliQuery = mysqli_query($sqliConnection, "select * from `$table` where `$selectColumn`='$selectContent'");
    return $sqliQuery;
}

function sqliGetArray($sqliQuery)
{
    return mysqli_fetch_assoc($sqliQuery);
}

function sqliNumRow($sqliQuery)
{
    return mysqli_num_rows($sqliQuery);
}

function sqliInsert($insertArray, $table)
{
    global $sqliConnection;
    $columns = implode("`, `", array_keys($insertArray));
    $escaped_values = array_map(array($sqliConnection, 'real_escape_string'), array_values($insertArray));
    $values  = implode("', '", $escaped_values);
    $sql = "INSERT INTO `$table` (`$columns`) VALUES ('$values')";
    if ($sqliConnection->query($sql) === true) {
        return true;
    } else {
        return false;
    }
}
