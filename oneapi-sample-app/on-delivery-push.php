<?php

require_once 'app.php';

$result = SmsClient::unserializeDeliveryStatus();

// Process the delivery status here...

// We'll just save this object:
$fileName = PUSH_LOG_DIRECTORY . '/delivery-status-' . strftime('%Y-%m-%d %H:%M') . '.txt';
$data = print_r($result, true);

file_put_contents($fileName, $data);

// Not needed, but just for testing:
echo 'OK';
