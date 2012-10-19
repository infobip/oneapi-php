<?php

#require_once 'yapd/dbg.php';

require_once 'app.php';

if(!getFormParam('from') || !getFormParam('to') || !getFormParam('message')) {
    redirectWithFormError('send-message-form.php', 'From, to and message are mandatory');
}

// Construct the sms message object:
$message = new SMSRequest();
$message->senderAddress = getFormParam('from');
$message->address = getFormParam('to');
$message->message = getFormParam('message');
$message->notifyURL = getFormParam('notifyURL');

// Initialize the client:
$smsClient = new SmsClient(USERNAME, PASSWORD);

try {
    $result = $smsClient->sendSMS($message);
    redirectWithFormSuccess('send-message-form.php', '<h1>Message sent</h1><a href="check-delivery-status-form.php?clientCorrelator=' . $result->clientCorrelator . '">check delivery status</a>');
    return;
} catch(Exception $e) {
    redirectWithFormError('send-message-form.php', 'Error sending message:' . $e->getMessage());
    return;
}
