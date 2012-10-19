<?php

#require_once 'yapd/dbg.php';

require_once 'app.php';

$clientCorrelator = getFormParam('clientCorrelator');

if(!$clientCorrelator) {
    redirectWithFormError('check-delivery-status-form.php', 'Client correlator is mandatory');
}

// Initialize the client:
$smsClient = new SmsClient(USERNAME, PASSWORD);

try {
    $result = $smsClient->queryDeliveryStatus($clientCorrelator);
    redirectWithFormSuccess('check-delivery-status-form.php', '<h1>Delivery status is ' . $result->deliveryInfo[0]->deliveryStatus . '</h1>');
    return;
} catch(Exception $e) {
    redirectWithFormError('check-delivery-status-form.php', 'Error sending message:' . $e->getMessage());
    return;
}
