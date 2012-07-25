<?

require_once 'oneapi/client.php';
require_once 'oneapi/models.php';

$client = new DataConnectionProfileClient(USERNAME, PASSWORD);

$response = $client->retrieveRoamingStatusAsync(DESTINATION_ADDRESS);

Logs::printLogs();
