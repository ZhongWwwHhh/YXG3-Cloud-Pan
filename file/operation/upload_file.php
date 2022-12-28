<?php
	session_start();

    if (!isset($_SESSION['lightname'])) {
        header('Location:/');
        exit;
    }

	$filepath = $_SESSION["filepath"];
	header("Content_type:text/html;charset=utf8");

	$imgname = $_FILES['myFile']['name'];
    $tmp = $_FILES['myFile']['tmp_name'];
	$error=$_FILES['myFile']['error'];
		
   move_uploaded_file($tmp,"../../storage/$filepath/".$imgname);
 
   if ($error==0) {
  			
  			header("refresh:0;url = /php/file.php");
        	echo "上传成功！跳转至主页...";
   }else{
		  switch ($error){
		    case 1:
		      header("refresh:1;url = /php/file.php");
		      echo "超过了上传文件的最大值，请上传400M以下文件！跳转至主页...";
		      break;
		    case 2:
		      header("refresh:1;url = /php/file.php");
		      echo "上传文件过多，请一次上传20个及以下文件！跳转至主页...";
		      break;
		    case 3:
		      header("refresh:1;url = /php/file.php");
		      echo "文件并未完全上传，请再次尝试！跳转至主页...";
		      break;
		    case 4:
		      header("refresh:1;url = /php/file.php");
		      echo "未选择上传文件！跳转至主页...";
		      break;
		    case 5:
		      header("refresh:1;url = /php/file.php");
		      echo "上传文件为0!跳转至主页...";
	      	 break;
	}
}
 
?>