<?php

namespace infobip\models;

// Example:
// {"deliveryReceiptSubscription":{"callbackReference":{"callbackData":null,"notifyURL":"http://192.168.10.111/save_requests"},"resourceURL":"http://oneapi.infobip.com/1/smsmessaging/outbound/subscriptions/q1id6ksfc8"}}

use infobip\Models;
use infobip\SubFieldConversionRule;

class DeliveryReportSubscription extends AbstractObject {

    public $subscriptionId;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'infobip\models\DeliveryReportSubscription',
        new SubFieldConversionRule('subscriptionId', 'deliveryReceiptSubscription.resourceURL')
);
