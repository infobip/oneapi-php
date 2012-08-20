<?

require_once 'oneapi/client.php';

$string = '{"terminalRoamingStatusList":{"roaming":{"address":"38598123456","currentRoaming":"NotRoaming","servingMccMnc":{"mcc":"219","mnc":"01"},"resourceURL":null,"retrievalStatus":"Retrieved","extendedData":{"destinationAddress":"38598123456","statusId":5,"doneTime":1345454221270,"pricePerMessage":5.0,"mccMnc":"21901","servingMsc":"38598042001","censoredServingMsc":"3859804","gsmErrorCode":0,"originalNetworkName":"T-Mobile HR","portedNetworkName":"T-Mobile HR","servingHlr":"3859812005","imsi":"219014100019459","originalNetworkPrefix":"98","originalCountryPrefix":"385","originalCountryName":"Croatia","isNumberPorted":false,"portedNetworkPrefix":"97","portedCountryPrefix":"385","portedCountryName":"Croatia","numberInRoaming":false},"callbackData":"test"}}}';

$status = SmsClient::unserializeRoamingStatus($string);

assert($status->terminalRoamingStatus->extendedData->destinationAddress == '38598123456');
assert($status->callbackData == "test");
