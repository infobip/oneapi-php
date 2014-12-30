<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:29 PM
 */

namespace infobip;


use infobip\models\Encodings;

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
