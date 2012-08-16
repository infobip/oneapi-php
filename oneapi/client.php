<?php

function __oneapi_autoloader($class) {
    $paths = array('oneapi/models', 'oneapi/core', 'oneapi/utils');
    foreach($paths as $path) {
        $fileName = $path . '/' . $class . '.class.php';
        if(is_file($fileName))
            require_once $fileName;
    }
}

spl_autoload_register('__oneapi_autoloader');

//require_once 'yapd/dbg.php';

require_once 'oneapi/object.php';

/**
 * Utility handler class to store username/password.
 */
class OneApiConfigurator {

    private static $username;
    private static $password;

    public static function setCredentials($username, $password) {
        self::$username = $username;
        self::$password = $password;
    }

    public static function getUsername() {
        return self::$username;
    }

    public static function getPassword() {
        return self::$password;
    }

}

class AbstractOneApiClient {

    const VERSION = '0.01';

    public static $DEFAULT_BASE_URL = 'https://api.parseco.com';

    public $oneApiAuthentication = null;

    private $username;
    private $password;

    public $throwException = true;

    public function __construct($username = null, $password = null, $baseUrl = null) {
        if(!$username)
            $username = OneApiConfigurator::getUsername();
        if(!$password)
            $password = OneApiConfigurator::getPassword();

        $this->username = $username;
        $this->password = $password;

        $this->baseUrl = $baseUrl ? $baseUrl : self::$DEFAULT_BASE_URL;

        if ($this->baseUrl[strlen($this->baseUrl) - 1] != '/')
            $this->baseUrl .= '/';

        # If true -- an exception will be thrown on error, otherwise, you have 
        # to check the is_success and exception methods on resulting objects.
        $this->throwException = true;
    }

    public function login() {
        $restPath = '/1/customerProfile/login';

        list($isSuccess, $content) = $this->executePOST(
                $this->getRestUrl($restPath), Array(
                    'username' => $this->username,
                    'password' => $this->password
                )
        );

        return $this->fillOneApiAuthentication($content, $isSuccess);
    }

    protected function fillOneApiAuthentication($content, $isSuccess) {
        $this->oneApiAuthentication = Conversions::createFromJSON('OneApiAuthentication', $content, !$isSuccess);
        $this->oneApiAuthentication->username = $this->username;
        $this->oneApiAuthentication->password = $this->password;
        $this->oneApiAuthentication->authenticated = @strlen($this->oneApiAuthentication->ibssoToken) > 0;
        return $this->oneApiAuthentication;
    }

    // ----------------------------------------------------------------------------------------------------
    // Util methods:
    // ----------------------------------------------------------------------------------------------------

    /**
     * Check if the authorization (username/password) is valid.
     */
    public function isValid() {
        $restUrl = $this->getRestUrl('/1/customerProfile');

        list($isSuccess, $content) = $this->executeGET($restUrl);

        return (boolean) $isSuccess;
    }

    protected function getOrCreateClientCorrelator($clientCorrelator=null) {
        if($clientCorrelator)
            return $clientCorrelator;

        return Utils::randomAlphanumericString();
    }

    protected function executeGET($restPath, $params = null) {
        list($isSuccess, $result) = $this->executeRequest('GET', $restPath, $params);

        return array($isSuccess, json_decode($result, true));
    }

    protected function executePOST($restPath, $params = null) {
        list($isSuccess, $result) = $this->executeRequest('POST', $restPath, $params);

        return array($isSuccess, json_decode($result, true));
    }

    protected function executePUT($restPath, $params = null) {
        list($isSuccess, $result) = $this->executeRequest('PUT', $restPath, $params);

        return array($isSuccess, json_decode($result, true));
    }

    protected function executeDELETE($restPath, $params = null) {
        list($isSuccess, $result) = $this->executeRequest('DELETE', $restPath, $params);

        return array($isSuccess, json_decode($result, true));
    }

    private function executeRequest(
        $httpMethod, $url, $queryParams = null, $requestHeaders = null, 
        $contentType = "application/x-www-form-urlencoded; charset=utf-8")
    {
        if ($queryParams == null)
            $queryParams = Array();
        if ($requestHeaders == null)
            $requestHeaders = Array();

        $sendHeaders = Array(
            'Content-Type: ' . $contentType
        );
        foreach ($requestHeaders as $key => $value) {
            $sendHeaders[] = $key . ': ' . $value;
        }

        if($this->oneApiAuthentication && $this->oneApiAuthentication->ibssoToken)
            $sendHeaders[] = 'Authorization: IBSSO ' . $this->oneApiAuthentication->ibssoToken;

        if($httpMethod === 'GET') {
            if(sizeof($queryParams) > 0)
                $url .= '?' . http_build_query($queryParams, null, '&');
        }

        $opts = array(
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => 'OneApi-php-' . self::VERSION,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $sendHeaders,
        );

        Logs::debug('Executing ', $httpMethod, ' to ', $url);

        if (sizeof($queryParams) > 0 && ($httpMethod == 'POST' || $httpMethod == 'PUT')) {
            $httpBody = http_build_query($queryParams, null, '&');
            Logs::debug('Http body:', $httpBody);
            $opts[CURLOPT_POSTFIELDS] = $httpBody;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $opts);

        $result = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if(curl_errno($ch) != 0)
            throw new Exception(curl_error($ch));

        $isSuccess = 200 <= $code && $code < 300;

        curl_close($ch);

        Logs::debug('Response code ', $code);
        Logs::debug('isSuccess:', $isSuccess);
        Logs::debug('Result:', $result);

        return array($isSuccess, $result);
    }

