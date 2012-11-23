<?php

require_once 'oneapi/client.php';

# example:data-connection-client
$client = new DataConnectionProfileClient(USERNAME, PASSWORD);
$client->login();
# ----------------------------------------------------------------------------------------------------

# example:retrieve-roaming-status
$response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);
echo 'HLR result: \n';
echo 'servingMccMnc: ', $response->servingMccMnc,'\n';
echo 'address: ', $response->address,'\n';
echo 'currentRoaming: ', $response->currentRoaming,'\n';
echo 'resourceURL: ', $response->resourceURL,'\n';
echo 'retrievalStatus: ', $response->retrievalStatus,'\n';
echo 'callbackData: ', $response->callbackData,'\n';
echo 'extendedData: ', $response->extendedData,'\n';
# ----------------------------------------------------------------------------------------------------

//Logs::printLogs();
