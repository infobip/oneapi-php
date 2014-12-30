<?php

namespace infobip\models;

use infobip\Models;
use infobip\ObjectArrayConversionRule;

class DeliveryReportSubscriptions extends AbstractObject {

    public $subscriptionId;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'infobip\models\DeliveryReportSubscriptions',
        new ObjectArrayConversionRule('infobip\models\DeliveryReportSubscription', 'deliveryReceiptSubscriptions')
);
