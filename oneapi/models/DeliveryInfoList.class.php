<?php

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
    'DeliveryInfoList',
    new ObjectConversionRule(function($object, $json) {
        $deliveryInfoJsons = Utils::getArrayValue($json, 'deliveryInfoList.deliveryInfo');
        $object->deliveryInfo = array();
        foreach($deliveryInfoJsons as $part) {
            $object->deliveryInfo[] = Conversions::createFromJSON('DeliveryInfo', $part, false);
        }
    })
);

