<?php

class TfaDeliveryInfo extends AbstractObject {

    public $status;
    public $finalStatus;
    public $description;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaDeliveryInfo');

?>
