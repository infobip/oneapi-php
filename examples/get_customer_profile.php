<?php

include 'oneapi/client.php';

$customerProfileClient = new CustomerProfileClient(USERNAME, PASSWORD);
$customerProfileClient->login();

$customerProfile = $customerProfileClient->getCustomerProfile();

if(!$customerProfile->isSuccess()) {
    echo $customerProfile->exception;
    //Logs::printLogs();
    die(0);
}

echo $customerProfile, "\n";
//Logs::printLogs();
