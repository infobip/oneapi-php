<?

namespace infobip\models;

use infobip\Models;
use infobip\SubFieldConversionRule;
use infobip\SubObjectConversionRule;

class TerminalRoamingStatusNotification extends AbstractObject {

    public $terminalRoamingStatus;
    public $callbackData;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'infobip\models\TerminalRoamingStatusNotification',
        array(
            new SubObjectConversionRule('infobip\models\TerminalRoamingStatus', 'terminalRoamingStatus', 'terminalRoamingStatusList.roaming'),
            new SubFieldConversionRule('callbackData', 'terminalRoamingStatusList.roaming.callbackData')
        )
);

?>
