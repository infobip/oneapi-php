<?php

namespace infobip\models;

use infobip\Models;

class InboundSmsMessage extends AbstractObject {

    public $dateTime;
    public $destinationAddress;
    public $messageId;
    public $message;
    public $resourceURL;
    public $senderAddress;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\InboundSmsMessage');

?>