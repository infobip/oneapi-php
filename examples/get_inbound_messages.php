<?php


require_once 'oneapi/client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

# example:retrieve-inbound-messages
$inboundMessages = $smsClient->retrieveInboundMessages();

foreach($inboundMessages->inboundSMSMessage as $message) {
    echo $message;
}
# ----------------------------------------------------------------------------------------------------
