<?php


/**
 * Send message and check for delivery status untill it is delivered.
 *
 * Use ../examples.php to test this file
 */

use infobip\models\SMSRequest;
use infobip\SmsClient;

require_once __DIR__ . '\..\oneapi\client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

# example:prepare-message-with-notify-url
$smsMessage = new SMSRequest();
$smsMessage->senderAddress = SENDER_ADDRESS;
$smsMessage->address = DESTINATION_ADDRESS;
$smsMessage->message = 'Hello world';
$smsMessage->notifyURL = NOTIFY_URL;
# ----------------------------------------------------------------------------------------------------

$smsMessageSendResult = $smsClient->sendSMS($smsMessage);
$clientCorrelator = $smsMessageSendResult->clientCorrelator;

echo NOTIFY_URL;
echo $clientCorrelator;