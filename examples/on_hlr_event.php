<?

//require_once 'yapd/dbg.php';

require_once 'oneapi/client.php';

$requestBody = file_get_contents("php://input");
echo 'OK';

$result = Conversions::createFromJSON('TerminalRoamingStatusList', $requestBody);

// Process $result here