    protected function getRestUrl($restPath = null, $vars = null) {
        $rurl = $this->baseUrl;
        if ($restPath && $restPath !== '') {
            $rurl .= substr($restPath, 0, 1) === '/' ?
                    substr($restPath, 1) : $restPath
            ;
        }

        return $this->applyTemplate($rurl, $vars);
    }

    // escape string
    protected function strEscape($str) {
        $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
        return str_replace($search, $replace, $str);
    }

    // apply bind variables to template
    protected function applyTemplate($str, $params = NULL, $escapeFields = FALSE) {
        if (!$params)
            return($str);

        $rez = $str;

        foreach ($params as $nam => $val) {
            if ($val !== NULL) {
                $valn = $vals = $escapeFields ? $this->strEscape($val) : $val;
            } else {
                $vals = '';
                $valn = 'null';
            }

            $rez = str_replace("'{" . $nam . "}'", "'" . $vals . "'", $rez);
            $rez = str_replace("{" . $nam . "}", $valn, $rez);
        }
        return($rez);
    }

    protected function createFromJSON($className, $json, $isError) {
        $result = Conversions::createFromJSON($className, $json, $isError);

        if($this->throwException && !$result->isSuccess()) {
            $message = $result->exception->messageId . ': ' . $result->exception->text . ' [' . implode(',', $result->exception->variables) . ']';
            throw new Exception($message);
        }

        return $result;
    }

}

class SmsClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    // ----------------------------------------------------------------------------------------------------
    // Static methods used for http push events from the server:
    // ----------------------------------------------------------------------------------------------------    

    public static function unserializeDeliveryStatus($json) {
        if($json === null)
            $json = file_get_contents("php://input");

        return Conversions::createFromJSON('DeliveryInfo', @$json['deliveryInfoNotification']);
    }

    public static function unserializeRoamingStatus($json=null) {
        if($json === null)
            $json = file_get_contents("php://input");

        return Conversions::createFromJSON('TerminalRoamingStatusList', $requestBody);
    }

    public static function unserializeInboundMessage($json=null) {
        if($json === null)
            $json = file_get_contents("php://input");

        $inboundMessages = Conversions::createFromJSON('InboundSmsMessages', $json);
    }

    // ----------------------------------------------------------------------------------------------------
    // Rest methods:
    // ----------------------------------------------------------------------------------------------------    

    public function sendSMS($message) {
        $restPath = '/1/smsmessaging/outbound/{senderAddress}/requests';

        $clientCorrelator = $this->getOrCreateClientCorrelator($message->clientCorrelator);

        $params = array(
            'senderAddress' => $message->senderAddress,
            'address' => $message->address,
            'message' => $message->message,
            'clientCorrelator' => $clientCorrelator,
            'senderName' => 'tel:' . $message->senderAddress,
        );

        if ($message->notifyURL)
            $params['notifyUrl'] = $notifyUrl;
        if ($message->callbackData)
            $params['callbackData'] = $callbackData;

        list($isSuccess, $content) = $this->executePOST(
                $this->getRestUrl($restPath, Array('senderAddress' => $message->senderAddress)), $params
        );

        return new ResourceReference($content, $isSuccess);
    }

    /**
     * Check for delivery status of a message. If no 
     * $clientCorrelatorOrResourceReference is given -- get the list of all pending 
     * delivery statuses.
     */
    public function queryDeliveryStatus($clientCorrelatorOrResourceReference = null) {
        $restPath = '/1/smsmessaging/outbound/' . 'TODO' . '/requests/{clientCorrelator}/deliveryInfos';

        if (is_object($clientCorrelatorOrResourceReference))
            $clientCorrelator = $clientCorrelatorOrResourceReference->clientCorrelator;
        else
            $clientCorrelator = $clientCorrelator;

        $clientCorrelator = $this->getOrCreateClientCorrelator($clientCorrelator);

        $params = array();
        if($clientCorrelator)
            $params['clientCorrelator'] = $clientCorrelator;

        list($isSuccess, $content) = $this->executeGET(
                $this->getRestUrl($restPath, $params)
        );

        return $this->createFromJSON('DeliveryInfoList', $content, !$isSuccess);
    }

    /**
     * Get the list of mobile originated subscriptions for the current user.
     */
    public function retrieveInboundMessagesSubscriptions() {
        $restUrl = $this->getRestUrl('/1/smsmessaging/inbound/subscriptions');
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return new MoSubscriptions($content, $isSuccess);
    }

    /**
     * Create new inbound messages subscription.
     */
    public function subscribeToInboundMessagesNotifications($moSubscription) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/inbound/subscriptions');

        $params = Conversions::convertToJSON($moSubscription);

        list($isSuccess, $content) = $this->executePOST($restUrl, $params);

        // TODO(TK) clientCorrelator !!!

        return new GenericObject($content, $isSuccess);
    }

    /**
     * Delete inbound messages subscription.
     */
    // TODO(TK)
    public function cancelInboundMessagesSubscription($subscriptionId) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions/' . $subscriptionId);
        list($isSuccess, $content) = $this->executeDELETE($restUrl);

        return new GenericObject($content, $isSuccess);
    }

    public function retrieveInboundMessages($maxNumberOfMessages=null){
        $restUrl = $this->getRestUrl('/1/smsmessaging/inbound/registrations/INBOUND/messages');

        if(! $maxNumberOfMessages)
            $maxNumberOfMessages = 100;

        if($maxNumberOfMessages < 0)
            $maxNumberOfMessages = -1 * $maxNumberOfMessages;

        $params = array('maxBatchSize' => $maxNumberOfMessages);

        list($isSuccess, $content) = $this->executeGET($restUrl, $params);

        return $this->createFromJSON('InboundSmsMessages', $content, !$isSuccess);
    }

	/**
	 * Start subscribing to delivery status notifications over OneAPI for all your sent SMS  	                          
	 */
	public function subscribeToDeliveryStatusNotifications($subscribeToDeliveryNotificationsRequest) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/'.$subscribeToDeliveryNotificationsRequest->senderAddress.'/subscriptions');

        $clientCorrelator = $this->getOrCreateClientCorrelator($subscribeToDeliveryNotificationsRequest->clientCorrelator);

        $params = array(
            'notifyURL' => $subscribeToDeliveryNotificationsRequest->notifyURL,
            'criteria' => $subscribeToDeliveryNotificationsRequest->criteria,
            'callbackData' => $subscribeToDeliveryNotificationsRequest->callbackData,
            'clientCorrelator' => $clientCorrelator,
        );

        list($isSuccess, $content) = $this->executePOST($restUrl, $params);

        return $this->createFromJSON('DeliveryReportSubscription', $content, !$isSuccess);
    }

	/**
	 * Stop subscribing to delivery status notifications for all your sent SMS  
	 * @param subscriptionId (mandatory) contains the subscriptionId of a previously created SMS delivery report subscription
	 */
	public function cancelDeliveryNotificationsSubscription($subscriptionId) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions/' . $subscriptionId);

        list($isSuccess, $content) = $this->executeDELETE($restUrl);

        return $this->createFromJSON('GenericObject', null, !$isSuccess);
    }

	/**
	 * Retrieve delivery notifications subscriptions by for the current user
	 * @return DeliveryReportSubscription[]
	 */
	public function retrieveDeliveryNotificationsSubscriptions() {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions');

        list($isSuccess, $content) = $this->executeGET($restUrl);

        return $this->createFromJSON('DeliveryReportSubscription', $content, !$isSuccess);
    }

}

class DataConnectionProfileClient extends AbstractOneApiClient {
	
	/**
	 * Retrieve asynchronously the customer’s roaming status for a single network-connected mobile device  (HLR)
	 */
	public function retrieveRoamingStatus($address, $notifyURL=null) {
        $restUrl = $this->getRestUrl('/1/terminalstatus/queries/roamingStatus');

        $params = array(
			'address' => $address,
        );

        // TODO(TK) Add these parameters when ready:
        if(false)
            $params['includeExtendedData'] = true;
        if(false)
			$params['clientCorrelator'] = true;
        if(false)
			$params['callbackData'] = true;

        if($notifyURL)
			$params['notifyURL'] = $notifyURL;

        list($isSuccess, $content) = $this->executeGET($restUrl, $params);

        if($notifyURL)
            return $this->createFromJSON('GenericObject', null, !$isSuccess);
        else
            return $this->createFromJSON('TerminalRoamingStatus', $content['roaming'], !$isSuccess);
    }

}

class CaptchaClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    /**
     * Get captcha
     */
    // TODO(TK)
    public function getCaptcha($width=200,$height=50,$imageFormat="png") {
        $restUrl = $this->getRestUrl('/1/captcha/generate',Array(
            'width' => $width,
            'height' => $height,
            'imageFormat' => $imageFormat
        ));
        list($isSuccess, $content) = $this->executePOST($restUrl);

        return new Captcha($content, $isSuccess);
    }

}

class CountryClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    /**
     * Get list of all countries or one with given country id 
     */
    // TODO(TK)
    public function getCountries($id = null) {
        $restUrl = $this->getRestUrl(
                $id == null ? '/1/countries' : '/1/countries/{id}', Array('id' => $id)
        );
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return new Countries($content, $isSuccess);
    }

}

class CustomerProfileClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    public function getAccountBalance() {
        $restPath = $this->getRestUrl('/1/customerProfile/balance');

        list($isSuccess, $content) = $this->executeGET($restPath);
        
        return $this->createFromJSON('AccountBalance', $content, !$isSuccess);
    }

    public function logout() {
        $restPath = '/1/customerProfile/logout';

        list($isSuccess, $content) = $this->executePOST($this->getRestUrl($restPath));
        $this->oneApiAuthentication = null;
        
        return $isSuccess;
    }

    // TODO(TK)
    public function verifyUser($verificationCode='') {
        $restPath = '/1/customerProfile/verify';

        // reset current auth
        list($isSuccess, $content) = $this->executePOST(
                $this->getRestUrl($restPath), Array(
                    'verificationCode' => $verificationCode
                )
        );
        if(!$isSuccess) {
            return new SmsAuthentication($content, $isSuccess);
        } else {
            $this->oneApiAuthentication->verified = true;
        }
        return $this->oneApiAuthentication;
    }
   
    
    // TODO(TK)
    public function signup($customerProfile, $password, $captchaId, $captchaAnswer) {
        $restPath = '/1/customerProfile/signup';

        $params = array(
            'username' => $customerProfile->username,
            'forename' => $customerProfile->forename,
            'surname' => $customerProfile->surname,
            'email' => $customerProfile->email,
            'gsm' => $customerProfile->gsm,
            'countryCode' => $customerProfile->countryCode,
            'timezoneId' => $customerProfile->timezoneId,
            // 
            'password' => $password,
            'captchaId' => $captchaId,
            'captchaAnswer' => $captchaAnswer
        );

        list($isSuccess, $content) = $this->executePOST(
                $this->getRestUrl($restPath), 
                $params
        );

        return $this->fillOneApiAuthentication($content, $isSuccess);
    }

    // TODO(TK)
    public function checkUsername($uname) {
        $restPath = '/1/customerProfile/username/check';

        list($isSuccess, $content) = $this->executeGET(
            $this->getRestUrl($restPath), Array(
                'username' => $uname
            )
        );

        return Utils::getArrayValue($content,'usernameCheck',false) == true;
    }
    
    /**
     * Get customer profile for the current user or user with given user id.
     */
    public function getCustomerProfile($id = null) {
        $restUrl = $this->getRestUrl(
                $id == null ? '/1/customerProfile' : '/1/customerProfile/{id}', Array('id' => $id)
        );
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return $this->createFromJSON('CustomerProfile', $content, !$isSuccess);
    }

    /**
     * Update customer profile.
     */
    // TODO(TK)
    public function updateCustomerProfile($customerProfile) {
        $restUrl = $this->getRestUrl('/1/customerProfile');
        list($isSuccess, $content) = $this->executeGET($restUrl, Array(
            'id' => $customerProfile->id,
            'username' => $customerProfile->username,
            'forename' => $customerProfile->forename,
            'surname' => $customerProfile->surname,
            'street' => $customerProfile->street,
            'city' => $customerProfile->city,
            'zipCode' => $customerProfile->zipCode,
            'telephone' => $customerProfile->telephone,
            'gsm' => $customerProfile->gsm,
            'fax' => $customerProfile->fax,
            'email' => $customerProfile->email,
            'msn' => $customerProfile->msn,
            'skype' => $customerProfile->skype,
            'countryId' => $customerProfile->countryId,
            'timezoneId' => $customerProfile->timezoneId,
            'primaryLanguageId' => $customerProfile->primaryLanguageId,
            'secondaryLanguageId' => $customerProfile->secondaryLanguageId
        ));

        return $isSuccess;
    }

}

class TimeZoneClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    /**
     * Get list of all timezones or one with given timezone id 
     */
    // TODO(TK)
    public function getTimezones($id = null) {
        $restUrl = $this->getRestUrl(
                $id == null ? '/1/timezones' : '/1/timezones/{id}', Array('id' => $id)
        );
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return new Timezones($content, $isSuccess);
    }

}

class EncodingClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    /**
     * Get list of all encodings
     */
    // TODO(TK)
    public function getEncodings($id = null) {
        $restUrl = $this->getRestUrl('/1/fileEncodings');
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return new Encodings($content, $isSuccess);
    }
    
}
