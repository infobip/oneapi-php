<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:27 PM
 */

namespace infobip;


use infobip\models\Countries;

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
