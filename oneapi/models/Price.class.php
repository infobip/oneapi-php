<?php

class Price extends AbstractObject {

    public $price;
    public $currency;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }
}

Models::register('Price');

?>