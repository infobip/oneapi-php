<?php

/*
 * When the message is called with the notifyURL param, we will push an HTTP
 * request. This is the script to process this request.
 */

use infobip\SmsClient;

require_once __DIR__ . '\..\oneapi\client.php';

define(FILE_NAME, '../delivery-'.mktime(true));

# example:on-delivery-notification
$result = SmsClient::unserializeDeliveryStatus();

// Process $result here, e.g. just save it to a file:
$f = fopen(FILE_NAME, 'w');
fwrite($f, "\n-------------------------------------\n");
fwrite($f, 'status: ' . $result->deliveryInfo->deliveryStatus . "\n") ;
fwrite($f, 'address: ' . $result->deliveryInfo->address . "\n");
fwrite($f, 'messageId: ' . $result->deliveryInfo->messageId . "\n");
fwrite($f, 'clientCorrelator: '. $result->deliveryInfo->clientCorrelator . "\n");
fwrite($f, 'callback data: ' . $result->callbackData . "\n");
fwrite($f, "\n-------------------------------------\n");
fclose($f);
# ----------------------------------------------------------------------------------------------------

echo 'OK';
