<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:26 PM
 */

namespace infobip;


class DataConnectionProfileClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    public static function unserializeRoamingStatus($json=null) {
        if($json === null)
            $json = file_get_contents("php://input");

        return Conversions::createFromJSON('infobip\models\TerminalRoamingStatusNotification', $json);
    }

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
            return $this->createFromJSON('infobip\models\GenericObject', null, !$isSuccess);
        else
            return $this->createFromJSON('infobip\models\TerminalRoamingStatus', $content['roaming'], !$isSuccess);
    }

}
