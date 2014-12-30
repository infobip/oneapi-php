<?php

namespace infobip\models\iam;

use infobip\Models;
use infobip\models\AbstractObject;
use infobip\SubObjectConversionRule;

class IamRequestError extends AbstractObject {

    public $serviceException;
    public $clientCorrelator;
    public $variables;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\iam\IamRequestError', array (
  new SubObjectConversionRule('infobip\models\iam\IamServiceException', 'serviceException')
));

?>
