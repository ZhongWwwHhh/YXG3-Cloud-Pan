<?php
require_once '../../function/session.php';
sessionStart();
// not login
$_SESSION['lightname'] || (header('Location:/')) . (exit);


$lightname = $_SESSION['lightname'];
$filepath = $_SESSION['filepath'];

?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <title>Your Files</title>
    <link rel="stylesheet" href="panel.css">
</head>

<body>
    <div id="wrapper">

        <header class="SiteHeader">
            <div class="inner">
                <a href="index.html" class="logo">轻量云网盘</a>
                <nav id="nav">
                    <a><?php echo $lightname ?> 已登录</a>
                    <a href="/account/quit/quit.php">注销</a>
                </nav>
            </div>
        </header>

        <aside class="NavSidebar">
            <nav>
                <h2>使用说明</h2>
                <ul>
                    <li>账户注册后，默认分配云空间。</li>
                    <li>注意数据安全，隐私数据请勿上传。</li>
                    <li>上传文件时点击浏览选择本地文件进行上传。</li>
                    <li>下载文件时点击云盘内文件后点击下载。</li>
                    <li>删除文件时点击云盘内文件后点击删除。</li>
                </ul>
            </nav>
        </aside>

        <main class="main">
            <article class="Content">
                <header class="ArticleHeader">
                    <h2>欢迎</h2>
                </header>

                <div id="file">
                    <?php
                    if ($_SESSION['write'] == true) {
                        echo <<<EOT
                            <!--文件上传-->
                            <h2>&nbsp;&nbsp;文件上传</h2>
                            <form action="../../api/file/upload.php" method="post" enctype="multipart/form-data">
                                <input type="file" name="myFile" id="test3" style="width: 100%;height: 30px; border-radius: 10px; box-shadow: 0 0 5px rgba(0,113,241,1);" /><br />
                                <input name="blobnum" />
                                <input name="totalblobnum" />
                                <input name="filename" />
                                <input type="submit" value="上传" class="button_css" />
                            </form>
                        EOT;
                    }
                    ?>

                    <!--文件下载-->
                    <br />
                    <br />
                    <h2>&nbsp;&nbsp;文件下载</h2>
                    <form action="../../api/file/download.php" method="get">
                        <input id="test2" name="filename" type="text" readonly="readonly" style="width: 100%;height: 30px; border-radius: 10px; box-shadow: 0 0 5px rgba(0,113,241,1);" value="" placeholder="请在右侧选择文件" />
                        <input type="hidden" name="filepath" value="<?php echo $filepath ?>" readonly />
                        <input type="submit" value="点击下载" class="button_css" />
                    </form>
                </div>

                <div id="filelist">
                    <h2 style="color: #33CCCC;">云盘内文件:</h2>
                    <hr width="100%" align="left">
                    <br />
                    <?php    ///查询目录下文件列表

                    if (!is_dir("../../../storage/$filepath/")) {
                        mkdir("../../../storage/$filepath/");
                    }
                    function getDirContent($path)
                    {
                        if (!is_dir($path)) {
                            return false;
                        }
                        $arr = array();
                        $data = scandir($path);
                        foreach ($data as $value) {
                            if ($value != '.' && $value != '..') {
                                $arr[] = $value;
                            }
                        }
                        return $arr;
                    }

                    $file = getDirContent("../../../storage/$filepath/");

                    $arrlength = count((array)$file);
                    ?>
                    <ul>
                        <?php
                        for ($x = 0; $x < $arrlength; $x++) {    //将目录下所有文件输出
                            echo "<li><a href=\"javascript:changeText('$file[$x]')\";" . ">$file[$x]</a></li>";  //点击超链接自动向input中填入
                        }
                        ?>
                    </ul>
                </div>
            </article>
        </main>
    </div>
    <script>
        function changeText(text) //点击超链接向input填入内容
        {
            var element2 = document.getElementById("test2");
            element2.value = text;
        }
    </script>
</body>

</html>