<?php 
session_start();

if (!isset($_SESSION['lightname'])) {
    header("Location:/html/login.html");
    exit;
}

$filepath = $_SESSION["filepath"];

$path = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $filename = test_input($_POST["filename"]);
  //如果文件夹下有文件，或者目录，均不能删除成功 
 $file_path="../file/$filepath/$filename"; 
 if(is_file($file_path)) { 
	if(unlink($file_path)) { 
		header("refresh:0;url = /php/file.php");
      	echo "删除成功，跳转到主页..."; 
  	} 
  	else { 
  		header("refresh:1;url = /php/file.php");
   		echo "删除失败，跳转到主页..."; 
  	} 
 } 
 else { 
 	header("refresh:1;url = /php/file.php");
 	echo "文件不存在，跳转到主页..."; 
 } 
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
