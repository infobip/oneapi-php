<?php

require_once 'app.php';


$result = SmsClient::unserializeInboundMessages();


// Process the inbound message here...



// We'll send a response here:
$message = new SMSRequest();
$message->senderAddress = $result->destinationAddress;
$message->address = $result->senderAddress;
$message->message = 'Thank you for your message (' . $result->message . ')!';

// Initialize the client:
$smsClient = new SmsClient(USERNAME, PASSWORD);

$result = $smsClient->sendSMS($message);


// We'll just save this object:
$fileName = PUSH_LOG_DIRECTORY . '/inbound-message-' . strftime('%Y-%m-%d %H:%M') . '.txt';
$data = print_r($result, true);

file_put_contents($fileName, $data);


// Not needed, but just for testing:
echo 'OK';
