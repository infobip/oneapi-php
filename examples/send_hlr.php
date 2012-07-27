<?

require_once 'oneapi/client.php';

$client = new DataConnectionProfileClient(USERNAME, PASSWORD);

$response = $client->retrieveRoamingStatusAsync(DESTINATION_ADDRESS, NOTIFY_URL);

echo 'Hlr is now sent, the result will be pushed to ', NOTIFY_URL, "\n";

//Logs::printLogs();
