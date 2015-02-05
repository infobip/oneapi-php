<?php

use infobip\CustomerProfileClient;

//require_once __DIR__ . '\..\oneapi\client.php';
require_once __DIR__ . '\vendor\autoload.php';

$customerProfileClient = new CustomerProfileClient(USERNAME, PASSWORD);
$customerProfileClient->login();

$accountBalance = $customerProfileClient->getAccountBalance();

if(!$accountBalance->isSuccess()) {
    echo $accountBalance->exception;
    die();
}

echo 'accountBalance=', $accountBalance->balance, $accountBalance->currency->symbol;
