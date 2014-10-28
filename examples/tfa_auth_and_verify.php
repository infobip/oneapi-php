<?php

/**
 * Send 2-FA message, and if sent, check it's delivery status few times.
 * Upon delivering, try verification, and afterwards check whether phone number is verified.
 *
 * Use ../examples.php to test this file
 */

require_once 'oneapi/client.php';

# example:initialize-tfa-client
$tfaClient = new TwoFactorAuthenticationClient(); // No need for USERNAME or PASSWORD.
# ----------------------------------------------------------------------------------------------------

# example:prepare-tfa-authentication-request
$tfaRequest = new TfaRequest();
$tfaRequest->applicationId = "B7DC8A5FCACAE95D754219400EEA38F2";
$tfaRequest->messageId = "9030551F327704A0F262995A1A4B4596";
$tfaRequest->phoneNumber = DESTINATION_ADDRESS;
$tfaRequest->senderId = SENDER_ADDRESS;
# ----------------------------------------------------------------------------------------------------

# example:send-authentication-request
try {
  $tfaResponse = $tfaClient->authentication($tfaRequest, API_KEY);

  if (property_exists($tfaResponse, 'requestError') && $tfaResponse->requestError != null) {
    echo "There was an error processing request. Response is: \n", json_encode($tfaResponse);
    exit (1);
  }
  # ----------------------------------------------------------------------------------------------------

  # example:send-authentication-sms-id
  $smsId = $tfaResponse->smsId;
  # ----------------------------------------------------------------------------------------------------

  if ($tfaResponse->smsStatus == "MESSAGE_SENT") {
    for($i = 0; $i < 5; $i++) {
        # example:query-for-delivery-status
        $deliveryStatus = $tfaClient->deliveryStatus($smsId, API_KEY);
        if ($deliveryStatus->finalStatus || "MESSAGE_DELIVERED" == $deliveryStatus->status) {
          echo 'Status is (FINAL): ', $deliveryStatus->status, "\n\t>>", $deliveryStatus->description, "\n";
          break;
        } else {
          echo 'Status is: ', $deliveryStatus->status, "\n\t>>", $deliveryStatus->description, "\n";
        }
        # ----------------------------------------------------------------------------------------------------
        sleep(3);
    }

    $handle = fopen("php://stdin", "r");

    $verified = false;
    $verificationRequest = new TfaVerifyPinRequest();
    $verificationRequest->applicationId = $tfaRequest->applicationId;
    $verificationRequest->phoneNumber = $tfaRequest->phoneNumber;

    while (!$verified) {
      echo "Please input received PIN:\n";
      $pin = trim(fgets($handle));
      $verificationRequest->pin = $pin;
      $verificationResponse = $tfaClient->verification($verificationRequest, API_KEY);
      $verified = $verificationResponse->verified;

      print_r($verificationResponse);

      if (!$verified) {
        if ($verificationResponse->pinError) {
          echo "PinError: ", $verificationResponse->pinError, "\n";
        }
        echo "Verification attempts remaining: ", $verificationResponse->attemptsRemaining, "\n";
        if ($verificationResponse->attemptsRemaining <= 0) break;
      } else {
        echo "You successfully finished Infobip Two Factor Authentication process for ", $tfaRequest->phoneNumber, "\n";
      }
    }
  } else {
    echo "\n", 'Message was not sent. Your HLR status is: ', $tfaResponse->hlrStatus;
  }
} catch (Exception $e) {
  echo $e->getMessage();
}
