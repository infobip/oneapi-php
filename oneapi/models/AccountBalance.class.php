<?php

class AccountBalance extends AbstractObject {

	public $totalCash;

	public $currency;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'AccountBalance',
        new SubObjectConversionRule('Currency', 'currency')
);
