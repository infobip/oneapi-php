<?php

class MoSubscriptions extends AbstractObject {

    public $subscriptions;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'MoSubscriptions',
        new ObjectConversionRule(function($object, $jsonData) {
                $subscriptions = Utils::getArrayValue($jsonData, 'subscriptions', array());
                $object->subscriptions = array();
                foreach($subscriptions as $subscription) {
                    $object->subscriptions[] = new MoSubscription($subscription, true);
                }
            }
        )
);

?>
