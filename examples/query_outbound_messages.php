<?php

use infobip\SmsClient;
use infobip\utils\OneApiDateTime;

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("UTC");

$fromTime = OneApiDateTime::createFromFormat('Y-m-dTH:i:s....O', '1970-01-01T00:00:00.000+0000');
$toTime = new DateTime();
$messageId = sizeof($argv) >= 4 ? $argv[3] : null;

$smsClient = new SmsClient(USERNAME, PASSWORD);
$messages = $smsClient->retrieveOutboundMessages($fromTime, $toTime, $messageId);

foreach ($messages->logs as $message) {
    echo "Message: \n";
    echo "\tSend Date Time: ". $message->sendDateTime . "\n";
    echo "\tMessage Id: ".$message->messageId . "\n";
    echo "\tSMS Count: ".$message->smsCount . "\n";
    echo "\tDestaination Address: ".$message->destinationAddress . "\n";
    echo "\tSender Address: ".$message->sender . "\n";
    echo "\tClient Metadata: ".$message->clientMetadata . "\n";
    echo "\tMessage Text: ".$message->messageText . "\n";
    echo "\tStatus: \n";
    echo "\t\tId: ". $message->status->id . "\n";
    echo "\t\tStatus: ". $message->status->status . "\n";
    echo "\t\tDescription: ". $message->status->description . "\n";
    echo "\t\tFailure Reason: ". $message->status->failureReason . "\n";
    echo "\tBulkId: ". $message->bulkId . "\n";
    echo "\tDelivery Report Time: ". $message->deliveryReportTime . "\n";
    echo "\tPorted: ". $message->ported . "\n";
    echo "\tPrice:\n";
    echo "\t\tPrice: ". $message->pricePerMessage->price . "\n";
    echo "\t\tCurrency: ". $message->pricePerMessage->currency . "\n";
    echo "\tDestination Network: \n";
    echo "\t\tId: ". $message->destinationNetwork->id . "\n";
    echo "\t\tName: ". $message->destinationNetwork->name . "\n";
    echo "\t\tCountry: \n";
    echo "\t\t\tId: ". $message->destinationNetwork->country->id . "\n";
    echo "\t\t\tCode: ". $message->destinationNetwork->country->code . "\n";
    echo "\t\t\tPrefix: ". $message->destinationNetwork->country->prefix . "\n";
    echo "\t\t\tName: ". $message->destinationNetwork->country->name . "\n";
    echo "\t\t\tLocale: ". $message->destinationNetwork->country->locale . "\n";
    echo "**********************************\n";
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
