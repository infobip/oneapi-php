### System overview
[Parseco](https://github.com/parseco/) is an [API](http://en.wikipedia.org/wiki/Application_programming_interface) implementation inspired by [OneApi](http://oneapi.gsma.com/api-list/) specification which is issued by the Global System for Mobile Communications Association. 
We felt the need to enrich the specification by adding a few fields in some requests and responses to make the API more comfortable for the developer.

Parseco API exposes the following mobile network functionalities:

 * <strong>Short message service</strong> (<strong>SMS</strong>) is the most widespread mobile network data application. The term stands for the service as well as the text message itself. We fully support [Unicode](http://en.wikipedia.org/wiki/Unicode) [UTF-16](http://en.wikipedia.org/wiki/UTF-16) character set so that you can use virtually any alphabet for composing your text. The only limitation for SMS messages is the message length which is 80 characters in case of a Unicode encoded message, or 160 characters in case the message is not Unicode encoded.
 * <strong>Unstructured supplementary services data</strong> (<strong>USSD</strong>) is mostly used for prepaid callback service, mobile-money services and menu-based information services. It is a connection-based data protocol which makes it more responsive than the message-based SMS. Connection-based means that a connection (session) is established and kept alive for the entire time during the communication. That is why it is sometimes used for WAP browsing. The length of the USSD message is up to 182 alphanumeric characters in length. Unfortunately, Unicode encoding is not supported.
 * <strong>Home location register</strong> (<strong>HLR</strong>) is a central database which stores details about every mobile phone subscriber for that network. A HLR record holds valuable data such as: is the phone turned on or off, is it connected to home network or not, is it ported (meaning that it has the network prefix of a network that is not actually belonging to) etc.

Other mobile network-related functionalities are due to be implemented.
In order to use Parseco API and gain access to [Infobip](http://www.infobip.com) mobile networks aggregator system you must [register](http://www.parseco.com/sign-up/) at the Parseco website.
In other words, by using Parseco PHP library you can [send SMS messages](http://www.parseco.com/#features-list) to **any** cell phone [around the globe](http://www.parseco.com/pricing-and-coverage/).
<br/>screenshot: system-landscape

### Prerequisites
* You have [installed a PHP interpreter](http://php.net/manual/en/install.php).
* You have downloaded the [Parseco OneAPI PHP library](https://github.com/parseco/oneapi-php/tree/master/oneapi)


### General assumptions which must be fulfilled for all the following examples
 * You must have an active Internet connection.
 * You have satisfied the prerequisites and [signed up](http://www.parseco.com/sign-up/) at Parseco website. After sign-up, SMS message with the verification PIN will be sent to your cell phone.

screenshot: account verification page screenshot<br/>
Input the four-digit PIN from the received SMS message in the verification box and press verify.
Congratulations on your successful registration - you can start using Parseco API! If you want, you can try out the [live demos](http://www.parseco.com/demos/) now.

### Assumptions which must be fulfilled for the examples with notification push
In every example two different architectural approaches are shown.
In the first scenario the mobile-originated (see example 3 for term explanation) information is returned to the (web) application that requested the operation.
In the second scenario the mobile-terminated information is still being sent by your (web) application, but the mobile-originated information is returned to an URL predefined by you via HTTP POST request. 
In other words, Parseco pushes the receiving inbound notifications (be it HLR or delivery data, or messages) to your web application.

 * You must have your own web application in order to provide the URL for the Parseco push notifications.
 * You must register the URL mentioned above as notification URL at Parseco site [setup wizard](http://www.parseco.com/application/setup-wizard/)
 * Your inbound messages will be available for a period of 48 h after being received by our gateways.

For a given notification URL the [setup wizard](http://www.parseco.com/application/setup-wizard/) generates a pair of subscription number and keyword. The just generated subscription will be shown in the list below:
 
 * <strong>Id</strong> - The id of the subscription.
 * <strong>Address</strong> - Your GSM subscription number to which inbound messages are sent. **Prefix it with '+' prior to sending SMS message.**
 * <strong>Criteria</strong> - String which **must** be present at the start of the SMS message text, otherwise Parseco won't forward it to your code.
 * <strong>Notify URL</strong> - The registered URL to receive Parseco push notifications
 * <strong>Action</strong> - Action for subscription.

screenshot subscriptions<br/>

The "Notify URL" field is crucial. 
If it is present, then the approach with notification push is chosen, meaning that all your mobile-originated information will be sent to it via HTTP POST request. 
If it is not present then the approach without notification push is chosen, meaning that all your mobile-originated information will be returned to the (web) application that requested the operation.
If you make changes, a "Save" button will appear in the "Action" column. If you want to apply the changes, press it.


### Notice
 * **After signup you won't be able to use any of our services for 2 to 5 minutes until the system propagates the changes.**
 * **Until you make your first payment the only GSM number to which you can send messages is the one tied to your Parseco account. It is meant for demo purposes only, so you have a 5 &euro; budget for testing, which roughly translates to 500 or less SMS messages, [depending upon your location](http://www.parseco.com/pricing-and-coverage/).**
 * All examples are [valid, runnable](http://sscce.org/) code snippets,  you can copy them to a new empty PHP file, and replace the PATH_TO_LIBRARY with a string containing the path to the downloaded library. There may be other strings to replace, e.g. as in example 1.1. After you have done that, you can run them.


### Example 1.1: Basic messaging (Hello world)

The first thing that needs to be done is to initialize the client with username and password in plaintext.
You are basically logging in to Parseco, so an exception will be thrown if the username and/or password are incorrect. The next step is to prepare the message:

 * <strong>sender address</strong> - value which will appear in the FROM field on the destination cell phone
 * <strong>address</strong> - GSM number of the destination cell phone
 * <strong>message</strong> - the contents of the SMS message

Sender address may be any string composed of printable characters but will it be delivered as such depends on the settings of the destination [network operator](http://en.wikipedia.org/wiki/Mobile_network_operator).
Therefore, our recommendation (but not a guarantee) is to use the [English](http://en.wikipedia.org/wiki/English_alphabet) [alphanumeric](http://en.wikipedia.org/wiki/Alphanumeric) character subset.

When you execute the send method it will return an object which is used to query the message delivery status.
These are possible states of the message once you have sent it:  
 
 * <strong>DeliveredToTerminal</strong> - the message has been delivered to the cell phone
 * <strong>DeliveredToNetwork</strong> - the message has been delivered to the cell phone network operator
 * <strong>MessageWaiting</strong> - message is pending delivery to the cell phone network operator
 * <strong>DeliveryImpossible</strong> - message will not be delivered
 * <strong>DeliveryUncertain</strong> - delivery cannot be confirmed due to network operator settings. It still may be delivered but we will never know it.

Now you are ready to send the message.

    <?php

    require_once(PATH_TO_LIBRARY);

    $smsClient = new SmsClient(USERNAME, PASSWORD);

    $smsMessage = new SMSRequest();
    $smsMessage->senderAddress = SENDER_ADDRESS;
    $smsMessage->address = DESTINATION_ADDRESS;
    $smsMessage->message = 'Hello world';

    $smsMessageSendResult = $smsClient->sendSMS($smsMessage);

    // The client correlator is a unique identifier of this api call:
    $clientCorrelator = $smsMessageSendResult->clientCorrelator;

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

    ?>


### Example 1.2: Basic messaging (Hello world) with notification push
Set the notify URL when sending message:

    <?php

    require_once(PATH_TO_LIBRARY);

    $smsClient = new SmsClient(USERNAME, PASSWORD);

    $smsMessage = new SMSRequest();
    $smsMessage->senderAddress = SENDER_ADDRESS;
    $smsMessage->address = DESTINATION_ADDRESS;
    $smsMessage->message = 'Hello world';
    $smsMessage->notifyURL = NOTIFY_URL;

    $smsMessageSendResult = $smsClient->sendSMS($smsMessage);

    ?>


Parseco will send a HTTP POST request to this URL, and your web application must process it like his:

    <?php

    require_once(PATH_TO_LIBRARY);

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

    ?>


Note that there is nothing stopping you from running both code snippets on the same host or within the same web application, but it is not necessary.

### Example 2.1: Cell phone roaming status (HLR query)
When the cell phone is connected to a network other than his home operator network it is said to be [roaming](http://en.wikipedia.org/wiki/Roaming).
This is just a part of the information about a cell phone that can be obtained via a [HLR](http://en.wikipedia.org/wiki/Network_switching_subsystem#Home_location_register_.28HLR.29) query like in the example below.

    <?php

    require_once(PATH_TO_LIBRARY);

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);
    echo 'HLR result: \n<br>';
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

    ?>


### Example 2.2: Cell phone roaming status (HLR query) as notification push
Set the notify URL when sending message:

    <?php

    require_once(PATH_TO_LIBRARY);

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS, NOTIFY_URL);
    // if there is no error the query has been succesfully executed
    if(!$response->isSuccess()) {
        echo 'Error:', $response->exception, "\n";
        Logs::printLogs();
    }

    ?>


Parseco will send a HTTP POST request to this URL, and your web application must process it like this:

    <?php

    require_once(PATH_TO_LIBRARY);

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
    fwrite($f, 'destinationAddres: ', $response->extendedData->destinationAddress,'\n');
    fwrite($f, 'originalNetworkPrefix: ', $response->extendedData->originalNetworkPrefix,'\n');
    fwrite($f, 'portedNetworkPrefix: ', $response->extendedData->portedNetworkPrefix,'\n');
    fwrite($f, "\n-------------------------------------\n");
    fclose($f);

    ?>


Note that there is nothing stopping you from running both code snippets on the same host or within the same web application, but it is not necessary.

### Example 3.1: Process inbound messages (two way communication)
Two way communication with cell phone is also possible via Parseco.
The messages your application sends to cell phones are outbound or mobile-terminated messages.
It is a scenario much like in the first example.
The messages which your application receives from cell phones are inbound or mobile-originated messages.
In order to be able to receive inbound messages programmatically you must have a valid GSM subscription number.
For demo purposes, a valid 30-day trial GSM subscription number is tied to your Parseco account.
Our paid services include (info coming soon, mail to info@parseco.com):

 * you may register a single subscription number 
 * you may register a single subscription number paired-up with a keyword of your choice
 * you may register a single subscription number paired-up with multiple keywords oy our choice
 * you may register for all or some of the above as many times as you like 

In order for the below example to work make sure that you have a subscription with no notify URL set at your [Parseco account](http://www.parseco.com/application/setup-wizard/).

    <?php

    require_once(PATH_TO_LIBRARY);

    $smsClient = new SmsClient(USERNAME, PASSWORD);

    $inboundMessages = $smsClient->retrieveInboundMessages();
    
    foreach($inboundMessages->inboundSMSMessage as $message) {
        echo $message->dateTime;
        echo $message->destinationAddress;
        echo $message->messageId;
        echo $message->message;
        echo $message->resourceURL;
        echo $message->senderAddress;
    }

    ?>


### Example 3.2: Process inbound messages (two way communication) as notification push
In order for the below example to work make sure that you have a subscription with a notify URL set at your [Parseco account](http://www.parseco.com/application/setup-wizard/). 
Of course, the notify URL must be mapped to your web application.

    <?php

    require_once(PATH_TO_LIBRARY);

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

    ?>

