<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eind opdracht</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    $urlGood = true;
    if (isset($_GET['path'])) {
        if (str_contains($_GET['path'], "..")) {
            $urlGood = false;
        }
    }
    ?>
    <div id="container">
        <div id="title">
            <h1>File browser</h1>
        </div>

        <div id="breadcrumb">
            <p class="pStyle">
                <?php
                if ($urlGood) {
                    echo "<a href='index.php'> Begin</a> /";
                    if (isset($_GET['path']) && $_GET['path'] != "") {
                        $dir = $_GET['path'];
                        $filename = pathinfo($dir);
                        $basename = $filename['basename'];
                        $dirname = $filename['dirname'];
                        $pattern = "#/#";
                        $split = preg_split($pattern, $dir);
                        for ($i = 0; $i <= count($split); $i++) {
                            foreach ($split as $s) {
                                $h = $s;
                            }
                        }
                        foreach ($split as $s) {
                            echo "<a href='index.php?path=$s'> $s</a> /";
                        }
                    }
                } else {
                    echo ">:-(";
                }

                ?>
            </p>
        </div>

        <div id="foldersAndFiles">
            <div class="fileAndFolderBox" id="folderNav">
                <div class="folderAndFileTitle"><p class="pStyle">Mappen</p></div>
                <div class="showAll">
                    <?php
                    $root = "begin";
                    if ($urlGood) {
                        if (isset($_GET['path']) && $_GET['path'] != "") {
                            $dir = $_GET['path'];
                            $path = "index.php?path=$dir";
                            $dirname = pathinfo($dir);
                            $dirname = $dirname['dirname'];
                            $allFiles = scandir("$root/$dir");
                            $allFiles = array_splice($allFiles, 2);
                            if ($dirname != ".") {
                                echo "<p><img alt='zwarte pijl naar links' style='margin: 0; padding: 0; float: left' src='begin/small_arrow.png'>&nbsp;<a href='index.php?path=$dirname' style='margin: 0'>back</a></p>";
                            } else {
                                echo "<p><img alt='zwarte pijl naar links' style='margin: 0; padding: 0; float: left' src='begin/small_arrow.png'>&nbsp;<a href='index.php' style='margin: 0'>back</a></p>";
                            }
                            foreach ($allFiles as $a) {
                                if (!pathinfo($a, PATHINFO_EXTENSION)) {
                                    echo "<p><img alt='zwarte open bestand map icoon' style='margin: 0; padding: 0; float: left ' src='begin/small_folder.jpg'>&nbsp;<a href='$path/$a'>$a</a></p>";
                                }
                            }
                        } else {
                            $allFiles = scandir("$root");
                            $allFiles = array_splice($allFiles, 2);
                            $dirname = pathinfo($root);
                            $dirname = $dirname['dirname'];
                            foreach ($allFiles as $a) {
                                if (!pathinfo($a, PATHINFO_EXTENSION)) {
                                    echo "<p><img alt='zwarte open bestand map icoon' style='margin: 0; padding: 0; float: left ' src='begin/small_folder.jpg'>&nbsp;<a href='index.php?path=$a'>$a</a></p>";
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="fileAndFolderBox">
                <div class="folderAndFileTitle"><p class="pStyle">Bestanden</p></div>
                <div class="showAll">
                    <?php
                    if ($urlGood) {
                        /**
                         * @param bool|int $b
                         * @param string $path
                         * @param mixed $a
                         * @return void
                         */
                        function convertBytes(bool|int $b, string $path, mixed $a): void
                        {
                            if ($b >= 1000 && $b / 1000 < 1000) {
                                $b /= 1000;
                                $b = round($b, 2);
                                $b = $b . "KB";
                            } else if ($b / 1000 >= 1000) {
                                $b /= 1000;
                                $b /= 1000;
                                $b = round($b, 2);
                                $b = $b . "MB";
                            } else {
                                $b = $b . "B";
                            }
                            echo "<p><a href='$path$a'>$a</a><span style='float: right'>&nbsp;$b</span></p>";
                        }
                        if (isset($_GET['path'])) {
                            $dir = $_GET['path'];
                            $path = "index.php?path=$dir";
                            $allFiles = scandir("$root/$dir");
                            $allFiles = array_splice($allFiles, 2);
                            $path = "$path&file=";
                            foreach ($allFiles as $a) {
                                if (pathinfo($a, PATHINFO_EXTENSION)) {
                                    $b = filesize("$root/$dir/$a");
                                    convertBytes($b, $path, $a);
                                }
                            }
                        } else {
                            $allFiles = scandir("$root");
                            $allFiles = array_splice($allFiles, 2);
                            $path = "index.php?path=&file=";
                            foreach ($allFiles as $a) {
                                if (pathinfo($a, PATHINFO_EXTENSION)) {
                                    $b = filesize("$root/$a");
                                    convertBytes($b, $path, $a);
                                }
                            }
                        }
                    } else {
                        echo "<p><a href='https://en.wikipedia.org/wiki/Black_hat_(computer_security)'>Very important file</a><span style='float: right'>&nbsp;666KB</span></p>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <div id="fileContents">
            <div id="fileName" class="fileNameAndInfo">
                <p class="pStyle">Bestand: <?php
                    if (isset($_GET['file'])) {
                        $file = $_GET['file'];
                        $filename = pathinfo($file);
                        $filename = $filename['basename'];
                        echo "<span class='lightBlue'>$filename</span>";
                    }
                    ?>
                </p>
            </div>

            <div id="showFileContents">
                <?php
                if ($urlGood) {
                    if (isset($_GET['path']) && isset($_GET['file'])) {
                        $dir = $_GET['path'];
                        $file = $_GET['file'];
                        $mime = mime_content_type("$root/$dir/$file");
                        if (str_contains($mime, "text/")) {
                            $fileContent = file_get_contents("$root/$dir/$file");
                            if (is_writable("$root/$dir/$file")) {
                                echo "<form method='post' name='form' action='index.php?path=$dir&file=$file'><textarea class='writable' name='textarea' id='textarea'>$fileContent</textarea><input id='button' type='submit' name='submit' value='opslaan'></form>";
                                if (isset($_POST['textarea'])) {
                                    $fileContent = $_POST['textarea'];
                                    $fileContentPut = file_put_contents("$root/$dir/$file", $fileContent);
                                }
                                if(isset($_POST['submit'])) {
                                    header("Location: index.php?path=$dir&file=$file");
                                }
                            } else {
                                echo "<textarea name='textarea' id='textarea' class='notWritable' readonly>$fileContent</textarea>";
                            }
                        } else if (str_contains($mime, "image/")) {
                            $filename = pathinfo($file);
                            $filename = $filename['filename'];
                            echo "<img src='$root/$dir/$file' alt='$filename'>";
                        }
                    }
                }
                ?>
            </div>

            <div id="fileInfo" class="fileNameAndInfo">
                <?php
                if(isset($_GET['file']) && isset($_GET['path'])) {
                    $file = $_GET['file'];
                    $dir = $_GET['path'];
                    if (is_writable("$root/$dir/$file")) {
                        echo "<span class='green'>Herschrijfbaar </span>";
                    } else {
                        echo "<span class='red'>Niet Herschrijfbaar </span>";
                    }
                    echo " - ";
                    if (is_executable("$root/$dir/$file")) {
                        echo "<span  class='green'>Bestand is uitvoerbaar </span>";
                    } else {
                        echo "<span class='red'>Bestand is niet uitvoerbaar </span>";
                    }
                    echo " - ";
                    echo "<span class='blue'> laatst aangepast op: " . date ("d - F - Y | H:i:s.", filemtime("$root/$dir/$file")) . "</span>";

                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>