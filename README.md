OneApi PHP client
============================

Basic messaging example
-----------------------

First initialize the messaging client using your username and password:

    $smsClient = new SmsClient(USERNAME, PASSWORD);


An exception will be thrown if your username and/or password are incorrect.

Prepare the message:

    $smsMessage = new SMSRequest();
    $smsMessage->senderAddress = SENDER_ADDRESS;
    $smsMessage->address = DESTINATION_ADDRESS;
    $smsMessage->message = 'Hello world';


Send the message:

    $smsMessageSendResult = $smsClient->sendSMS($smsMessage);


Later you can query for the delivery status of the message:

    // You can use $clientCorrelator or $smsMessageSendResult as an method call argument here:
    $smsMessageStatus = $smsClient->queryDeliveryStatus($smsMessageSendResult);
    $deliveryStatus = $smsMessageStatus->deliveryInfo[0]->deliveryStatus;

    echo 'Success:', $smsMessageStatus->isSuccess(), "\n";
    echo 'Status:', $deliveryStatus, "\n";
    if( ! $smsMessageStatus->isSuccess()) {
        echo 'Message id:', $smsMessageStatus->exception->messageId, "\n";
        echo 'Text:', $smsMessageStatus->exception->text, "\n";
        echo 'Variables:', $smsMessageStatus->exception->variables, "\n";
    }


Possible statuses are: **DeliveredToTerminal**, **DeliveryUncertain**, **DeliveryImpossible**, **MessageWaiting** and **DeliveredToNetwork**.

Messaging with notification push example
-----------------------

Same as with the standard messaging example, but when preparing your message:

    $smsMessage = new SMSRequest();
    $smsMessage->senderAddress = SENDER_ADDRESS;
    $smsMessage->address = DESTINATION_ADDRESS;
    $smsMessage->message = 'Hello world';
    $smsMessage->notifyURL = NOTIFY_URL;


When the delivery notification is pushed to your server as a HTTP POST request, you must process the body of the message with the following code:

    $result = SmsClient::unserializeDeliveryStatus();

    // Process $result here, e.g. just save it to a file:
    $f = fopen(FILE_NAME, 'w');
    fwrite($f, "\n-------------------------------------\n");
    fwrite($f, 'status: ' . $result->deliveryInfo->deliveryStatus . "\n") ;
    fwrite($f, 'address: ' . $result->deliveryInfo->address . "\n");
    fwrite($f, 'messageId: ' . $result->deliveryInfo->messageId . "\n");
    fwrite($f, 'clientCorrelator: '. $result->deliveryInfo->clientCorrelator . "\n");
    fwrite($f, 'callback data: ' . $result->callbackData . "\n");
    fwrite($f, "\n-------------------------------------\n");
    fclose($f);


Number Context example
-----------------------

Initialize and login the data connection client:

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);


Retrieve the roaming status (Number Context):

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);
    echo 'Number context result: \n<br>';
    echo 'servingMccMnc: ', $response->servingMccMnc,'\n<br>';
    echo 'address: ', $response->address,'\n<br>';
    echo 'currentRoaming: ', $response->currentRoaming,'\n<br>';
    echo 'resourceURL: ', $response->resourceURL,'\n<br>';
    echo 'retrievalStatus: ', $response->retrievalStatus,'\n<br>';
    echo 'callbackData: ', $response->callbackData,'\n<br>';
    echo 'extendedData: ', $response->extendedData,'\n<br>';
    echo 'IMSI: ', $response->extendedData->imsi,'\n<br>';
    echo 'destinationAddres: ', $response->extendedData->destinationAddress,'\n<br>';
    echo 'originalNetworkPrefix: ', $response->extendedData->originalNetworkPrefix,'\n<br>';
    echo 'portedNetworkPrefix: ', $response->extendedData->portedNetworkPrefix,'\n<br>';


Number Context with notification push example
-----------------------

Similar to the previous example, but this time you must set the notification url where the result will be pushed:

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS, NOTIFY_URL);
    // if there is no error the query has been succesfully executed
    if(!$response->isSuccess()) {
        echo 'Error:', $response->exception, "\n";
        Logs::printLogs();
    }


