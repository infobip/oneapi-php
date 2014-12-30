<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:26
 */

namespace infobip;

use infobip\utils\Utils;

class SubFieldConversionRule extends ObjectConversionRule {

    private $objectFieldName;
    private $jsonFieldName;

    public function __construct($objectFieldName, $jsonFieldName) {
        $this->objectFieldName = $objectFieldName;
        $this->jsonFieldName = $jsonFieldName;
    }

    public function convertFromJson($object, $json) {
        $value = Utils::getArrayValue($json, $this->jsonFieldName);
        $fieldName = $this->objectFieldName;
        $object->$fieldName = $value;
    }

    public function convertToJson($object, $json) {
        // TODO(TK)
    }

}