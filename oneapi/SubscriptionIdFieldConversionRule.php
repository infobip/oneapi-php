<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:24
 */

namespace infobip;
use infobip\utils\Utils;

class SubscriptionIdFieldConversionRule extends ObjectConversionRule {

    private $objectFieldName;
    private $jsonFieldName;

    public function __construct($objectFieldName, $jsonFieldName) {
        $this->objectFieldName = $objectFieldName;
        $this->jsonFieldName = $jsonFieldName;
    }

    public function convertFromJson($object, $json) {
        $value = Utils::getArrayValue($json, $this->jsonFieldName);

        // Value is an url, the last part is the subscription id:
        $parts = explode('/', $value);
        if($value && sizeof($parts) > 0)
            $value = $parts[sizeof($parts) - 2];

        $fieldName = $this->objectFieldName;
        $object->$fieldName = $value;
    }

    public function convertToJson($object, $json) {
        // TODO(TK)
    }

}