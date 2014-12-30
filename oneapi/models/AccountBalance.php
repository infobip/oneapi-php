<?php

namespace infobip\models;

use infobip\Models;
use infobip\SubObjectConversionRule;

class AccountBalance extends AbstractObject {

	public $balance;

	public $currency;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'infobip\models\AccountBalance',
        new SubObjectConversionRule('infobip\models\Currency', 'currency')
);
