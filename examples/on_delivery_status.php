<?php

/*
 * When the message is called with the notifyURL param, we will push an HTTP 
 * request. This is the script to process this request.
 */

require_once 'oneapi/client.php';


# example:on-delivery-notification
$result = SmsClient::unserializeDeliveryStatus();
# ----------------------------------------------------------------------------------------------------

echo 'OK';

// Process $result here
