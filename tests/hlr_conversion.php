<?

require_once 'oneapi/client.php';
require_once 'oneapi/models.php';

$json = '{"terminalRoamingStatusList":{"roaming":{"address":"38598854702","currentRoaming":null,"servingMccMnc":{"mcc":"219","mnc":"01"},"resourceURL":null,"retrievalStatus":"Error","extendedData":{"destinationAddress":"38598854702","statusId":5,"doneTime":1343312580170,"pricePerMessage":5.0,"mccMnc":"21901","servingMsc":"38598042003","censoredServingMsc":"3859804","gsmErrorCode":0,"originalNetworkName":"T-Mobile HR","portedNetworkName":"T-Mobile HR","servingHlr":"3859812008","imsi":"219014100133704","originalNetworkPrefix":"98","originalCountryPrefix":"385","originalCountryName":"Croatia                                           ","isNumberPorted":false,"portedNetworkPrefix":"97","portedCountryPrefix":"385","portedCountryName":"Croatia                                           ","numberInRoaming":false},"callbackData":null}}}';

//print_r(json_decode($json, false));

$object = Conversions::createFromJSON('TerminalRoamingStatusList', $json, false);

echo $object;

assert($object != null);
assert($object->terminalRoamingStatus != null);
assert($object->terminalRoamingStatus->servingMccMnc != null);
assert($object->terminalRoamingStatus->servingMccMnc->mcc == '219');
assert($object->terminalRoamingStatus->extendedData != null);
assert(trim($object->terminalRoamingStatus->extendedData->originalCountryName) == 'Croatia');
assert($object->terminalRoamingStatus->extendedData->isNumberPorted === false);
assert($object->terminalRoamingStatus->extendedData->portedNetworkPrefix == '97');
