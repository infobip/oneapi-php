<?php

use infobip\DataConnectionProfileClient;

require_once __DIR__ . '\..\oneapi\client.php';

# example:data-connection-client
$client = new DataConnectionProfileClient(USERNAME, PASSWORD);
# ----------------------------------------------------------------------------------------------------
//$client->login();
# example:retrieve-roaming-status
$response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);
echo 'Number context result: \n<br>';
echo 'servingMccMnc: ', $response->servingMccMnc,'\n<br>';
echo 'address: ', $response->address,'\n<br>';
echo 'currentRoaming: ', $response->currentRoaming,'\n<br>';
echo 'resourceURL: ', $response->resourceURL,'\n<br>';
echo 'retrievalStatus: ', $response->retrievalStatus,'\n<br>';
echo 'callbackData: ', $response->callbackData,'\n<br>';
echo 'extendedData: ', $response->extendedData,'\n<br>';
echo 'IMSI: ', $response->extendedData->imsi,'\n<br>';
echo 'destinationAddres: ', $response->extendedData->destinationAddress,'\n<br>';
echo 'originalNetworkPrefix: ', $response->extendedData->originalNetworkPrefix,'\n<br>';
echo 'portedNetworkPrefix: ', $response->extendedData->portedNetworkPrefix,'\n<br>';
# ----------------------------------------------------------------------------------------------------

//Logs::printLogs();
