<?php
$url = null;
function err($errCode)
{
    $url = "/$errCode.html";
    header("Location:$url");
    exit;
}

// check session first

// 允许上传的后缀
$allowedExts = array("gif", "jpeg", "jpg", "png", "ppt", "pptx", "doc", "docx", "xls", "xlsx");
// 获取文件后缀名
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

// less than 1GB
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

        // test
        $fileSavedPath = '../file/404.html';

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
        header("Location:/html/file.html");
    }
} else {
    err(420);
}
