<?php

namespace infobip\models;

use infobip\Models;

class SubscribeToDeliveryNotificationsRequest extends AbstractObject {

    public $senderAddress;
    public $notifyURL;
    public $criteria;
    public $callbackData;
    public $clientCorrelator;

    public function __construct() {
        parent::__construct();
    }

}

Models::register('infobip\models\SubscribeToDeliveryNotificationsRequest');
