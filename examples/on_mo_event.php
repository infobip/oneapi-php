<?php

use infobip\SmsClient;

require_once __DIR__ . '\..\oneapi\client.php';

define(FILE_NAME, '../message-'.mktime(true));

# example:on-mo
// returns a single message not array of messages
$inboundMessages = SmsClient::unserializeInboundMessages();

// Process $inboundMessages here, e.g. just save it to a file:
$f = fopen(FILE_NAME, 'w');
fwrite($f, "\n-------------------------------------\n");
fwrite($f, 'dateTime: ' . $inboundMessages->dateTime . "\n");
fwrite($f, 'destinationAddress: '  . $inboundMessages->destinationAddress . "\n");
fwrite($f, 'messageId: '  . $inboundMessages->messageId . "\n");
fwrite($f, 'message: '  . $inboundMessages->message . "\n");
fwrite($f, 'resourceURL: '  . $inboundMessages->resourceURL . "\n");
fwrite($f, 'senderAddress: '  . $inboundMessages->senderAddress . "\n");
# ----------------------------------------------------------------------------------------------------

echo 'OK';
