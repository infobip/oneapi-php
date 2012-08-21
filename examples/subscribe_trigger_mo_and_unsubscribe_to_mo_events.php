<?php

require_once 'oneapi/client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

// Keyword to be used for MO messages:
$criteria = 'test' . rand(10000000, 100000000);

// Create new subscriptions:
$moSubscription = new MoSubscription();
$moSubscription->notifyURL = MO_NOTIFY_URL;
$moSubscription->callbackData = 'any string';
$moSubscription->criteria = $criteria;
$moSubscription->destinationAddress = MO_NUMBER;

// This step usually will be needed only once per application:
$createSubscriptionsResult = $smsClient->subscribeToInboundMessagesNotifications($moSubscription);

echo 'create subscriptions result:', $createSubscriptionsResult, "\n";

// Now that the subscription is saved, the application will push messages starting with $criteria and sent to $moNumber to $notifyURL

// Let's try to send a message ourselves (usually this will be done by the end user from a real mobile phone):
$smsMessage = new SMSRequest();
$smsMessage->senderAddress = '38598123456';
$smsMessage->address = $moSubscription->destinationAddress;
$smsMessage->message = $criteria . ' Some message';

$smsMessageSendResult = $smsClient->sendSMS($smsMessage);

echo 'The message is created, and you should receive a http push on ' . $moSubscription->notifyURL;
