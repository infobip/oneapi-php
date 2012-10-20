<?php

require_once 'app.php';

if(array_key_exists('gsm', $_REQUEST)) {
	$gsm = $_REQUEST['gsm'];
	$ussdClient = new UssdClient(USERNAME, PASSWORD);

    $response = null;
    while($response != '1') {
        $result = $ussdClient->sendMessage($gsm, "You favourite mobile API is\n1. Parseco\n2. Other");
        $response = $result->message;
    }
    $ussdClient->stopSession($gsm, "Cool");

   echo "<h1>Session finished</h1>";
}

?>
<html>
    <head>
        <title>USSD</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Start USSD session</h1>
        <form method="GET">
            GSM: <input type="text" name="gsm"/> <input type="submit" value="Start USSD" />
        </form>
    </body>
</html>
