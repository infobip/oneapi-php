<?php

namespace infobip\models\two_factor_authentication;

use infobip\Models;
use infobip\models\AbstractObject;

class TfaApplicationConfiguration extends AbstractObject {

    public $pinTimeToLive;
    public $pinAttempts;

    public $verificationAttempts;
    public $verificationIntervalLength;

    public $initiationAttempts;
    public $initiationIntervalLength;

    public $overallInitiationAttempts;
    public $overallInitiationIntervalLength;

    public $initiationThrottlingProlongationFactor;
    public $initiationThrottlingWaitPeriod;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\two_factor_authentication\TfaApplicationConfiguration');

?>
