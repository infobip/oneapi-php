<?php

class Status extends AbstractObject {

    public $id;
    public $status;
    public $description;
    public $failureReason;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }
}

Models::register('Status');

?>