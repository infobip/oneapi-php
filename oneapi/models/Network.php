<?php

namespace infobip\models;

use infobip\Models;
use infobip\SubObjectConversionRule;

class Network extends AbstractObject {
    public $id;
    public $name;
    public $country;
    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }
}
Models::register(
    'infobip\models\Network',
    new SubObjectConversionRule('infobip\models\Country', 'country')
);
?>