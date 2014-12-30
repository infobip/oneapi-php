<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:21 PM
 */

namespace infobip;

/**
 * Utility handler class to store username/password.
 */
class OneApiConfigurator {

    private static $username;
    private static $password;
    private static $charset;

    public static function setCredentials($username, $password) {
        self::$username = $username;
        self::$password = $password;
    }

    public static function getUsername() {
        return self::$username;
    }

    public static function getPassword() {
        return self::$password;
    }

    /**
     * May be used in case the locale charset of this script is not utf-8.
     */
    public static function setCharset($charset) {
        self::$charset = $charset;
    }

    public static function getCharset() {
        return self::$charset;
    }

}