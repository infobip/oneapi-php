<?php

class DeliveryReceiptSubscriptions extends AbstractObject {

    public $subscriptionId;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'DeliveryReceiptSubscriptions',
        new ObjectArrayConversionRule('DeliveryReportSubscription', 'deliveryReceiptSubscriptions')
);
