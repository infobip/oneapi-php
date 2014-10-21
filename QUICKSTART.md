### System overview

[Infobip OneAPI](https://developer.infobip.com/api) is an [API](http://en.wikipedia.org/wiki/Application_programming_interface) implementation inspired by [OneApi](http://www.gsma.com/oneapi/) specification which is issued by the Global System for Mobile Communications Association. We felt the need to enrich the specification by adding a few fields in some requests and responses to make the API more comfortable for the developer.

Infobip OneAPI exposes the following mobile network functionalities:

-   **Short message service** (**SMS**) is the most widespread mobile network data application. The term stands for the service as well as the text message itself. We fully support [Unicode](http://en.wikipedia.org/wiki/Unicode) [UTF-16](http://en.wikipedia.org/wiki/UTF-16) character set so that you can use virtually any alphabet for composing your text.
-   **Unstructured supplementary services data** (**USSD**) is mostly used for prepaid callback service, mobile-money services and menu-based information services. It is a connection-based data protocol which makes it more responsive than the message-based SMS. Connection-based means that a connection (session) is established and kept alive for the entire time during the communication. That is why it is sometimes used for WAP browsing. The length of the USSD message is up to 182 alphanumeric characters in length. Unfortunately, Unicode encoding is not supported.
-   **[Number Context](http://www.infobip.com/services/number_context)** Infobip's Number Context service communicates with a relevant mobile number's home network and can identify whether the subscribers handset is roaming on another network, currently active or has been disabled.
    <sub>** HLR service has been renamed to Number Context service**</sub>

Other mobile network-related functionalities are due to be implemented. In order to use Infobip OneAPI and gain access to [Infobip](https://www.infobip.com) mobile networks aggregator system you must [register](https://www.infobip.com/sign_up/) at the Infobip website. In other words, by using Infobip OneAPI PHP library you can [send SMS messages](https://infobip.com/messaging/) to **any** cell phone [around the globe](https://www.infobip.com/about/platform/).

### Prerequisites

-   You have [installed a PHP interpreter](http://php.net/manual/en/install.php).
-   You have downloaded the [Infobip OneAPI PHP library](https://github.com/infobip/oneapi-php/tree/master/oneapi)

### General assumptions which must be fulfilled for all the following examples

-   You must have an active Internet connection.
-   You have satisfied the prerequisites and [signed up](https://www.infobip.com/sign-up/) at Infobip website. After sign-up, SMS message with the verification PIN will be sent to your cell phone. Input the four-digit PIN from the received SMS message in the verification box and press verify.

### Assumptions which must be fulfilled for the examples with notification push

In every example two different architectural approaches are shown. In the first scenario the mobile-originated (see example 3 for term explanation) information is returned to the (web) application that requested the operation.

In the second scenario the mobile-terminated information is still being sent by your (web) application, but the mobile-originated information is returned to an URL predefined by you via HTTP POST request. In other words, Infobip pushes the receiving inbound notifications (be it Number Context or delivery data, or messages) to your web application.

-   You must have your own web application in order to provide the URL for the Infobip push notifications.
-   You must register the URL mentioned above as notification URL[using API](http://developer.infobip.com/api#!/ReceiveSMS/POST_1_smsmessaging_inbound_subscriptions).
-   Your inbound messages will be available for a period of 48 h after being received by our gateways.

### Notice

-   **After signup you won't be able to use any of our services for few minutes (up to five minutes) until the system propagates the changes.**
-   **Until you make your first payment the only GSM number to which you can send messages is the one tied to your Infobip account. It is meant for demo purposes only, so you have a 5 € budget for testing, which roughly translates to 500 or less SMS messages depending upon your location.**
-   All examples are [valid, runnable](http://sscce.org/) code snippets, you can copy them to a new empty PHP file, and replace the *PATH\_TO\_LIBRARY* with a string containing the path to the client.php file from downloaded library. There may be other strings to replace, e.g. as in example 1.1. If your operating system supports it, don't forget to set appropriate file permissions. After you have done that, you can run them.

### Example 1.1: Basic messaging (Hello world)

The first thing that needs to be done is to initialize the client with username and password in plaintext. You are basically logging in to Infobip, so an exception will be thrown if the username and/or password are incorrect. The next step is to prepare the message:

-   **sender address** - value which will appear in the FROM field on the destination cell phone
-   **address** - GSM number of the destination cell phone
-   **message** - the contents of the SMS message

Sender address may be any string composed of printable characters but will it be delivered as such depends on the settings of the destination [network operator](http://en.wikipedia.org/wiki/Mobile_network_operator). Therefore, our recommendation (but not a guarantee) is to use the [English](http://en.wikipedia.org/wiki/English_alphabet) [alphanumeric](http://en.wikipedia.org/wiki/Alphanumeric) character subset.

When you execute the send method it will return an object which is used to query the message delivery status. These are possible states of the message once you have sent it:

-   **DeliveredToTerminal** - the message has been delivered to the cell phone
-   **DeliveredToNetwork** - the message has been delivered to the cell phone network operator
-   **MessageWaiting** - message is pending delivery to the cell phone network operator
-   **DeliveryImpossible** - message will not be delivered
-   **DeliveryUncertain** - delivery cannot be confirmed due to network operator settings. It still may be delivered but we will never know it.

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

Infobip will send a HTTP POST request to this URL, and your web application must process it like this:

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

### Example 2.1: Cell phone roaming status (Number Context query)

When the cell phone is connected to a network other than his home operator network it is said to be [roaming](http://en.wikipedia.org/wiki/Roaming). This is just a part of the information about a cell phone that can be obtained via a [Number Context](http://www.infobip.com/messaging/end_users/number_context_packages) query like in the example below.

    <?php

    require_once(PATH_TO_LIBRARY);

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);

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

    ?>

### Example 2.2: Cell phone roaming status (Number Context query) as notification push

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

Infobip will send a HTTP POST request to this URL, and your web application must process it like this:

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

Two way communication with cell phone is also possible via Infobip. The messages your application sends to cell phones are outbound or mobile-terminated messages. It is a scenario much like in the first example. The messages which your application receives from cell phones are inbound or mobile-originated messages.

In order to be able to receive inbound messages programmatically you must have a valid GSM subscription number. For demo purposes, a valid 30-day trial GSM subscription number is tied to your Infobip account. Our paid services include (info coming soon, mail to [info@infobip.com](mailto:info@infobip.com)):

-   you may register a single subscription number
-   you may register a single subscription number paired-up with a keyword of your choice
-   you may register a single subscription number paired-up with multiple keywords oy our choice
-   you may register for all or some of the above as many times as you like

In order for the below example to work make sure that you have a subscription with no notify URL set at your [Infobip](https://www.infobip.com/) account.

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

In order for the below example to work make sure that you have a subscription with a notify URL set at your Infobip account. Of course, the notify URL must be mapped to your web application.

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
