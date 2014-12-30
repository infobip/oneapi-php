<?php

namespace infobip\models\iam;

use infobip\Models;
use infobip\models\AbstractObject;
use infobip\SubObjectConversionRule;

class IamException extends AbstractObject {

    public $requestError;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\iam\IamException', array (
  new SubObjectConversionRule('infobip\models\iam\IamRequestError', 'requestError')
));

?>
