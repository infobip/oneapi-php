<?

require_once 'oneapi/client.php';

$json = '{"deliveryInfoNotification":{"deliveryInfo":{"address":"tel:38598854702","deliveryStatus":"DeliveredToTerminal"},"callbackData":"1234"}}';

$status = SmsClient::unserializeDeliveryStatus($json);

assert($status->callbackData == "1234");
