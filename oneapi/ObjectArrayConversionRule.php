<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:26
 */

namespace infobip;

use infobip\utils\Logs;
use infobip\utils\Utils;

class ObjectArrayConversionRule extends ObjectConversionRule {

    private $className;
    private $objectFieldName;
    private $jsonFieldName;

    public function __construct($className, $objectFieldName, $jsonFieldName=null) {
        assert($className != null);
        assert($objectFieldName != null);

        $this->className = $className;
        $this->objectFieldName = $objectFieldName;
        $this->jsonFieldName = $jsonFieldName;

        if(!$this->jsonFieldName)
            $this->jsonFieldName = $objectFieldName;

    }

    public function convertFromJson($object, $json) {
        $values = Utils::getArrayValue($json, $this->jsonFieldName);
        if(!is_array($values)) {
            Logs::warn('Looking for array (', $this->jsonFieldName, '), but found:', $values);
            return null;
        }

        $result = array();

        foreach($values as $value) {
            $result[] = Conversions::createFromJSON($this->className, $value, false);
        }

        $fieldName = $this->objectFieldName;
        $object->$fieldName = $result;
    }

    public function convertToJson($object, $json) {
        // TODO(TK)
    }

}