<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php

$basePath = !empty($_GET['path']) ? $_GET['path'] : './';
// if (empty($_GET['path'])) header("Location: $basePath");

$dir_array = [];
$file_array = [];


if (is_dir($basePath)) {
    $dd = opendir($basePath);

    while ($item = readdir($dd)) {
        if (is_dir($basePath . $item)) array_push($dir_array, $item);
        if (is_file($basePath . $item)) array_push($file_array, $item);
    }
} else {
    array_push($dir_array, '..');
}
sort($dir_array);
sort($file_array);

$list = '';

foreach ($dir_array as $dir) {
    $path = $basePath . $dir . '/';

    if ($dir == '.') continue;

    if ($dir == '..') {
        $basePathArr = explode('/', $basePath);
        if (is_dir($basePath)) array_pop(($basePathArr));

        $lastItem = array_pop($basePathArr);

        if ($lastItem != '.' && $lastItem != '..') {
            $path = implode('/', $basePathArr) . '/';
        }
    }

    $list .= '<li><a href="/admin/?path=' . $path . '">' . $dir . '</a></li>';
}

foreach ($file_array as $file) {
    $list .= '<li><a href="/admin/?path=' . $basePath . $file . '">' . $file . '</a></li>';
}

if (!empty($list)) echo '<ul><li><a href="/admin/">.</a></li>' . $list . '</ul>';

if (is_file($basePath)) {
    $content = htmlspecialchars(file_get_contents($basePath));

    echo '<form method="POST">
    <textarea name="filecontent">' . $content . '</textarea>
    <button>Save</button>
    </form>';
}
if (!empty($_POST['filecontent'])) {
    file_put_contents($basePath, htmlspecialchars_decode($_POST['filecontent']));
    header("location:" . $_SERVER['REQUEST_URI']);
}
?>

<form method="POST">
    <?php 
    if (empty($_GET['path'])) {$path = './';}  
    else $path = $_GET['path'] . (!empty($_GET['file']) ? '/' . $_GET['file'] : ''); ?>
    
    <input type="hidden" name="path" value="<?php echo $path; ?>" />
    <input type="hidden" name="action" value="delete" />

    Address: <?php echo $path; ?>
    <button>Delete</button>
</form>

<form method="POST">
    <input type="hidden" name="action" value="create" />

    Create: 
    <input type="text" name="name" />
    <input checked type="radio" name="type" value="file" /> - File
    <input type="radio" name="type" value="directory" /> - Directory
    <button>Go</button>
</form>

<?php 
if (!empty($_POST['action']) && !empty($_POST['path']) && $_POST['action'] == 'delete') {
    if (is_dir($_POST['path'])) rmdir($_POST['path']);
    else if (is_file($_POST['path'])) unlink($_POST['path']);

    header("Location:" . $_SERVER['REQUEST_URI']);
}

if (!empty($_POST['action']) && !empty($_POST['type']) && !empty($_POST['name']) && $_POST['action'] == 'create') {
    if (empty($_GET['path'])) {$path = './' . '/' . $_POST['name'];}  
    else $path = $_GET['path'] . '/' . $_POST['name'];
        
        switch ($_POST['type']) {
            case 'file':
                $file = fopen($path, 'w');
                fclose($file);
            break;
            case 'directory':
                mkdir($path);
            break;
        }

        header("Location:" . $_SERVER['REQUEST_URI']);
    }
    // print_r($_POST);
?>

    <!-- Uploader -->

<form action="./uploader.php" method="POST" enctype="multipart/form-data">
<input type="file" multiple name="files[]" />
<button>Upload</button>
</form>

</body>
</html>