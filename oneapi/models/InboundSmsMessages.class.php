<?php

require_once 'oneapi/Utils.class.php';

class InboundSmsMessages extends AbstractObject {

    public $inboundSMSMessage;
    public $numberOfMessagesInThisBatch;
    //public $resourceURL;
    public $totalNumberOfPendingMessages;
    public $callbackData;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'InboundSmsMessages',
        new ObjectConversionRule(function($object, $jsonData) {
            $messages = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.inboundSMSMessage', array());
            $object->inboundSMSMessage = array();
            foreach($messages as $message) {
                $object->inboundSMSMessage[] = Conversions::createFromJSON('InboundSmsMessage', $message, false);
            }
            $object->numberOfMessagesInThisBatch = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.numberOfMessagesInThisBatch', 0);
            $object->totalNumberOfPendingMessages = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.totalNumberOfPendingMessages', 0);
            $object->callbackData = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.callbackData');
        })
);

?>
