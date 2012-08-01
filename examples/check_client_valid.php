<?php

require_once 'oneapi/client.php';

$smsClient = new SmsClient(USERNAME, PASSWORD);

echo 'smsClient validity is ', $smsClient->isValid() ? 'true' : 'false', "\n";

$dataConnectionProfileClient = new DataConnectionProfileClient(USERNAME, 'wrongpassword');

echo 'dataConnectionProfileClient validity is ', $dataConnectionProfileClient->isValid() ? 'true' : 'false', "\n";

assert(true === $smsClient->isValid());
assert(false === $dataConnectionProfileClient->isValid());
