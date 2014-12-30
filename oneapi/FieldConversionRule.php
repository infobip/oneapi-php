<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:21
 */

namespace infobip;

class FieldConversionRule {

    public $field;
    public $fromJSON;
    public $toJSON;

    public function __construct($field, $fromJSON, $toJSON) {
        $this->field = $field;
        $this->fromJSON = $fromJSON;
        $this->toJSON = $toJSON;
    }

    public function convertFromJSON($value) {
        $function = $this->fromJSON;
        return $function($value);
    }

    public function convertToJSON($value) {
        $function = $this->toJSON;
        return $function($value);
    }

}