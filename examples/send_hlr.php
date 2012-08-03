<?php

require_once 'oneapi/client.php';

$client = new DataConnectionProfileClient(USERNAME, PASSWORD);
$client->login();

$response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);

echo 'HLR result:', $response, "\n";

//Logs::printLogs();
