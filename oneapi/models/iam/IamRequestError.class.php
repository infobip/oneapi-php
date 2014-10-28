<?php

class IamRequestError extends AbstractObject {

    public $serviceException;
    public $clientCorrelator;
    public $variables;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('IamRequestError', array (
  new SubObjectConversionRule('IamServiceException', 'serviceException')
));

?>
