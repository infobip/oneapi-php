<?php

// Example:
// {"deliveryReceiptSubscription":{"callbackReference":{"callbackData":null,"notifyURL":"http://192.168.10.111/save_requests"},"resourceURL":"http://api.parseco.com/1/smsmessaging/outbound/subscriptions/q1id6ksfc8"}}

class DeliveryReceiptSubscription extends AbstractObject {

    public $subscriptionId;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'DeliveryReceiptSubscription',
        new ObjectConversionRule(function($object, $json) {
            $url = Utils::getArrayValue($json, 'deliveryReceiptSubscription.resourceURL', null);
            $parts = explode('/', $url);
            if($url && sizeof($parts) > 0) {
                $object->subscriptionId = $parts[sizeof($parts) - 1];
            }
        })
);
