<?php

namespace infobip\models\two_factor_authentication;

use infobip\Models;
use infobip\models\AbstractObject;
use infobip\SubObjectConversionRule;

class TfaApplication extends AbstractObject {

    public $applicationId;
    public $name;
    public $enabled;
    public $processId;
    public $configuration;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\two_factor_authentication\TfaApplication', array (
  new SubObjectConversionRule('infobip\models\two_factor_authentication\TfaApplicationConfiguration', 'configuration')
));

?>
