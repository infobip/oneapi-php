<?

namespace infobip\models;

use infobip\Models;
use infobip\SubFieldConversionRule;
use infobip\SubObjectConversionRule;

class DeliveryInfoNotification extends AbstractObject {

    public $deliveryInfo;
    public $callbackData;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'infobip\models\DeliveryInfoNotification',
        array(
            new SubObjectConversionRule('infobip\models\DeliveryInfo', 'deliveryInfo', 'deliveryInfoNotification.deliveryInfo'),
            new SubFieldConversionRule('callbackData', 'deliveryInfoNotification.callbackData')
        )
);

?>
