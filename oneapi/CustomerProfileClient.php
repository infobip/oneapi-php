<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:28 PM
 */

namespace infobip;


use infobip\utils\Utils;

class CustomerProfileClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    public function getAccountBalance() {
        $restPath = $this->getRestUrl('/1/customerProfile/balance');

        list($isSuccess, $content) = $this->executeGET($restPath);

        return $this->createFromJSON('infobip\models\AccountBalance', $content, !$isSuccess);
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

        return $this->createFromJSON('infobip\models\CustomerProfile', $content, !$isSuccess);
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
