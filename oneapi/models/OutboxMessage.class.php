<?php

class OutboxMessage extends AbstractObject {

    public $sendDateTime;
    public $messageId;
    public $smsCount;
    public $destinationAddress;
    public $sender;
    public $clientMetadata;
    public $messageText;
    public $status;
    public $bulkId;
    public $deliveryReportTime;
    public $ported;
    public $pricePerMessage;
    public $destinationNetwork;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }
}

Models::register(
    'OutboxMessage',
    array(
    new SubObjectConversionRule('Status', 'status'), 
    new SubObjectConversionRule('Network', 'destinationNetwork'),
    new SubObjectConversionRule('Price', 'pricePerMessage')
    )
);



?>
