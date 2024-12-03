<?php

// Directory indexer
// base directory = files/
$base_directory = "files/";

$title = "";
$message = "";
if (isset($_GET['dir'])) {
    $requested_directory = $_GET['dir'];
}
else {
    $requested_directory = "";
}
// remove any ../ from the end of path
$requested_directory = preg_replace('/\.\.\//', '', $requested_directory);
$dirs = [];
$files = [];
$parent_dir = "";
if ($requested_directory != "") {
    $parent_dir = dirname($requested_directory);
    if ($parent_dir != ".") {
        $parent_dir = $parent_dir . "/";
    }
    if ($requested_directory == ".") {
        $requested_directory = "";
    }
}
$directory = $base_directory . $requested_directory;

$path = preg_replace('/^'.$base_directory, '/', $requested_directory);
if ($path == "") {
    $path = "/";
}

// pass Download request
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    $file_path = $directory . $file;
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "File not found";
    }
} else {
    // List files
    $contents = scandir($directory);
    foreach ($contents as $content) {
        if ($content == "." || $content == "..") {
            continue;
        } else {

            if (is_dir($directory . $content)) {
                $dirs[] = [
                    "name" => $content,
                    "size" => "",
                    "type" => "directory",
                    "link" => "dir=" . $requested_directory . $content . "/"
                ];
            } else {
                $files[] = [
                    "name" => $content,
                    "size" => filesize($directory. "/" . $content),
                    "type" => mime_content_type($directory . "/" . $content),
                    "link" => "file=" . $content . "&dir=" . $requested_directory,
                ];
            }
        }
    }
}

include 'dirShow.html';
?>


