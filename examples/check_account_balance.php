<?php

require_once 'oneapi/client.php';

$customerProfileClient = new CustomerProfileClient(USERNAME, PASSWORD);

$accountBalance = $customerProfileClient->getAccountBalance();

echo 'accountBalance=', $accountBalance;
