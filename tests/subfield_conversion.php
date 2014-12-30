<?php

use infobip\Conversions;
use infobip\Models;
use infobip\models\AbstractObject;
use infobip\SubFieldConversionRule;

require_once '../oneapi/client.php';

$json = '{"deliveryReceiptSubscription":{"callbackReference":{"callbackData":null,"notifyURL":"http://192.168.10.111/save_requests"},"resourceURL":"http://oneapi.infobip.com/1/smsmessaging/outbound/subscriptions/q1id6ksfc8"}}';

class TestClass extends AbstractObject {

    public $subscriptionId;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'TestClass',
        new SubFieldConversionRule('subscriptionId', 'deliveryReceiptSubscription.resourceURL')
);

$object = Conversions::createFromJSON('TestClass', $json, false);

assert($object);
assert($object->subscriptionId == 'http://oneapi.infobip.com/1/smsmessaging/outbound/subscriptions/q1id6ksfc8');
