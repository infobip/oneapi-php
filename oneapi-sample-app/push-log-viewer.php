<?php

$empty = array_key_exists('empty', $_GET);

$files = array();
if ($handle = opendir('push_log')) {
    while (false !== ($entry = readdir($handle))) {
        if($entry != '.' && $entry != '..') {
            if($empty) {
                unlink('push_log/' . $entry);
            } else {
                $files[] = $entry;
            }
        }
    }

    closedir($handle);
}

?>
<html>
    <head>
        <title>Push log viewer</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Messages pushed to this app</h1>
        <div style="border:1px solid black;">
            <ul>
                <?php foreach($files as $file): ?>
                    <li/> <a href="push_log/<?php echo $file; ?>"><?php echo $file ?></a>
                <?php endforeach; ?>
            </ul>
        </div>
        <p>
            <a href="push-log-viewer.php?empty=true">Empty log</a>
            <a href="index.php">Home</a>.
        </p>
    </body>
</html>
