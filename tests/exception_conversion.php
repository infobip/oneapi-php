<?php

use infobip\Conversions;

require_once '../vendor/autoload.php';

$json = '{"requestError":{"serviceException":{"text":"Request URI missing required component(s): ","messageId":"SVC0002","variables":[""]},"policyException":null}}';

$smsException = Conversions::createFromJSON('infobip\models\SmsException', $json, false);

assert($smsException);
assert($smsException->messageId == 'SVC0002');
assert($smsException->text == 'Request URI missing required component(s): ');
