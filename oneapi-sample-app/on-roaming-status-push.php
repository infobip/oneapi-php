<?php

require_once 'app.php';

$result = DataConnectionProfileClient::unserializeRoamingStatus();

// Process the roaming status here...

// We'll just save this object:
$fileName = PUSH_LOG_DIRECTORY . '/roaming-status-' . strftime('%Y-%m-%d %H:%M') . '.txt';
$data = print_r($result, true);

file_put_contents($fileName, $data);

// Not needed, but just for testing:
echo 'OK';
