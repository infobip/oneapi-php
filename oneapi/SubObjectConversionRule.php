<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:23
 */

namespace infobip;
use infobip\utils\Utils;

class SubObjectConversionRule extends ObjectConversionRule {

    private $className;
    private $objectFieldName;
    private $jsonFieldName;

    public function __construct($className, $objectFieldName, $jsonFieldName=null) {
        assert(@strlen($className) > 0);
        assert(@strlen($objectFieldName) > 0);

        $this->className = $className;
        $this->objectFieldName = $objectFieldName;
        $this->jsonFieldName = $jsonFieldName;

        if(!$this->jsonFieldName)
            $this->jsonFieldName = $objectFieldName;

    }

    public function convertFromJson($object, $json) {
        $value = Utils::getArrayValue($json, $this->jsonFieldName);

        $result = Conversions::createFromJSON($this->className, $value, false);

        $fieldName = $this->objectFieldName;
        $object->$fieldName = $result;
    }

    public function convertToJson($object, $json) {
        // TODO(TK)
    }

}