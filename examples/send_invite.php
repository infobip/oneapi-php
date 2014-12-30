<?php


/**
 * Send message and check for delivery status until it is delivered.
 *
 * Use ../examples.php to test this file
 */
use infobip\models\SocialInviteRequest;
use infobip\SmsClient;
use infobip\SocialInviteClient;

require_once __DIR__ . '\..\oneapi\client.php';

# example:initialize-sms-client
$socinv = new SocialInviteClient(USERNAME, PASSWORD);
$smsClient = new SMSClient(USERNAME, PASSWORD);
# ----------------------------------------------------------------------------------------------------

# example:login-sms-client
$smsClient->login();
# ----------------------------------------------------------------------------------------------------

$siReq = new SocialInviteRequest();
$siReq->senderAddress = SENDER_ADDRESS;
$siReq->recipients = DESTINATION_ADDRESS;
$siReq->messageKey = SOCIAL_INVITES_MESSAGE_KEY;
# ----------------------------------------------------------------------------------------------------

# example:send-invite
$siResult = $socinv->sendInvite($siReq, API_KEY);
# ----------------------------------------------------------------------------------------------------

// The bulk id is a unique identifier of this api call:
$bulkId = $siResult->sendSmsResponse->bulkId;
# ----------------------------------------------------------------------------------------------------

$deliveryStatus = null;

if ($siResult != null) {
  for ($i = 0; $i < 4; $i++) {
      # example:query-for-delivery-status
      // You can use $bulkId as an method call argument here:
      $smsMessageStatus = $smsClient->queryDeliveryStatus($bulkId);
      echo "Getting status (try #", $i, "): \n<br />\n";

      for ($j = 0; $j < count($smsMessageStatus->deliveryInfo); $j++) {
        $deliveryStatus = $smsMessageStatus->deliveryInfo[$j]->deliveryStatus;

        echo 'Message[' . $j . '] => {', "\n";
        echo 'Success:', $smsMessageStatus->isSuccess() ? 'true' : 'false', "\n";
        echo 'Status:', $deliveryStatus, "\n";
        if (! $smsMessageStatus->isSuccess()) {
            echo 'Message id:', $smsMessageStatus->exception->messageId, "\n";
            echo 'Text:', $smsMessageStatus->exception->text, "\n";
            echo 'Variables:', $smsMessageStatus->exception->variables, "\n";
        }
        echo "\n} \n<br />\n";
      }

      echo "<br />";
      # ----------------------------------------------------------------------------------------------------
      sleep(3);
  }
}

//Logs::printLogs();
