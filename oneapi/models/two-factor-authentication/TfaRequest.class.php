<?php

class TfaRequest extends AbstractObject {

    public $applicationId;
    public $messageId;

    public $senderId;
    public $phoneNumber;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaRequest');

?>
