<?php

namespace infobip\models;

use infobip\Models;

class ServingMccMnc extends AbstractObject {

    public $mcc;
    public $mnc;

    public function __construct() {
        parent::__construct();
    }

}

Models::register('infobip\models\ServingMccMnc');

