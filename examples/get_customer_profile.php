<?php

include 'oneapi/client.php';

$customerProfilerClient = new CustomerProfilerClient(USERNAME, PASSWORD);

$customerProfile = $customerProfilerClient->getCustomerProfile();

if(!$customerProfile->isSuccess()) {
    echo $customerProfile->exception;
    //Logs::printLogs();
    die(0);
}

echo $customerProfile, "\n";
//Logs::printLogs();
