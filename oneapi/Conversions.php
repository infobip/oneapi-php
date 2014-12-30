<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:18
 */

namespace infobip;

use Exception;

class Conversions {

    /** Create new model instance from JSON. */
    public static function createFromJSON($className, $json, $isError=false) {
        if(!$className) {
            throw new Exception('Invalid className:'.$className);
        }
        $model = new $className();
        self::fillFromJSON($model, $json, $isError);
        return $model;
    }

    /** Fill existing model instance from JSON. */
    public static function fillFromJSON($model, $json, $isError=false) {
        if(is_array($json)) {
            $array = $json;
        } else {
            if (get_magic_quotes_gpc()) {
                $json = stripslashes($json);
            }
            $array = json_decode($json, true);
        }

        if(!is_array($array))
            $array = array();

        if($isError) {
            $exception = self::createFromJSON('infobip\models\SmsException', $json, false);
            $model->exception = $exception;
            return $model;
        }

        $conversionRules = Models::getConversionRules(get_class($model));

        $className = get_class($model);

        // Keep original JSON values (for simple string values):
        foreach($array as $key => $value) {
            if(property_exists($className, $key) && (is_string($value) || is_numeric($value) || is_bool($value))) {
                $model->$key = $value;
            }
        }

        // Convert the ones that have custom conversion rules:
        foreach($conversionRules as $conversionRule) {
            foreach($array as $key => $value) {
                if($conversionRule instanceof FieldConversionRule) {
                    // field conversion rule:
                    if(property_exists($className, $key) && $conversionRule->field == $key) {
                        $model->$key = $conversionRule->convertFromJSON($value);
                    }
                }
            }
        }
        foreach($conversionRules as $conversionRule) {
            if($conversionRule instanceof ObjectConversionRule) {
                $conversionRule->convertFromJson($model, $array);
            }
        }

        return $model;
    }

    /** Convert model to JSON. */
    public static function convertToJSON($model, $toString=false) {
        $conversionRules = Models::getConversionRules(get_class($model));
        $result = array();

        // Keep original simple values:
        $objectVars = get_object_vars($model);
        foreach($objectVars as $key => $value) {
            if(is_string($value) || is_numeric($value))
                $result[$key] = $value;
        }

        foreach($conversionRules as $conversionRule) {
            if($conversionRule instanceof FieldConversionRule) {
                $fieldName = $conversionRule->field; // TODO what if field names in JSON and in model are not the same?
                $result[$fieldName] = $conversionRule->toJSON($model->$fieldName);
            } else if($conversionRule instanceof FieldConversionRule) {
                // TODO
            }
        }

        if($toString)
            return json_encode($result);

        return $result;
    }

}