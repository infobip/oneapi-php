<?php

/**
 * Description of Encodings
 *
 * @author rbelusic
 */
class Encodings extends AbstractObject {

    public $encodings;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'Encodings',
        new ObjectConversionRule(function($object, $jsonData) {
                $arrEncodings = Utils::getArrayValue($jsonData, 'encodings', array());
                Logs::debug("ENC:" . print_r($jsonData,true));
                foreach($arrEncodings as $arrVal) {
                    $arrEncoding = Array('name' => $arrVal);
                    $object->encodings[] = new Encoding($arrEncoding, true);
                }
            }
        )
);
        


?>
