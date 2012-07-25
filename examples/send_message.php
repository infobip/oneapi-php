<?php


/**
 * Send message and check for delivery status untill it is delivered.
 *
 * Use ../examples.php to test this file
 */

require_once 'oneapi/client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);

$smsMessage = new SMSRequest();
$smsMessage->senderAddress = SENDER_ADDRESS;
$smsMessage->address = DESTINATION_ADDRESS;
$smsMessage->message = 'Test message';

$smsMessageSendResult = $smsClient->sendSMS($smsMessage);

echo 'Success:', $smsMessageSendResult->isSuccess(), "\n";
echo 'Response:', $smsMessageSendResult, "\n";
if( ! $smsMessageSendResult->isSuccess()) {
    return;
}

$deliveryStatus = null;

for($i = 0; $i < 4; $i++) {
    $smsMessageStatus = $smsClient->queryDeliveryStatus($smsMessageSendResult);

    $deliveryStatus = $smsMessageStatus->deliveryInfo[0]->deliveryStatus;

    echo 'Success:', $smsMessageStatus->isSuccess(), "\n";
    echo 'Status:', $deliveryStatus, "\n";
    if( ! $smsMessageStatus->isSuccess()) {
        echo 'Message id:', $smsMessageStatus->exception->messageId, "\n";
        echo 'Text:', $smsMessageStatus->exception->text, "\n";
        echo 'Variables:', $smsMessageStatus->exception->variables, "\n";
    }

    sleep(3);
}

assert($deliveryStatus == 'DeliveredToTerminal');

//Logs::printLogs();
