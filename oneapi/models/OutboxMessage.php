<?php

namespace infobip\models;

use infobip\Models;
use infobip\SubObjectConversionRule;

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
    'infobip\models\OutboxMessage',
    array(
        new SubObjectConversionRule('infobip\models\Status', 'status'), 
        new SubObjectConversionRule('infobip\models\Network', 'destinationNetwork'),
        new SubObjectConversionRule('infobip\models\Price', 'pricePerMessage')
    )
);

?>
