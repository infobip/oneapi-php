<?

require_once 'oneapi/client.php';
require_once 'oneapi/models.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);

// Keyword to be used for MO messages:
$criteria = 'test' . rand(10000000, 100000000);

// Create new subscriptions:
$moSubscription = new MoSubscription();
// Do not set notifyURL property for this one!
// $moSubscription->notifyURL = ...;
$moSubscription->callbackData = 'any string';
$moSubscription->criteria = $criteria;
$moSubscription->destinationAddress = MO_NUMBER;

// This step usually will be needed only once per application:
$createSubscriptionsResult = $smsClient->subscribeToInboundMessagesNotifications($moSubscription);

// Now that the subscription is saved, the application will store messages starting with $criteria and sent to $moNumber for you

// Let's try to send a message ourselves (usually this will be done by the end user from a real mobile phone):
$smsMessage = new SMSRequest();
$smsMessage->senderAddress = '38598123456';
$smsMessage->address = $moSubscription->destinationAddress;
$smsMessage->message = $criteria . ' Some message';

$smsMessageSendResult = $smsClient->sendSMS($smsMessage);

// Sleep a few seconds just to be sure the message is processed:
echo 'Waiting for the message to be processed...';
sleep(20);

// Note: the time lag depends on a lot of things. Usually a few seconds will be OK, but it depends on the
// client roaming status, Newtorks, etc.

// OK, let's see if there are any SMS message waiting for us (of course there is one!):
$receivedInboundMessages = $smsClient->retrieveInboundMessages(100);

echo 'Found ', sizeof($receivedInboundMessages->inboundSMSMessage), ' messages', "\n";

foreach($receivedInboundMessages->inboundSMSMessage as $inboundMessage) {
    echo $inboundMessage . "\n";
    echo 'destinationAddress:', $inboundMessage->destinationAddress, "\n";
    echo 'message:', $inboundMessage->message, "\n";
    echo 'senderAddress:', $inboundMessage->senderAddress, "\n";
}

// Let's try once again. If nobody else sent any messages to our number, this one should be empty:
$receivedInboundMessages = $smsClient->retrieveInboundMessages(100);

echo 'Found ', sizeof($receivedInboundMessages->inboundSMSMessage), ' messages', "\n";

//Logs::printLogs();
