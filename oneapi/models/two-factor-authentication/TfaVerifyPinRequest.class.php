<?php

class TfaVerifyPinRequest extends AbstractObject {

    public $applicationId;
    public $phoneNumber;
    public $pin;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaVerifyPinRequest');

?>
