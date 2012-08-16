<?php

require_once 'oneapi/client.php';

# example:data-connection-client
$client = new DataConnectionProfileClient(USERNAME, PASSWORD);
$client->login();
# ----------------------------------------------------------------------------------------------------

# example:retrieve-roaming-status
$response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);
# ----------------------------------------------------------------------------------------------------

echo 'HLR result:', $response, "\n";

//Logs::printLogs();
