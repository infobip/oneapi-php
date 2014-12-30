<?php

namespace infobip\models;

use infobip\Models;

class SmsMessageSubscription extends AbstractObject {

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\SmsMessageSubscription');


?>
