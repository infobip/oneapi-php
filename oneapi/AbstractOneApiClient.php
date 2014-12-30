<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:22 PM
 */

namespace infobip;


use Exception;
use infobip\utils\Logs;
use infobip\utils\Utils;

if(!defined('ONEAPI_BASE_URL'))
    define('ONEAPI_BASE_URL', 'https://oneapi.infobip.com');

class AbstractOneApiClient {

    const VERSION = '0.0.3';

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

        $this->baseUrl = $baseUrl ? $baseUrl : ONEAPI_BASE_URL;

        if ($this->baseUrl[strlen($this->baseUrl) - 1] != '/')
            $this->baseUrl .= '/';

        # If true -- an exception will be thrown on error, otherwise, you have
        # to check the is_success and exception methods on resulting objects.
        $this->throwException = true;
    }

    public function setAPIurl($baseUrl=NULL) {
        $this->baseUrl = $baseUrl ? $baseUrl : ONEAPI_BASE_URL;
        if ($this->baseUrl[strlen($this->baseUrl) - 1] != '/')
            $this->baseUrl .= '/';
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
        $this->oneApiAuthentication = Conversions::createFromJSON('\infobip\models\OneApiAuthentication', $content, !$isSuccess);
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

    protected function executeGET($restPath, $params = null, $contentType = null, $apiKey = null) {
        if ($contentType != null && $apiKey != null) {
            list($isSuccess, $result) =
                $this->executeRequest('GET', $restPath, $params, null, $contentType, $apiKey);
        } else if ($contentType != null) {
            list($isSuccess, $result) = $this->executeRequest('GET', $restPath, $params, null, $contentType);
        } else {
            list($isSuccess, $result) = $this->executeRequest('GET', $restPath, $params);
        }

        return array($isSuccess, json_decode($result, true));
    }

    protected function executePOST($restPath, $params = null, $contentType = null, $apiKey = null) {
        if ($contentType != null && $apiKey != null) {
            list($isSuccess, $result) =
                $this->executeRequest('POST', $restPath, $params, null, $contentType, $apiKey);
        } else if($contentType != null){
            list($isSuccess, $result) = $this->executeRequest('POST', $restPath, $params, null, $contentType);
        } else {
            list($isSuccess, $result) = $this->executeRequest('POST', $restPath, $params);
        }

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

    /**
     * Like http_build_query but works for {'a': ['b', 'c']} the result is
     * a=b&a=c
     */
    private function buildQuery($array) {
        $result = '';
        foreach($array as $key => $value) {
            if($result)
                $result .= '&';
            if(is_array($value)) {
                foreach($value as $subValue) {
                    if($result)
                        $result .= '&';
                    $result .= urlencode($key) . '=' . urlencode($subValue);
                }
            } else {
                $result .= urlencode($key) . '=' . urlencode($value);
            }
        }
        return $result;
    }

    private function executeRequest(
        $httpMethod, $url, $queryParams = null, $requestHeaders = null,
        $contentType = "application/x-www-form-urlencoded", $specialAuth = null)
    {
        if ($queryParams == null)
            $queryParams = Array();
        if ($requestHeaders == null)
            $requestHeaders = Array();

        // Check if the charset is specified in the content-type:
        if(strpos($contentType, 'charset') === false) {
            $charset = OneApiConfigurator::getCharset();
            if(!$charset)
                $charset = 'utf-8';

            $contentType .= '; charset=' . $charset;
        }

        $sendHeaders = Array(
            'Content-Type: ' . $contentType
        );
        foreach ($requestHeaders as $key => $value) {
            $sendHeaders[] = $key . ': ' . $value;
        }

        if($httpMethod === 'GET') {
            if(sizeof($queryParams) > 0)
                $url .= '?' . $this->buildQuery($queryParams);
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
            CURLOPT_HTTPHEADER => $sendHeaders
        );

        if ($specialAuth) {
            $opts[CURLOPT_HTTPHEADER][] = 'Authorization: App ' . $specialAuth;
        } else {
            if ($this->oneApiAuthentication && $this->oneApiAuthentication->ibssoToken) {
                // Token based authentication (one request per login request):
                $opts[CURLOPT_HTTPHEADER][] = 'Authorization: IBSSO ' . $this->oneApiAuthentication->ibssoToken;
            } else {
                // Basic authorization:
                $opts[CURLOPT_USERPWD] = $this->username . ':' . $this->password;
            }
        }

        Logs::debug('Executing ', $httpMethod, ' to ', $url);

        if (sizeof($queryParams) > 0 && ($httpMethod == 'POST' || $httpMethod == 'PUT')) {
            $httpBody = null;

            if (strpos($contentType, 'x-www-form-urlencoded')) {
                $httpBody = $this->buildQuery($queryParams);
            } else if (strpos($contentType, 'json')) {
                $httpBody = json_encode($queryParams);
            }

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

            $rez = str_replace("'{" . $nam . "}'", "'" . urlencode($vals) . "'", $rez);
            $rez = str_replace("{" . $nam . "}", urlencode($valn), $rez);
        }
        return($rez);
    }

    protected function createFromJSON($className, $json, $isError) {
        $result = Conversions::createFromJSON($className, $json, $isError);

        if ($this->throwException && !$result->isSuccess()) {
            $message = $result->exception->messageId . ': ' . $result->exception->text . ' [' . implode(',', $result->exception->variables) . ']';
            throw new Exception($message);
        }

        if ('infobip\models\iam\IamException' == $className) {
            $message = json_encode($result->requestError);
            throw new Exception($message);
        }

        return $result;
    }

}
