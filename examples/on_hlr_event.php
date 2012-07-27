<?

require_once 'yapd/dbg.php';

/*
require_once 'oneapi/Logs.class.php';
require_once 'oneapi/models.php';
*/

$requestBody = file_get_contents("php://input");
__dbg(get_defined_vars());
echo 'OK';

/*
//$inboundMessages = Conversions::createFromJSON('InboundSmsMessages', $requestBody);

// Usually you will process this inbound message here, save it and (eventually) respond with another message.

// For example, just save it to a file:
$fileName = '/tmp/message-'.mktime(true);
$f = fopen($fileName, 'w');
fwrite($f, "\n-------------------------------------\n");
//fwrite($f, '' . $inboundMessages);
fwrite($f, "\n-------------------------------------\n");
fwrite($f, $requestBody);
fwrite($f, "\n-------------------------------------\n");
fclose($f);

echo 'OK', $fileName;
*/
