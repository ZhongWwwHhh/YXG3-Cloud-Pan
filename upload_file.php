<!DOCTYPE html>
<html lang="zh-cn">

<?php
$url = null;
function err($errCode)
{
	$url = "/$errCode.html";
	exit;
}

// check session first

// 允许上传的后缀
$allowedExts = array("gif", "jpeg", "jpg", "png", "ppt", "pptx", "doc", "docx", "xls", "xlsx");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);     // 获取文件后缀名
if (($_FILES["file"]["size"] < 1024000000) && in_array($extension, $allowedExts)) {
	if ($_FILES["file"]["error"] > 0) {
		err(420);
	} else {
		/*
		if (file_exists("upload/" . $_FILES["file"]["name"]))
		{
			echo $_FILES["file"]["name"] . " 文件已经存在。 ";
		}
		else
		{
			// 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
			move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
			echo "文件存储在: " . "upload/" . $_FILES["file"]["name"];
		}
		*/

		// delete oldfile that has existed
		if (file_exists($fileSavedPath)) {
			if (!unlink($fileSavedPath)) {
				err(500);
			}
		}

		// seve file
		if (!move_uploaded_file($_FILES["file"]["tmp_name"], $fileSavedPath)) {
			err(500);
		}

		// back
		$url = '/html/file.html';
	}
} else {
	err(420);
}
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="/css/loading.css">
	<meta http-equiv="refresh" content="1;url=/<?php echo $url; ?>">
</head>

<body>
	<div class="loadingThree">
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
	</div>
</body>

</html>