When the roaming status notification is pushed to your server as a HTTP POST request, you must process the body of the message with the following code:

    $result = DataConnectionProfileClient::unserializeRoamingStatus();

    // Process $result here, e.g. just save it to a file:
    $f = fopen(FILE_NAME, 'w');
    fwrite($f, "\n-------------------------------------\n");
    fwrite($f, 'callbackData: ' . $result->callbackData . "\n") ;
    fwrite($f, 'servingMccMnc: '. $result->terminalRoamingStatus->servingMccMnc . "\n") ;
    fwrite($f, 'address: '. $result->terminalRoamingStatus->address . "\n") ;
    fwrite($f, 'currentRoaming: ' . $result->terminalRoamingStatus->currentRoaming . "\n") ;
    fwrite($f, 'resourceURL: ' . $result->terminalRoamingStatus->resourceURL . "\n") ;
    fwrite($f, 'retrievalStatus: ' . $result->terminalRoamingStatus->retrievalStatus . "\n") ;
    fwrite($f, 'terminalRoamingStatus callbackData: ' . $result->terminalRoamingStatus->callbackData . "\n") ;
    fwrite($f, 'extendedData: ' . $result->terminalRoamingStatus->extendedData . "\n") ;
    fwrite($f, 'IMSI: ', $response->extendedData->imsi,'\n');
    fwrite($f, 'destinationAddress: ', $response->extendedData->destinationAddress,'\n');
    fwrite($f, 'originalNetworkPrefix: ', $response->extendedData->originalNetworkPrefix,'\n');
    fwrite($f, 'portedNetworkPrefix: ', $response->extendedData->portedNetworkPrefix,'\n');
    fwrite($f, "\n-------------------------------------\n");
    fclose($f);


Retrieve inbound messages example
-----------------------

With the existing sms client (see the basic messaging example to see how to start it):

    $inboundMessages = $smsClient->retrieveInboundMessages();

    foreach($inboundMessages->inboundSMSMessage as $message) {
        echo $message->dateTime;
        echo $message->destinationAddress;
        echo $message->messageId;
        echo $message->message;
        echo $message->resourceURL;
        echo $message->senderAddress;
    }


Inbound message push example
-----------------------

The subscription to receive inbound messages can be set up on our site.
When the inbound message notification is pushed to your server as a HTTP POST request, you must process the body of the message with the following code:

    // returns a single message not array of messages
    $inboundMessages = SmsClient::unserializeInboundMessages();

    // Process $inboundMessages here, e.g. just save it to a file:
    $f = fopen(FILE_NAME, 'w');
    fwrite($f, "\n-------------------------------------\n");
    fwrite($f, 'dateTime: ' . $inboundMessages->dateTime . "\n");
    fwrite($f, 'destinationAddress: '  . $inboundMessages->destinationAddress . "\n");
    fwrite($f, 'messageId: '  . $inboundMessages->messageId . "\n");
    fwrite($f, 'message: '  . $inboundMessages->message . "\n");
    fwrite($f, 'resourceURL: '  . $inboundMessages->resourceURL . "\n");
    fwrite($f, 'senderAddress: '  . $inboundMessages->senderAddress . "\n");

Social invites sms example
--------------------------

If you have Social Invites application registered and configured ([tutorial](http://developer.infobip.com/getting-started/tutorials/social-invite)), you can send invitations.

First initialize the social invites client using your username and password:

    $socinv = new SocialInviteClient(USERNAME, PASSWORD);

Prepare the social invitation:

    $siReq = new SocialInviteRequest();
    $siReq->senderAddress = SENDER_ADDRESS;
    $siReq->recipients = DESTINATION_ADDRESS;
    $siReq->messageKey = SOCIAL_INVITES_MESSAGE_KEY;

Send the message:

    $siResult = $socinv->sendInvite($siReq, SOCIAL_INVITES_APP_SECRET);

Later you can query for the delivery status of the social invite message:

    // You can use $siResult->sendSmsResponse->bulkId as an argument here:
    $smsMessageStatus = $smsClient->queryDeliveryStatus($siResult->sendSmsResponse->bulkId);
    $deliveryStatus = $smsMessageStatus->deliveryInfo[0]->deliveryStatus;

    echo 'Success:', $smsMessageStatus->isSuccess(), "\n";
    echo 'Status:', $deliveryStatus, "\n";
    if( ! $smsMessageStatus->isSuccess()) {
        echo 'Message id:', $smsMessageStatus->exception->messageId, "\n";
        echo 'Text:', $smsMessageStatus->exception->text, "\n";
        echo 'Variables:', $smsMessageStatus->exception->variables, "\n";
    }

License
-------

This library is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)
