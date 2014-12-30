<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:22
 */

namespace infobip;

class ObjectConversionRule {

    public $fromJSON;
    public $toJSON;

    public function __construct($fromJSON, $toJSON=null) {
        $this->fromJSON = $fromJSON;
        $this->toJSON = $toJSON;
    }

    public function convertFromJson($object, $json) {
        $function = $this->fromJSON;
        $function($object, $json);
    }

    public function convertToJson($object, $json)
    {
        $function = $this->toJSON;
        if (is_callable($function)) {
            $function($object, $json);
        } else {
            // TODO throw an exception
        }
    }



}