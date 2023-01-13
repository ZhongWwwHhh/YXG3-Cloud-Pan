<?php
session_start();
isset($_COOKIE[session_name()]) && setcookie(session_name(), '', time() - 3600, '/');
session_destroy();
echo 'success';
