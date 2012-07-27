<?php
require_once 'oneapi/object.php';

require_once 'oneapi/Logs.class.php';

// Basic messaging:
require_once 'oneapi/models/SMSRequest.class.php';
require_once 'oneapi/models/ResourceReference.class.php';
require_once 'oneapi/models/DeliveryInfo.class.php';
require_once 'oneapi/models/DeliveryInfoList.class.php';

// Delivery reports:
require_once 'oneapi/models/DeliveryReportSubscription.class.php';
require_once 'oneapi/models/SubscribeToDeliveryNotificationsRequest.class.php';
require_once 'oneapi/models/DeliveryReceiptSubscription.class.php';
require_once 'oneapi/models/DeliveryReceiptSubscriptions.class.php';

// HLR
require_once 'oneapi/models/TerminalRoamingStatusList.class.php';
require_once 'oneapi/models/TerminalRoamingStatus.class.php';
require_once 'oneapi/models/ServingMccMnc.class.php';
require_once 'oneapi/models/TerminalRoamingExtendedData.class.php';

require_once 'oneapi/models/Captcha.class.php';
require_once 'oneapi/models/Country.class.php';
require_once 'oneapi/models/Countries.class.php';
require_once 'oneapi/models/Timezone.class.php';
require_once 'oneapi/models/Timezones.class.php';
require_once 'oneapi/models/Encoding.class.php';
require_once 'oneapi/models/Encodings.class.php';
require_once 'oneapi/models/CustomerProfile.class.php';
require_once 'oneapi/models/Hlr.class.php';
require_once 'oneapi/models/HlrSendResult.class.php';
require_once 'oneapi/models/MoSubscription.class.php'; 
require_once 'oneapi/models/MoSubscriptions.class.php';
require_once 'oneapi/models/SmsAuthentication.class.php';
require_once 'oneapi/models/SmsException.class.php';
require_once 'oneapi/models/SmsMessageSubscription.class.php';
require_once 'oneapi/models/GenericObject.class.php';
require_once 'oneapi/models/InboundSmsMessage.class.php';
require_once 'oneapi/models/InboundSmsMessages.class.php';

// add oneapi/models/ dir in autoload stack
/*
spl_autoload_register(function($clsname) {
    $fileName = '/oneapi/models/' . $clsname . '.class.php';
    if(is_file($fileName))
        include '/oneapi/models/' . $clsname . '.class.php';
});
*/
?>
