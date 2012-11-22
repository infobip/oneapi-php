Parseco OneAPI php client
============================

System overview
-----------------------

[OneAPI](http://OneAPI.gsma.com/) is an [API](http://en.wikipedia.org/wiki/Application_programming_interface) [specification](http://en.wikipedia.org/wiki/Specification_(technical_standard)) issued by the [GSM Association](http://en.wikipedia.org/wiki/GSMA) and it aims to expose many [mobile network](http://en.wikipedia.org/wiki/Mobile_network) functionalities such as: [SMS](http://oneapi.gsmworld.com/sms-restful-api/) ([as a term](http://en.wikipedia.org/wiki/Short_Message_Service)), [MMS](http://oneapi.gsmworld.com/mms-restful-api/) ([as a term](http://en.wikipedia.org/wiki/Multimedia_Messaging_Service)), [payments](http://oneapi.gsmworld.com/payment-restful-api/) ([as a term](http://en.wikipedia.org/wiki/Mobile_payment)), [voice call control](http://OneAPI.gsmworld.com/voice-call-control-restful-api/), [LBS](http://oneapi.gsmworld.com/reference-location-restful-api/) ([as a term](http://en.wikipedia.org/wiki/Location-based_service)), [data connection profile querying](http://OneAPI.gsma.com/data-connection-profile/) and [device capabilities querying](http://OneAPI.gsmworld.com/device-capability-restful-api/).
[Parseco](https://github.com/parseco/) is OneAPI [implementation](http://en.wikipedia.org/wiki/Implementation#Computer_Science) which exposes [SMS gateway](http://en.wikipedia.org/wiki/SMS_gateway) and [USSD gateway](http://en.wikipedia.org/wiki/USSD_Gateway) functionality.
In order to use Parseco OneAPI and gain access to our gateways you must [register](http://www.parseco.com/sign-up/) at the [Parseco website](http://www.parseco.com).
In other words, by using Parseco OneAPI php library you can [send SMS messages](http://www.parseco.com/#features-list) to **any** cell phone [around the globe](http://www.parseco.com/pricing-and-coverage/).
<br/>screenshot: system-landscape


Prerequisites
-----------------------

* You have [installed a PHP interpreter](http://php.net/manual/en/install.php).
* You have downloaded the [Parseco OneAPI PHP library](https://github.com/parseco/oneapi-php/tree/master/oneapi)


General assumptions which must be fulfilled for all the following examples
-----------------------

 * You must have an active Internet connection.
 * You have satisfied the prerequisites and [signed up](http://www.parseco.com/sign-up/) at [Parseco website](http://www.parseco.com). After sign-up, SMS message with the verification pin will be sent to your cell phone.

screenshot: account verifcation page screenshot<br/>
Input the four-digit PIN from the received SMS message in the verification box and press verify.
Congratulations on your successful registration - you can start using Parseco OneAPI! If you want, you can try out the [live demos](http://www.parseco.com/demos/) now.

Assumptions which must be fulfilled for the examples with notification push
-----------------------

In every example two different architectural approaches are shown.
In the first scenario the mobile-originated (see example 3 for term explanation) information is returned to the calling code.
In the second scenario the mobile-terminated information is still being sent by the calling code, but the mobile-originated information is returned to an URL predefined by the user.
In other words, Parseco pushes the receiving inbound notifications (be it HLR or delivery data, or messages) to your on-line machine.
Naturally, the notification generating code and the notification recieving code may be implemented in different languages but for the sake of this how-to document it will all be shown in php.

 * You must have your own web server accessible from the Internet in order to provide the well-defined URL for the Parseco push notifications.
 * You must register the URL mentioned above as notification URL at Parseco site [setup wizard](http://www.parseco.com/application/setup-wizard/)
 * It would be considered polite on your behalf to implement some sort of [polling](http://en.wikipedia.org/wiki/Polling_(computer_science)) mechanism in order not to waste our resources. Also, this conforms to the general [netiquette](http://en.wikipedia.org/wiki/Etiquette_(technology)) considering the use of network resources. Your inbound messages will be available for a period of 48 h after being received by our gateways.

For a given notification URL the [setup wizard](http://www.parseco.com/application/setup-wizard/) generates a pair of subscription number and keyword. The just generated subscription will be shown in the list below:
 
 * Id - The id of the subscription.
 * Address - Your GSM subscription number to which inbound messages are sent. **Prefix it with '+' prior to sending SMS message.**
 * Criteria	- String which **must** be present at the start of the SMS message text, otherwise Parseco won't forward it to your code.
 * Notify URL- The registered URL to receive Parseco push notifications
 * Action - Action for subscription.

screenshot subscriptions<br/>

The "Notify URL" field is crucial. 
If it is present, then the architectural approach with notification push is chosen, meaning that all your mobile-originated information will be sent to it. 
If it is not present then the architectural approach without notification push is chosen, meaning that all your mobile-originated information will be returned to the calling code.
If you make changes, a "Save" button will appear in the "Action" column. If you want to apply the changes, press it.


Notice
-----------------------

 * **After signup you won't be able to use any of our services for 2 to 5 minutes until the system propagates the changes.**
 * **Until you make your first payment the only GSM number to which you can send messages is the one tied to your Parseco account. It is meant for demo purposes only, so you have a 5 &euro; budget for testing, which roughly translates to 500 or less SMS messages, [depending upon your location](http://www.parseco.com/pricing-and-coverage/).**
 * All examples are [valid, runnable](http://sscce.org/) code snippets, you can copy them to a new empty php file, and replace the PATH_TO_LIBRARY with a string containing the path to the downloaded OneAPI library. There may be other strings to replace as noted in example 1.1. After you have done that, you can run them.

Example 1.1: Basic messaging (Hello world)
-----------------------
The first thing that needs to be done is to initialize the client with username and password in plaintext.
You are basically logging in to Parseco, so an exception will be thrown if the username and/or password are incorrect. The next step is to prepare the message:

 * sender address - value which will appear in the FROM field on the destination cell phone
 * address - GSM number of the destination cell phone
 * message - the contents of the SMS message

Sender address may be any string composed of printable characters but will it be delivered as such depends on the settings of the destination [network operator](http://en.wikipedia.org/wiki/Mobile_network_operator).
It that sense, it is recommended (but not guaranteed) to use the [English](http://en.wikipedia.org/wiki/English_alphabet) [alphanumeric](http://en.wikipedia.org/wiki/Alphanumeric) character subset.

Now you are ready to send the message.
When you execute the send method it will return an object which is used to query the message delivery status.
These are possible states of the message once you have sent it to [Infobip](http://www.infobip.com) SMS gateway:  
 
 * DeliveredToTerminal - the message has been delivered to the cell phone
 * DeliveredToNetwork - the message has been delivered to the cell phone network operator
 * MessageWaiting - message is pending delivery to the cell phone network operator
 * DeliveryImpossible - message will not be delivered
 * DeliveryUncertain - delivery cannot be confirmed due to network operator settings. It still may be delivered but we will never know it.

_

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


Example 1.2: Basic messaging (Hello world) with notification push
-----------------------

Set the notify URL when sending message, and make sure it is registered under some subscription at your [Parseco account](http://www.parseco.com/application/setup-wizard/):

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


Process it on the machine mapped to the notify URL:

    <?php

    require_once(PATH_TO_LIBRARY);

    $result = SmsClient::unserializeDeliveryStatus();
    echo 'status: ', $result->deliveryInfo->deliveryStatus , '\n' ;
    echo 'address: ', $result->deliveryInfo->address, '\n';
    echo 'messageId: ', $result->deliveryInfo->messageId, '\n';
    echo 'clientCorrelator: '+$result->deliveryInfo->clientCorrelator, '\n';
    echo 'callback data: ', $result->callbackData, '\n';

    ?>


Note that there is nothing stopping you from running both code snippets on the same machine, but it is not necessary.

Example 2.1: Cell phone roaming status (HLR query)
-----------------------
When the cell phone is connected to a network other than his home operator network it is said to be [roaming](http://en.wikipedia.org/wiki/Roaming).
This is just a part of the information about a cell phone that can be obtained via a [HLR](http://en.wikipedia.org/wiki/Network_switching_subsystem#Home_location_register_.28HLR.29) query like in the example below.

    <?php

    require_once(PATH_TO_LIBRARY);

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);
    $client->login();

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS);
    echo 'HLR result: \n';
    echo 'servingMccMnc: ', $response->servingMccMnc,'\n';
    echo 'address: ', $response->address,'\n';
    echo 'currentRoaming: ', $response->currentRoaming,'\n';
    echo 'resourceURL: ', $response->resourceURL,'\n';
    echo 'retrievalStatus: ', $response->retrievalStatus,'\n';
    echo 'callbackData: ', $response->callbackData,'\n';
    echo 'extendedData: ', $response->extendedData,'\n';

    ?>


Example 2.2: Cell phone roaming status (HLR query) as notification push
-----------------------

Set the notify URL when sending message, and make sure it is registered under some subscription at your [Parseco account](http://www.parseco.com/application/setup-wizard/):

    <?php

    require_once(PATH_TO_LIBRARY);

    $client = new DataConnectionProfileClient(USERNAME, PASSWORD);
    $client->login();

    $response = $client->retrieveRoamingStatus(DESTINATION_ADDRESS, NOTIFY_URL);
    // if there is no error the query has been succesfully executed
    if(!$response->isSuccess()) {
        echo 'Error:', $response->exception, "\n";
        Logs::printLogs();
    }

    ?>


Process it on the machine mapped to the notify URL:

    <?php

    require_once(PATH_TO_LIBRARY);

    $result = DataConnectionProfileClient::unserializeRoamingStatus();
    echo 'HLR result: \n';
    echo 'servingMccMnc: ', $response->servingMccMnc,'\n';
    echo 'address: ', $response->address,'\n';
    echo 'currentRoaming: ', $response->currentRoaming,'\n';
    echo 'resourceURL: ', $response->resourceURL,'\n';
    echo 'retrievalStatus: ', $response->retrievalStatus,'\n';
    echo 'callbackData: ', $response->callbackData,'\n';
    echo 'extendedData: ', $response->extendedData,'\n';

    ?>


Note that there is nothing stopping you from running both code snippets on the same machine, but it is not necessary.

Example 3.1: Process inbound messages (two way communication)
-----------------------
Two way communication with cell phone is also possible via OneAPI.
The messages your application sends to cell phones are outbound or mobile-terminated messages.
It is a scenario much like in the first example.
The messages which your application receives from cell phones are inbound or mobile-originated messages.
In order to be able to recieve inbound messages programmatically you must have a valid GSM subscription number.
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


Example 3.2: Process inbound messages (two way communication) as notification push
-----------------------

In order for the below example to work make sure that you have a subscription with a notify URL set at your [Parseco account](http://www.parseco.com/application/setup-wizard/). 
Of course, the notify URL must point at the below code.

    <?php

    require_once(PATH_TO_LIBRARY);

    $inboundMessages = SmsClient::unserializeInboundMessages();
    // returns a single message not array of messages
    echo $inboundMessages->dateTime;
    echo $inboundMessages->destinationAddress;
    echo $inboundMessages->messageId;
    echo $inboundMessages->message;
    echo $inboundMessages->resourceURL;
    echo $inboundMessages->senderAddress;

    ?>

