<?php

#require_once 'yapd/dbg.php';

require_once 'app.php';

$address = getFormParam('address');
$notifyURL = getFormParam('notifyURL');

if(!$address) {
    redirectWithFormError('send-roaming-status-form.php', 'Address field is mandatory');
}

// Initialize the client:
$dataConnectionProfileClient = new DataConnectionProfileClient(USERNAME, PASSWORD);

try {
    if($notifyURL) {
        $result = $dataConnectionProfileClient->retrieveRoamingStatus($address, $notifyURL);

        $message = '<h1>Request sent</h1>';
        $message .= '<p>The result will be pushed back to ' . $notifyURL . '</p>';

        redirectWithFormSuccess('send-roaming-status-form.php', $message);
    } else {
        $result = $dataConnectionProfileClient->retrieveRoamingStatus($address);

        $message = '<h1>Request sent</h1>';
        $message .= '<p>Mobile network code: ' . $result->servingMccMnc->mnc . '</p>';
        $message .= '<p>Mobile country code: ' . $result->servingMccMnc->mcc . '</p>';
        $message .= '<p>Roaming status: ' . $result->currentRoaming . '</p>';

        redirectWithFormSuccess('send-roaming-status-form.php', $message);
    }
} catch(Exception $e) {
    redirectWithFormError('send-roaming-status-form.php', 'Error checking roaming status:' . $e->getMessage());
    return;
}
