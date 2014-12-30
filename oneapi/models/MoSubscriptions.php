<?php

namespace infobip\models;

use infobip\Models;
use infobip\ObjectArrayConversionRule;

class MoSubscriptions extends AbstractObject {

    public $subscriptions;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'infobip\models\MoSubscriptions',
        new ObjectArrayConversionRule('infobip\models\MoSubscription', 'subscriptions')
);

?>
