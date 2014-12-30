<?php

namespace infobip\models;
use infobip\Models;
use infobip\ObjectArrayConversionRule;

/**
 * The delivery status of an message.
 */
class DeliveryInfoList extends AbstractObject {

    public $deliveryInfo;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
    'infobip\models\DeliveryInfoList',
    new ObjectArrayConversionRule('infobip\models\DeliveryInfo', 'deliveryInfo', 'deliveryInfoList.deliveryInfo')
);

