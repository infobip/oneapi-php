<?php


/**
 * Send message and check for delivery status untill it is delivered.
 *
 * Use ../examples.php to test this file
 */

require_once 'oneapi/client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

# example:prepare-message-with-notify-url
$smsMessage = new SMSRequest();
$smsMessage->senderAddress = SENDER_ADDRESS;
$smsMessage->address = DESTINATION_ADDRESS;
$smsMessage->message = 'Test message';
$smsMessage->notifyURL = NOTIFY_URL;
# ----------------------------------------------------------------------------------------------------

$smsMessageSendResult = $smsClient->sendSMS($smsMessage);
$clientCorrelator = $smsMessageSendResult->clientCorrelator;

