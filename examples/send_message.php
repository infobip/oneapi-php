<?php


/**
 * Send message and check for delivery status until it is delivered.
 *
 * Use ../examples.php to test this file
 */

use infobip\models\SMSRequest;
use infobip\SmsClient;
use infobip\utils\Logs;

require_once __DIR__ . '\..\oneapi\client.php';

# example:initialize-sms-client
$smsClient = new SmsClient(USERNAME, PASSWORD);
# ----------------------------------------------------------------------------------------------------

# example:login-sms-client
$smsClient->login();
# ----------------------------------------------------------------------------------------------------

# example:prepare-message-without-notify-url
$smsMessage = new SMSRequest();
$smsMessage->senderAddress = SENDER_ADDRESS;
$smsMessage->address = DESTINATION_ADDRESS;
$smsMessage->message = 'Hello world';
# ----------------------------------------------------------------------------------------------------

# example:send-message
$smsMessageSendResult = $smsClient->sendSMS($smsMessage);
# ----------------------------------------------------------------------------------------------------
//
# example:send-message-client-correlator
// The client correlator is a unique identifier of this api call:
$clientCorrelator = $smsMessageSendResult->clientCorrelator;
# ----------------------------------------------------------------------------------------------------

$deliveryStatus = null;

for($i = 0; $i < 4; $i++) {
    # example:query-for-delivery-status
    // You can use $clientCorrelator or $smsMessageSendResult as an method call argument here:
    $smsMessageStatus = $smsClient->queryDeliveryStatus($smsMessageSendResult);
    $deliveryStatus = $smsMessageStatus->deliveryInfo[0]->deliveryStatus;

    echo 'Success:', $smsMessageStatus->isSuccess(), "\n";
    echo 'Status:', $deliveryStatus, "\n";
    if( ! $smsMessageStatus->isSuccess()) {
        echo 'Message id:', $smsMessageStatus->exception->messageId, "\n";
        echo 'Text:', $smsMessageStatus->exception->text, "\n";
        echo 'Variables:', $smsMessageStatus->exception->variables, "\n";
    }
    # ----------------------------------------------------------------------------------------------------
    sleep(3);
}

Logs::printLogs();
