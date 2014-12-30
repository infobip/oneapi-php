<?php

namespace infobip\models;

use infobip\Models;

class DeliveryInfo extends AbstractObject {

    public $deliveryStatus;
    public $address;
    public $messageId;
    public $clientCorrelator;

    public function __construct() {
        parent::__construct();
    }

}

Models::register('infobip\models\DeliveryInfo');
