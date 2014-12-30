<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:28 PM
 */

namespace infobip;

use infobip\models\Timezones;

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
