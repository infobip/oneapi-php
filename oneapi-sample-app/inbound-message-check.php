<?php

require_once 'app.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);

$result = $smsClient->retrieveInboundMessages();
$result->inboundSMSMessage

?>
<html>
    <head>
        <title>Inbound messages</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Inbound messages</h1>
        <?php if($result->inboundSMSMessage): ?>
            <ul>
                <?php foreach($result->inboundSMSMessage as $message): ?>
                   <li/> Message <b><?php echo $message->senderAddress ?></b> from <b><?php echo $message->message ?></b>.
                <?php endforeach ?>
            </ul>
        <?php else: ?>
            <h2>Nothing found</h2>
        <?php endif ?>
    </body>
</html>
