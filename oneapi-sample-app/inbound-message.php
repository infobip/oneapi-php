<?php

require_once 'app.php';

$parts = explode('/', $_SERVER['REQUEST_URI']);
$parts[sizeof($parts) - 1] = 'on-inbound-message.php';
$notifyURLExample = 'http://' . $_SERVER['HTTP_HOST'] . '/' . implode('/', $parts);

?>
<html>
    <head>
        <title>Inbound message example</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Inbound message example</h1>
        <p>
            Use <span style="font-style:italic"><?php echo $notifyURLExample ?></span> in your parseco administation page. Be sure to <b>change the server address</b> because the URL is accessible from the internet. 
            <br/>
            Check <b><a href='push_log' target='_blank'>here</a></b> to see all inbound push requests received.
        </p>
    </body>
</html>
