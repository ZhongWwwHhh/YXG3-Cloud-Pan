<?php
// check input
function checkStr($str)
{
    $res = preg_match('/^[A-Za-z0-9]+$/u', $str);
    return $res ? TRUE : FALSE;
}
