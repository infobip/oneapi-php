<?php

class Network extends AbstractObject {

    public $id;
    public $name;
    public $country;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }
}

Models::register(
    'Network',
    new SubObjectConversionRule('Country', 'country')
);
//Models::register('Network');

?>