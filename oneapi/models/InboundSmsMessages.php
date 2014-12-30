<?php

namespace infobip\models;

use infobip\Conversions;
use infobip\Models;
use infobip\ObjectConversionRule;
use infobip\utils\Utils;

class InboundSmsMessages extends AbstractObject {

    # FIXME: rename to messages
    public $inboundSMSMessage;
    public $numberOfMessagesInThisBatch;
    //public $resourceURL;
    public $totalNumberOfPendingMessages;
    public $callbackData;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

function __convert_inbound_sms_messages($object, $jsonData) {
    $messages = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.inboundSMSMessage', array());
    $object->inboundSMSMessage = array();
    foreach($messages as $message) {
        $object->inboundSMSMessage[] = Conversions::createFromJSON('infobip\models\InboundSmsMessage', $message, false);
    }
    $object->numberOfMessagesInThisBatch = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.numberOfMessagesInThisBatch', 0);
    $object->totalNumberOfPendingMessages = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.totalNumberOfPendingMessages', 0);
    $object->callbackData = Utils::getArrayValue($jsonData, 'inboundSMSMessageList.callbackData');
}

Models::register(
        'infobip\models\InboundSmsMessages',
        new ObjectConversionRule('infobip\models\__convert_inbound_sms_messages')
);

?>
