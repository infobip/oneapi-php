<?php

require_once 'app.php';

$parts = explode('/', $_SERVER['REQUEST_URI']);
$parts[sizeof($parts) - 1] = 'on-delivery-push.php';
$notifyURLExample = 'http://' . $_SERVER['HTTP_HOST'] . '/' . implode('/', $parts);

?>
<html>
    <head>
        <title>Send message example</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Send message example</h1>
        <?php showFormMessage() ?>
        <form method="POST" action="send-message-action.php">
            <fieldset>
                <legend>SMS message details:</legend>

                From address:<br/>
                <input type="text" name="from" value="<?php echo getFormParam('from') ?>" size="15"/><br/><br/>

                To address:<br/>
                <input type="text" name="to" value="<?php echo getFormParam('to') ?>" size="15"/><br/><br/>

                Message:<br/>
                <textarea name="message" cols="80" rows="3"><?php echo getFormParam('message') ?></textarea><br/><br/>

                Notify URL:<br/>
                <input type="text" name="notifyURL" value="<?php echo getFormParam('notifyURL') ?>" size="80"/><br/>
                Use <span style="font-style:italic"><?php echo $notifyURLExample ?></span> and <b>change the server address</b> to be sure the URL is accessible from the internet. 
                <br/>
                Check <b><a href='push-log-viewer.php'>here</a></b> to see all push requests received.
            </fieldset>
            <br/>
            <input type="submit" value="Send message" />
        </form>
    </body>
</html>
