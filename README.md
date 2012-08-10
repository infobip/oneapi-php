OneApi php client
============================

Basic messaging example
-----------------------

First initialize the messaging client using your username and password:

    $smsClient = new SmsClient(USERNAME, PASSWORD);


Then login with the client:

    $smsClient->login();


An exception will be thrown if your username and/or password are incorrect.

Prepare the message:

    $smsMessage = new SMSRequest();
    $smsMessage->senderAddress = SENDER_ADDRESS;
    $smsMessage->address = DESTINATION_ADDRESS;
    $smsMessage->message = 'Test message';


Send the message:

    $smsMessageSendResult = $smsClient->sendSMS($smsMessage);
    $clientCorrelator = $smsMessageSendResult->clientCorrelator;


Later you can query for the delivery status of the message:

    $smsMessageStatus = $smsClient->queryDeliveryStatus($smsMessageSendResult);
    $deliveryStatus = $smsMessageStatus->deliveryInfo[0]->deliveryStatus;


Possible statuses are: **DeliveredToTerminal**, **DeliveryUncertain**, **DeliveryImpossible**, **MessageWaiting** and **DeliveredToNetwork**.

Messaging with notification push example
-----------------------

Same as with the standard messaging example, but when preparing your message:

    $smsMessage = new SMSRequest();
    $smsMessage->senderAddress = SENDER_ADDRESS;
    $smsMessage->address = DESTINATION_ADDRESS;
    $smsMessage->message = 'Test message';
    $smsMessage->notifyURL = NOTIFY_URL;


When the delivery notification is pushed to your server as a HTTP POST request, you must process the body of the message with the following code:

    TODO

HLR example
-----------------------

Initialize and login the data connection client:

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);
    $client->login();


Retrieve the roaming status (HLR):

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);


HLR with notification push example
-----------------------

Similar to the previous example, but this time you must set the notification url where the result will be pushed:

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS, NOTIFY_URL);


When the roaming status notification is pushed to your server as a HTTP POST request, you must process the body of the message with the following code:

    $result = Conversions::createFromJSON('TerminalRoamingStatusList', $requestBody);


Retrieve inbound messages example
-----------------------

With the existing sms client (see the basic messaging example to see how to start it):

    $inboundMessages = $smsClient->retrieveInboundMessages();
    
    foreach($inboundMessages->inboundSMSMessage as $message) {
        echo $message;
    }


Inbound message push example
-----------------------

The subscription to recive inbound messages can be set up on our site.
When the inbound message notification is pushed to your server as a HTTP POST request, you must process the body of the message with the following code:

    TODO

License
-------

This library is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)
