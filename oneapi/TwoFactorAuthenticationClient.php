<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:30 PM
 */

namespace infobip;

class TwoFactorAuthenticationClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = "https://oneapi-test.infobip.com/") {
        parent::__construct($username, $password, $baseUrl);
    }

    /**
     * Generate Api Key
     */
    public function generateApiKey() {
        $restUrl = $this->getRestUrl('/2fa/1/api-key');

        list($isSuccess, $content) = $this->executePOST(
            $restUrl, null, 'application/json'
        );

        return $content;
    }

    /**
     * Initiate Two Factor Authentication
     */
    public function authentication($authenticationRequest, $apiKey) {
        $restUrl = $this->getRestUrl('/2fa/1/authentication');

        $params = array(
            'applicationId' => $authenticationRequest->applicationId,
            'messageId' => $authenticationRequest->messageId,
            'senderId' => $authenticationRequest->senderId,
            'phoneNumber' => $authenticationRequest->phoneNumber,
        );

        list($isSuccess, $content) = $this->executePOST(
            $restUrl, $params, 'application/json', $apiKey
        );

        return $this->createFromJSON($isSuccess ? 'infobip\models\two_factor_authentication\TfaResponse' : 'infobip\models\iam\IamException', $content, false);// !$isSuccess);
    }

    /**
     * Verify phone number
     */
    public function verification($verificationRequest, $apiKey) {
        $restUrl = $this->getRestUrl('/2fa/1/verification');

        $params = array(
            'applicationId' => $verificationRequest->applicationId,
            'pin' => $verificationRequest->pin,
            'phoneNumber' => $verificationRequest->phoneNumber,
        );

        list($isSuccess, $content) = $this->executePOST(
            $restUrl, $params, 'application/json', $apiKey
        );

        return $this->createFromJSON($isSuccess ? 'infobip\models\two_factor_authentication\TfaVerifyPinResponse' : 'infobip\models\iam\IamException', $content, false);// !$isSuccess);
    }

    /**
     * Check if phone number is verified
     */
    public function isVerified($isVerifiedRequest, $apiKey) {
        $restUrl = $this->getRestUrl(
            '/2fa/1/applications/{appId}/phone-number/{phoneNumber}', Array(
                'appId' => $isVerifiedRequest->applicationId,
                'phoneNumber' => $isVerifiedRequest->phoneNumber
            )
        );

        list($isSuccess, $content) = $this->executeGET(
            $restUrl, null, 'application/json', $apiKey
        );

        return $this->createFromJSON($isSuccess ? 'infobip\models\two_factor_authentication\TfaIsVerifiedResponse' : 'infobip\models\iam\IamException', $content, false);// !$isSuccess);
    }

    /**
     * Check delivery status of sms
     */
    public function deliveryStatus($smsId, $apiKey) {
        $restUrl = $this->getRestUrl(
            '/2fa/1/sms/{smsId}', Array(
                'smsId' => $smsId
            )
        );

        list($isSuccess, $content) = $this->executeGET(
            $restUrl, null, 'application/json', $apiKey
        );

        return $this->createFromJSON($isSuccess ? 'infobip\models\two_factor_authentication\TfaDeliveryInfo' : 'infobip\models\iam\IamException', $content, false);// !$isSuccess);
    }

}
