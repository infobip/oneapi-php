<?php
/**
 * Created by PhpStorm.
 * User: amarjanovic
 * Date: 30.12.2014
 * Time: 12:27
 */

namespace infobip;


use Exception;
use infobip\utils\Logs;

class Models {

    private static $conversions = array();

    public static function register($className, $conversionRules=null) {
        if($conversionRules == null)
            $conversionRules = array();

        if($conversionRules !== null && !is_array($conversionRules))
            $conversionRules = array($conversionRules);

        self::$conversions[strtolower($className)] = $conversionRules;
    }

    public static function getConversionRules($className) {
        $className = strtolower($className);
        if(!array_key_exists($className, self::$conversions)) {
            Logs::debug('Registered models:', array_keys(self::$conversions));
            throw new Exception('Unregistered model:'. $className);
        }
        return self::$conversions[$className];
    }

}