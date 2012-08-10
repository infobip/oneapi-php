<?php

require_once 'oneapi/client.php';

$client = new DataConnectionProfileClient(USERNAME, PASSWORD);
$client->login();

# example:retrieve-roaming-status-with-notify-url
$response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS, NOTIFY_URL);
# ----------------------------------------------------------------------------------------------------

if(!$response->isSuccess()) {
    echo 'Error:', $response->exception, "\n";
    Logs::printLogs();
}

echo $response;
