<?php

namespace infobip\models;

use infobip\Models;
use infobip\ObjectArrayConversionRule;

class OutboxMessages extends AbstractObject
{

    public $logs;
    public $from;
    public $to;
    public $hasMoreLogs;

    public function __construct($array = null, $success = true)
    {
        parent::__construct($array, $success);
    }

    public function isMoreAvailable()
    {
        return $this->hasMoreLogs;
    }

}

Models::register(
    'infobip\models\OutboxMessages',
    new ObjectArrayConversionRule('infobip\models\OutboxMessage', 'logs')
);

?>
