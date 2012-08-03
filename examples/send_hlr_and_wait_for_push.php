<?php

require_once 'oneapi/client.php';

$client = new DataConnectionProfileClient(USERNAME, PASSWORD);
$client->login();

$response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);

if(!$response->isSuccess()) {
    echo 'Error:', $response->exception, "\n";
    Logs::printLogs();
}

echo $response;
