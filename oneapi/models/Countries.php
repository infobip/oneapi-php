<?php

namespace infobip\models;

use infobip\Models;
use infobip\ObjectArrayConversionRule;

class Countries extends AbstractObject {

    public $countries;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'infobip\models\Countries',
        new ObjectArrayConversionRule('infobip\models\Country', 'countries')
);

?>
