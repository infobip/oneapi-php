<?php

use infobip\UssdClient;

//require_once __DIR__ . '\..\oneapi\client.php';
require_once __DIR__ . '\vendor\autoload.php';

$ussdClient = new UssdClient(USERNAME, PASSWORD);

$response = null;
while($response != '1') {
    $result = $ussdClient->sendMessage(DESTINATION_ADDRESS, "Your favourite language is\n 1. PHP\n2. Python");
    $response = $result->message;
}
$ussdClient->stopSession(DESTINATION_ADDRESS, "Cool");
