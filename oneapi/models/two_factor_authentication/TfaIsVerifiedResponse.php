<?php

namespace infobip\models\two_factor_authentication;

use infobip\Models;
use infobip\models\AbstractObject;

class TfaIsVerifiedResponse extends AbstractObject {

    public $verified;
    public $verifiedAt;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\two_factor_authentication\TfaIsVerifiedResponse');

?>
