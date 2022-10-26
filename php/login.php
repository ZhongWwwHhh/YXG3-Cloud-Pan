<!DOCTYPE html>
<html lang="zh-cn">

<?php
$uid=$_POST["uid"];
if (!$uid==null) {
    $url=$uid;
} else {
    $url="/404";
}
?>

<head>
    <meta charset="UTF-8">
    <title>Logining</title>
    <link rel="stylesheet" type="text/css" href="/css/loading.css">
    <meta http-equiv="refresh" content="1;url=/file/<?php echo $url; ?>">  
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

