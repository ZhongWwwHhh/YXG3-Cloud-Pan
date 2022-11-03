<?php
	session_start();
	$_SESSION = array();
	session_destroy();
	header("refresh:1;url = /html/login.html");
    echo "跳转至主页...";

?>