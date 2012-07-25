<?php
class Timezones extends AbstractObject {

    public $timezones;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'Timezones',
        new ObjectConversionRule(function($object, $jsonData) {
                $arrTimezones = Utils::getArrayValue($jsonData, 'timeZones', array());
                foreach($arrTimezones as $arrTzone) {
                    $object->timezones[] = new Timezone($arrTzone, true);
                }
            }
        )
);


?>
