<?

class DeliveryInfo extends AbstractObject {

    public $deliveryStatus;

    public function __construct() {
        parent::__construct();
    }

}

Models::register('DeliveryInfo');
