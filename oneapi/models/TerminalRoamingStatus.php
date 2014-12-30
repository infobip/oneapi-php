<?php

namespace infobip\models;

use infobip\Models;
use infobip\SubObjectConversionRule;

class TerminalRoamingStatus extends AbstractObject {

    public $servingMccMnc;
    public $address;
    public $currentRoaming;
    public $resourceURL;
    public $retrievalStatus;
    public $callbackData;
    public $extendedData;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'infobip\models\TerminalRoamingStatus',
        array(
                new SubObjectConversionRule('infobip\models\ServingMccMnc', 'servingMccMnc'),
                new SubObjectConversionRule('infobip\models\TerminalRoamingExtendedData', 'extendedData'),
        )
);

