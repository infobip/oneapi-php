<?php

require_once 'oneapi/client.php';

$json = '{"deliveryReceiptSubscription":{"callbackReference":{"callbackData":null,"notifyURL":"http://192.168.10.111/save_requests"},"resourceURL":"http://oneapi.infobip.com/1/smsmessaging/outbound/subscriptions/q1id6ksfc8"}}';

class TestClass_32749789 extends AbstractObject {

    public $subscriptionId;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'TestClass_32749789',
        new SubscriptionIdFieldConversionRule('subscriptionId', 'deliveryReceiptSubscription.resourceURL')
);

$object = Conversions::createFromJSON('TestClass_32749789', $json, false);

assert($object);
assert($object->subscriptionId == 'q1id6ksfc8');
