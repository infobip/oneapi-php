<?php

namespace infobip\models;

// TODO: Remove this object and use only TerminalRoamingStatus !
use infobip\Models;
use infobip\SubObjectConversionRule;

class TerminalRoamingStatusList extends AbstractObject {

    public $terminalRoamingStatus;

    public function __construct() {
        parent::__construct();
    }

}

Models::register(
        'infobip\models\TerminalRoamingStatusList',
        new SubObjectConversionRule('infobip\models\TerminalRoamingStatus', 'terminalRoamingStatus', 'terminalRoamingStatusList.roaming')
);

