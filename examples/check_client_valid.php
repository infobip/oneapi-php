<?php


use infobip\DataConnectionProfileClient;
use infobip\SmsClient;

require_once __DIR__ . '\..\oneapi\client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);
$smsClient->login();

echo 'smsClient validity is ', $smsClient->isValid() ? 'true' : 'false', "\n";

$dataConnectionProfileClient = new DataConnectionProfileClient(USERNAME, 'wrongpassword');
$dataConnectionProfileClient->login();

echo 'dataConnectionProfileClient validity is ', $dataConnectionProfileClient->isValid() ? 'true' : 'false', "\n";

assert(true === $smsClient->isValid());
assert(false === $dataConnectionProfileClient->isValid());
