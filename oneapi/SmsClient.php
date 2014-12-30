<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:24 PM
 */

namespace infobip;


class SmsClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    // ----------------------------------------------------------------------------------------------------
    // Static methods used for http push events from the server:
    // ----------------------------------------------------------------------------------------------------

    public static function unserializeDeliveryStatus($json=null) {
        if($json === null)
            $json = file_get_contents("php://input");

        return Conversions::createFromJSON('infobip\models\DeliveryInfoNotification', $json);
    }

    public static function unserializeInboundMessages($json=null) {
        if($json === null)
            $json = file_get_contents("php://input");

        $json = json_decode($json, true);
        $json = Utils::getArrayValue($json, 'inboundSMSMessage.0');

        return Conversions::createFromJSON('infobip\models\InboundSmsMessage', $json);
    }

    // ----------------------------------------------------------------------------------------------------
    // Rest methods:
    // ----------------------------------------------------------------------------------------------------

    public function sendSMS($message) {
        $restPath = '/1/smsmessaging/outbound/{senderAddress}/requests'; //TODO check version

        $clientCorrelator = $this->getOrCreateClientCorrelator($message->clientCorrelator);

        if(is_string($message->address)) {
            $message->address = explode(',', $message->address);
        }

        $params = array(
            'senderAddress' => $message->senderAddress,
            'address' => $message->address,
            'message' => $message->message,
            'clientCorrelator' => $clientCorrelator,
            'senderName' => 'tel:' . $message->senderAddress
        );

        if ($message->notifyURL)
            $params['notifyURL'] = $message->notifyURL;
        if ($message->callbackData)
            $params['callbackData'] = $message->callbackData;
        if($message->language){
            $params['language'] = $message->language;
        }

        $contentType = 'application/json';

        list($isSuccess, $content) = $this->executePOST(
            $this->getRestUrl($restPath, Array('senderAddress' => $message->senderAddress)), $params, $contentType
        );

        return $this->createFromJSON('infobip\models\ResourceReference', $content, !$isSuccess);
    }

    /**
     * Check for delivery status of a message. If no
     * $clientCorrelatorOrResourceReference is given -- get the list of all pending
     * delivery statuses.
     */
    public function queryDeliveryStatus($clientCorrelatorOrResourceReference = null) {
        $restPath = '/1/smsmessaging/outbound/requests/{clientCorrelator}/deliveryInfos';

        if (is_object($clientCorrelatorOrResourceReference)) {
            $clientCorrelator = $clientCorrelatorOrResourceReference->clientCorrelator;
        } else {
            $clientCorrelator = (string) $clientCorrelatorOrResourceReference;
        }

        $clientCorrelator = $this->getOrCreateClientCorrelator($clientCorrelator);

        $params = array();
        if($clientCorrelator)
            $params['clientCorrelator'] = $clientCorrelator;

        list($isSuccess, $content) = $this->executeGET(
            $this->getRestUrl($restPath, $params)
        );

        return $this->createFromJSON('infobip\models\DeliveryInfoList', $content, !$isSuccess);
    }

    /**
     * Get the list of sent SMS messages.
     */
    public function retrieveOutboundMessages($fromTime=null, $toTime=null){
        $params = array();
        if(! $fromTime){
            $fromTime = OneApiDateTime::createFromFormat('Y-m-dTH:i:s....O', '1970-01-01T00:00:00.000+0000');
        }

        $params['from'] = $fromTime->format('Y-m-d\TH:i:s.000O');
        if($toTime){
            $params['to'] = $toTime->format('Y-m-d\TH:i:s.000O');
        }

        $restUrl = $this->getRestUrl('/1/messaging/outbound/logs/');
        list($isSuccess, $content) = $this->executeGET($restUrl, $params);

        return $this->createFromJSON('infobip\models\OutboxMessages', $content, !$isSuccess);
        //return new OutboxMessages($content, $isSuccess);
    }

    /**
     * Get the list of mobile originated subscriptions for the current user.
     */
    public function retrieveInboundMessagesSubscriptions() {
        $restUrl = $this->getRestUrl('/1/smsmessaging/inbound/subscriptions');
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return new MoSubscriptions($content, $isSuccess);
    }

    /**
     * Create new inbound messages subscription.
     */
    public function subscribeToInboundMessagesNotifications($moSubscription) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/inbound/subscriptions');

        $params = Conversions::convertToJSON($moSubscription);

        list($isSuccess, $content) = $this->executePOST($restUrl, $params);

        // TODO(TK) clientCorrelator !!!

        return new GenericObject($content, $isSuccess);
    }

    /**
     * Delete inbound messages subscription.
     */
    // TODO(TK)
    public function cancelInboundMessagesSubscription($subscriptionId) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions/' . $subscriptionId);
        list($isSuccess, $content) = $this->executeDELETE($restUrl);

        return new GenericObject($content, $isSuccess);
    }

    public function retrieveInboundMessages($maxNumberOfMessages=null){
        $restUrl = $this->getRestUrl('/1/smsmessaging/inbound/registrations/INBOUND/messages');

        if(! $maxNumberOfMessages)
            $maxNumberOfMessages = 100;

        if($maxNumberOfMessages < 0)
            $maxNumberOfMessages = -1 * $maxNumberOfMessages;

        $params = array('maxBatchSize' => $maxNumberOfMessages);

        list($isSuccess, $content) = $this->executeGET($restUrl, $params);

        return $this->createFromJSON('infobip\models\InboundSmsMessages', $content, !$isSuccess);
    }

    /**
     * Start subscribing to delivery status notifications over OneAPI for all your sent SMS
     */
    public function subscribeToDeliveryStatusNotifications($subscribeToDeliveryNotificationsRequest) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/'.$subscribeToDeliveryNotificationsRequest->senderAddress.'/subscriptions');

        $clientCorrelator = $this->getOrCreateClientCorrelator($subscribeToDeliveryNotificationsRequest->clientCorrelator);

        $params = array(
            'notifyURL' => $subscribeToDeliveryNotificationsRequest->notifyURL,
            'criteria' => $subscribeToDeliveryNotificationsRequest->criteria,
            'callbackData' => $subscribeToDeliveryNotificationsRequest->callbackData,
            'clientCorrelator' => $clientCorrelator
        );

        list($isSuccess, $content) = $this->executePOST($restUrl, $params);

        return $this->createFromJSON('infobip\models\DeliveryReportSubscription', $content, !$isSuccess);
    }

    /**
     * Stop subscribing to delivery status notifications for all your sent SMS
     * @param subscriptionId (mandatory) contains the subscriptionId of a previously created SMS delivery report subscription
     */
    public function cancelDeliveryNotificationsSubscription($subscriptionId) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions/' . $subscriptionId);

        list($isSuccess, $content) = $this->executeDELETE($restUrl);

        return $this->createFromJSON('infobip\models\GenericObject', null, !$isSuccess);
    }

    /**
     * Retrieve delivery notifications subscriptions by for the current user
     * @return DeliveryReportSubscription[]
     */
    public function retrieveDeliveryNotificationsSubscriptions() {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions');

        list($isSuccess, $content) = $this->executeGET($restUrl);

        return $this->createFromJSON('infobip\models\DeliveryReportSubscription', $content, !$isSuccess);
    }

}
