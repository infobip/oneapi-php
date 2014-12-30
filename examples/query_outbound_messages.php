<?php

use infobip\SmsClient;
use infobip\utils\OneApiDateTime;

require_once __DIR__ . '\..\oneapi\client.php';

date_default_timezone_set("UTC");

$fromTime = OneApiDateTime::createFromFormat('Y-m-dTH:i:s....O', '1970-01-01T00:00:00.000+0000');
$toTime = new DateTime();

$smsClient = new SmsClient(USERNAME, PASSWORD);
$messages = $smsClient->retrieveOutboundMessages($fromTime, $toTime);

foreach ($messages->logs as $message) {
    echo $message->sendDateTime;
    echo $message->messageId;
    echo $message->smsCount;
    echo $message->mcc;
    echo $message->mnc;
    echo $message->countryCode;
    echo $message->destinationAddress;
    echo $message->sender;
    echo $message->pricePerMessage;
    echo $message->clientMetadata;
    echo $message->messageText;
    echo "\n";
}

if ($messages->isMoreAvailable()) {
    $oldestMessage = $messages->logs[sizeof($messages->logs) - 1];
    $toTime = OneApiDateTime::createFromFormat('Y-m-dTH:i:s....O', $oldestMessage->sendDateTime);

    //fetching the next "page"
    $messages = $smsClient->retrieveOutboundMessages($fromTime, $toTime);
    foreach ($messages->logs as $message) {
        echo $message->sendDateTime;
        echo $message->messageId;
        echo $message->smsCount;
        echo $message->mcc;
        echo $message->mnc;
        echo $message->countryCode;
        echo $message->destinationAddress;
        echo $message->sender;
        echo $message->pricePerMessage;
        echo $message->clientMetadata;
        echo $message->messageText;
        echo "\n";
    }
}
