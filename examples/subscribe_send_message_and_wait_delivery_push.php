<?php

require_once 'oneapi/client.php';
require_once 'oneapi/models.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

// Keyword to be used for MO messages:
$criteria = 'test' . rand(10000000, 100000000);

$request = new SubscribeToDeliveryNotificationsRequest();
$request->senderAddress = SENDER_ADDRESS;
$request->criteria = $criteria;
$request->notifyURL = NOTIFY_URL;
$request->criteria = 'test' . rand(10000000, 100000000);

$subscriptionResult = $smsClient->subscribeToDeliveryStatusNotifications($request);

if(!$subscriptionResult->isSuccess()) {
    echo 'Error subscribing';
    Logs::printLogs();
    die();
}

echo 'Subscribed to delivery reports sent to ', $request->senderAddress, ' reports for messages starting with ', $criteria, ' will be pushed to ', $request->notifyURL, "\n";

// List all currently active subscriptions:
$subscriptions = $smsClient->retrieveDeliveryNotificationsSubscriptions();

echo 'Found ', sizeof($subscriptions->deliveryReceiptSubscriptions), ' subscriptions', "\n";

foreach($subscriptions->deliveryReceiptSubscriptions as $subscription) {
    // When needed, you can unsubscribe:
    $cancelResponse = $smsClient->cancelInboundMessagesSubscription($subscription->subscriptionId);

    if(!$cancelResponse->isSuccess()) {
        echo 'Error unsubscribing';
        Logs::printLogs();
        die();
    }

    echo 'Cancelled subscription to ', $subscription->subscriptionId, "\n";
}

//Logs::printLogs();
