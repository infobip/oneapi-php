<?php

/**
 * Subscribe and unsubscribe to MO (mobile originated messages) events.
 *
 * Use ../examples.php to test this file
 */

require_once 'oneapi/client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

// Get all current subscriptions:
$moSubscriptions = $smsClient->retrieveInboundMessagesSubscriptions();

echo 'Success:', $moSubscriptions->isSuccess(), "\n";
echo 'Found ', sizeof($moSubscriptions->subscriptions), ' subscriptions', "\n";

if(!$moSubscriptions->isSuccess()) {
    echo 'Error getting the list of subscriptions';
    die();
}

// Remove them one by one:
foreach($moSubscriptions->subscriptions as $subscription) {
    print $subscription;
    $deleteSubscriptionResult = $smsClient->cancelInboundMessagesSubscription($subscription->subscriptionId);
    echo $deleteSubscriptionResult->isSuccess(), "\n";
}

// Create new subscriptions:
$moSubscription = new MoSubscription();
$moSubscription->notifyURL = MO_NOTIFY_URL;
$moSubscription->callbackData = 'any string';
$moSubscription->criteria = 'test' . rand(10000000, 100000000);
$moSubscription->destinationAddress = MO_NUMBER;

$createSubscriptionsResult = $smsClient->subscribeToInboundMessagesNotifications($moSubscription);

if(!$createSubscriptionsResult->isSuccess()) {
    echo 'Error subscribing to ', $moSubscription->destinationAddress;
    Logs::printLogs();
    die();
}

echo 'Subscribed, messages sent to ', $moSubscription->destinationAddress, ' will now be pushed to ', $moSubscription->notifyURL;
