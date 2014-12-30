<?php

namespace infobip\models;

use infobip\Models;
use infobip\ObjectArrayConversionRule;

class Timezones extends AbstractObject {

    public $timezones;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'infobip\models\Timezones',
        new ObjectArrayConversionRule('infobip\models\Timezone', 'timeZones')
);


?>
