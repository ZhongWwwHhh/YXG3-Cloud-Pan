<?php
	session_start();
	if(isset($_COOKIE[session_name()])){
		setcookie(session_name(), '', time()-3600, '/');
	}
	session_destroy();
	header('location:/');
?>