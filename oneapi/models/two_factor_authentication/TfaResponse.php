<?php

namespace infobip\models\two_factor_authentication;

use infobip\Models;
use infobip\models\AbstractObject;

class TfaResponse extends AbstractObject {

    public $smsId;
    public $phoneNumber;

    public $hlrStatus;
    public $smsStatus;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\two_factor_authentication\TfaResponse');

?>
