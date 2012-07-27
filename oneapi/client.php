<?

require_once 'oneapi/models.php';
require_once 'oneapi/object.php';
require_once 'oneapi/Utils.class.php';
require_once 'oneapi/Logs.class.php';

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

    public static $DEFAULT_BASE_URL = 'http://api.parseco.com';

    public $smsAuthentication = null;

    public function __construct($username = null, $password = null, $baseUrl = null) {

        if(!$username)
            $username = OneApiConfigurator::getUsername();
        if(!$password)
            $password = OneApiConfigurator::getPassword();

        $this->smsAuthentication = new SmsAuthentication(
                        Array('username' => $username, 'password' => $password)
        );
        $this->baseUrl = $baseUrl ? $baseUrl : self::$DEFAULT_BASE_URL;

        if ($this->baseUrl[strlen($this->baseUrl) - 1] != '/')
            $this->baseUrl .= '/';
    }

    // ----------------------------------------------------------------------------------------------------
    // Util methods:
    // ----------------------------------------------------------------------------------------------------

    protected function getClientCorrelator($clientCorrelator=null) {
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

    protected function executeRequest(
        $httpMethod, $url, $queryParams = null, $requestHeaders = null, 
        $contentType = "application/x-www-form-urlencoded; charset=utf-8"
    ) {
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
            CURLOPT_USERAGENT => 'infobip-api',
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_URL => $url,
        );

        Logs::debug('Executing ', $httpMethod, ' to ', $url);

        if (sizeof($queryParams) > 0 && ($httpMethod == 'POST' || $httpMethod == 'PUT')) {
            $httpBody = http_build_query($queryParams, null, '&');
            Logs::debug('Http body:', $httpBody);
            $opts[CURLOPT_POSTFIELDS] = $httpBody;
        }
        $opts[CURLOPT_HTTPHEADER] = $sendHeaders;

        $this->authenticateRequest($opts);

        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $isSuccess = 200 <= $code && $code < 300;

        curl_close($ch);

        Logs::debug('Response code ', $code);
        Logs::debug('isSuccess:', $isSuccess);
        Logs::debug('Result:', $result);

        return array($isSuccess, $result);
    }

    protected function authenticateRequest(&$curlOpts) {
        if ($this->smsAuthentication === null || !$this->smsAuthentication->isAuthenticated()) {
            return;
        }

        if ($this->smsAuthentication->authType === SmsAuthentication::AUTH_TYPE_BASIC) {
            Logs::debug('Authentication BASIC');
            $curlOpts[CURLOPT_USERPWD] =
                    $this->smsAuthentication->username . ':' . $this->smsAuthentication->password
            ;
        } else if ($this->smsAuthentication->authType === SmsAuthentication::AUTH_TYPE_IBSSO) {
            Logs::debug('Authentication IBSSO');
            $curlOpts[CURLOPT_HTTPHEADER][] =
                    'Authorization:' .
                    ' IBSSO ' . $this->smsAuthentication->ibssoToken
            ;
        }
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

}

class SmsClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    // ----------------------------------------------------------------------------------------------------
    // Rest methods:
    // ----------------------------------------------------------------------------------------------------    
    public function sendSMS($message) {
        $restPath = '/1/smsmessaging/outbound/{senderAddress}/requests';

        $clientCorrelator = $this->getClientCorrelator($message->clientCorrelator);

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

        $clientCorrelator = $this->getClientCorrelator($clientCorrelator);

        $params = array();
        if($clientCorrelator)
            $params['clientCorrelator'] = $clientCorrelator;

        list($isSuccess, $content) = $this->executeGET(
                $this->getRestUrl($restPath, $params)
        );

        return Conversions::createFromJSON('DeliveryInfoList', $content, !$isSuccess);
    }

    /* Test this one:
      public function sendHlr($hlr) {
      $clientCorrelator = $hlr->clientCorrelator;
      if(!$clientCorrelator)
      $clientCorrelator = randomAlphanumericString();

      $params = array(
      'address' => $hlr->address,
      'notifyURL' => $hlr->notifyURL,
      );

      if($hlr->notifyURL)
      $params['notifyURL'] = $hlr->notifyURL;

      list($isSuccess, $content) = $this->executeGET(
      '/1/terminalstatus/queries/roamingStatus',
      $params
      );

      print_r($content);

      return new HlrSendResult($content, $isSuccess);
      }
     */

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

        return Conversions::createFromJSON('InboundSmsMessages', $content, !$isSuccess);
    }

	/**
	 * Start subscribing to delivery status notifications over OneAPI for all your sent SMS  	                          
	 */
	public function subscribeToDeliveryStatusNotifications($subscribeToDeliveryNotificationsRequest) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/'.$subscribeToDeliveryNotificationsRequest->senderAddress.'/subscriptions');

        $clientCorrelator = $this->getClientCorrelator($subscribeToDeliveryNotificationsRequest->clientCorrelator);

        $params = array(
            'notifyURL' => $subscribeToDeliveryNotificationsRequest->notifyURL,
            'criteria' => $subscribeToDeliveryNotificationsRequest->criteria,
            'callbackData' => $subscribeToDeliveryNotificationsRequest->callbackData,
            'clientCorrelator' => $clientCorrelator,
        );

        list($isSuccess, $content) = $this->executePOST($restUrl, $params);

        return Conversions::createFromJSON('DeliveryReceiptSubscription', $content, !$isSuccess);
    }

	/**
	 * Stop subscribing to delivery status notifications for all your sent SMS  
	 * @param subscriptionId (mandatory) contains the subscriptionId of a previously created SMS delivery receipt subscription
	 */
	public function cancelDeliveryNotificationsSubscription($subscriptionId) {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions/' . $subscriptionId);

        list($isSuccess, $content) = $this->executeDELETE($restUrl);

        return Conversions::createFromJSON('GenericObject', null, !$isSuccess);
    }

	/**
	 * Retrieve delivery notifications subscriptions by for the current user
	 * @return DeliveryReportSubscription[]
	 */
	public function retrieveDeliveryNotificationsSubscriptions() {
        $restUrl = $this->getRestUrl('/1/smsmessaging/outbound/subscriptions');

        list($isSuccess, $content) = $this->executeGET($restUrl);

        return Conversions::createFromJSON('DeliveryReceiptSubscriptions', $content, !$isSuccess);
    }

}

class DataConnectionProfileClient extends AbstractOneApiClient {
	
	/**
	 * Retrieve asynchronously the customer’s roaming status for a single network-connected mobile device  (HLR)
	 * @param address (mandatory) mobile device number being queried
	 * @param notifyURL (mandatory) URL to receive roaming status asynchronously
	 * @return MessageStatus
	 */
    // TODO(TK) notifyURL
	public function retrieveRoamingStatusAsync($address, $notifyURL) {
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
            return Conversions::createFromJSON('GenericObject', null, !$isSuccess);
        else
            return null; // TODO(TK)
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

class CustomerProfilerClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    // TODO(TK)
    public function login($username, $password) {
        $restPath = '/1/customerProfile/login';

        // reset current auth
        $this->smsAuthentication = null;

        list($isSuccess, $content) = $this->executePOST(
                $this->getRestUrl($restPath), Array(
                    'username' => $username,
                    'password' => $password
                )
        );
        $this->smsAuthentication = new SmsAuthentication($content, $isSuccess);

        return $this->smsAuthentication;
    }

    // TODO(TK)
    public function logout() {
        $restPath = '/1/customerProfile/logout';


        list($isSuccess, $content) = $this->executePOST($this->getRestUrl($restPath));
        $this->smsAuthentication = new SmsAuthentication(Array(), $isSuccess);
        
        return $this->smsAuthentication;
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
            $this->smsAuthentication->verified = true;
        }
        return $this->smsAuthentication;
    }
   
    
    // TODO(TK)
    public function signup($customerProfile, $password, $captchaId, $captchaAnswer) {
        $restPath = '/1/customerProfile/signup';

        list($isSuccess, $content) = $this->executePOST(
                $this->getRestUrl($restPath), Array(
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
                )
        );

        $this->smsAuthentication = new SmsAuthentication($content, $isSuccess);
        return $this->smsAuthentication;
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
    // TODO(TK)
    public function getCustomerProfile($id = null) {
        $restUrl = $this->getRestUrl(
                $id == null ? '/1/customerProfile' : '/1/customerProfile/{id}', Array('id' => $id)
        );
        list($isSuccess, $content) = $this->executeGET($restUrl);

        return new CustomerProfile($content, $isSuccess);
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
