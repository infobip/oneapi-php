<?php

use infobip\SmsClient;

//require_once __DIR__ . '\..\oneapi\client.php';
require_once __DIR__ . '\vendor\autoload.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

# example:retrieve-inbound-messages
$inboundMessages = $smsClient->retrieveInboundMessages();

foreach($inboundMessages->inboundSMSMessage as $message) {
    echo $message->dateTime;
    echo $message->destinationAddress;
    echo $message->messageId;
    echo $message->message;
    echo $message->resourceURL;
    echo $message->senderAddress;
}
# ----------------------------------------------------------------------------------------------------

