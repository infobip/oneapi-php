<?php

class TfaIsVerifiedResponse extends AbstractObject {

    public $verified;
    public $verifiedAt;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaIsVerifiedResponse');

?>
