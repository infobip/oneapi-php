<?php

class TfaMessage extends AbstractObject {

    public $applicationId;
    public $messageId;

    public $senderId;
    public $messageText;
    public $pinPlaceholder;

    public $pinLength;
    public $pinType;


    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaMessage');

?>
