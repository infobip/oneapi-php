<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:25 PM
 */

namespace infobip;

/**
 * Warning, temporary implementation, the API may change!
 */
class UssdClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    public function sendMessage($address, $message) {
        $params = array(
            'address' => $address,
            'message' => $message,
        );

        list($isSuccess, $content) = $this->executePOST(
            $this->getRestUrl('/1/ussd/outbound'),
            $params,
            'application/json'
        );

        return $this->createFromJSON('infobip\models\InboundSmsMessage', $content, !$isSuccess);
    }

    public function stopSession($address, $message) {
        $params = array(
            'address' => $address,
            'message' => $message,
            'stopSession' => 'true',
        );

        list($isSuccess, $content) = $this->executePOST(
            $this->getRestUrl('/1/ussd/outbound'),
            $params,
            'application/json'
        );

        return $isSuccess;
    }

}
