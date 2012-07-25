<?php

class DeliveryReportSubscription extends AbstractObject {

    public $subscriptionId;
    public $senderAddress;
    public $notifyURL;
    public $criteria;
    public $callbackData;
    public $clientCorrelator;

    public function __construct() {
        parent::__construct();
    }

}

Models::register('DeliveryReportSubscription');
