<?php

class TfaIsVerifiedRequest extends AbstractObject {

    public $applicationId;
    public $phoneNumber;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaIsVerifiedRequest');

?>
