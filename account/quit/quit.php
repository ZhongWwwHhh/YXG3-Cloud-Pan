<?php
	session_start();
	$_SESSION = array();
	if(isset($_COOKIE[session_name()])){
		setcookie(session_name(), '', time()-3600, '/');
	}
	session_destroy();
	header('refresh:1;url = /');
    echo "跳转至主页...";

?>