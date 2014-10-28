<?php

class TfaResponse extends AbstractObject {

    public $smsId;
    public $phoneNumber;

    public $hlrStatus;
    public $smsStatus;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaResponse');

?>
