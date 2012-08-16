<?php

require_once 'oneapi/client.php';

# example:on-delivery-notification
$result = SmsClient::unserializeDeliveryStatus();
# ----------------------------------------------------------------------------------------------------

echo 'OK';

// Process $result here
