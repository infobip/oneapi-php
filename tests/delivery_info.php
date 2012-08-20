<?php

require_once 'oneapi/client.php';

$json = '{"deliveryInfoList":{"deliveryInfo":[{"address":"38598123456","deliveryStatus":"DeliveredToTerminal"}],"resourceURL":"http://api.parseco.com/1/smsmessaging/outbound/TODO/requests/ih5k7mm6dy/deliveryInfos"}}';

$deliveryInfoList = Conversions::createFromJSON('DeliveryInfoList', $json, false);

assert($deliveryInfoList);
assert(sizeof($deliveryInfoList->deliveryInfo) == 1);
assert($deliveryInfoList->deliveryInfo[0]->deliveryStatus == 'DeliveredToTerminal');
