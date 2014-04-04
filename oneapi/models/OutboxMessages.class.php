<?php

class OutboxMessages extends AbstractObject
{

    public $logs;
    public $from;
    public $to;

    public function __construct($array = null, $success = true)
    {
        parent::__construct($array, $success);
    }

    public function isMoreAvailable()
    {
        return sizeof($this->logs) >= 100000;
    }

}

Models::register(
    'OutboxMessages',
    new ObjectArrayConversionRule('OutboxMessage', 'logs')
);

?>
