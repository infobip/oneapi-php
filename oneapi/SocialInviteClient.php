<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:29 PM
 */

namespace infobip;

use stdClass;

class SocialInviteClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    private function getOrCreateSenderId($sender) {
        if ($sender)
            return $sender;

        return 'InfoSMS';
    }

    /**
     * Send social invitation
     */
    public function sendInvite($socialInviteRequest, $socialInviteAppSecret) {
        $restUrl = $this->getRestUrl('/1/social-invite/invitation');

        $sender = $this->getOrCreateSenderId($socialInviteRequest->senderAddress);

        if(is_string($socialInviteRequest->recipients)) {
            $temp = explode(',', $socialInviteRequest->recipients);
            unset($socialInviteRequest->recipients);
            for ($i = 0; $i < count($temp); $i++) {
                $socialInviteRequest->recipients->destinations[$i] = new stdClass();
                $socialInviteRequest->recipients->destinations[$i]->address = $temp[$i];
            }
        }

        $params = array(
            'messageKey' => $socialInviteRequest->messageKey,
            'sender' => $sender,
            'recipients' => $socialInviteRequest->recipients
        );

        list($isSuccess, $content) = $this->executePOST(
            $restUrl, $params, 'application/json', $socialInviteAppSecret
        );

        return $this->createFromJSON('infobip\models\SocialInviteResponse', $content, !$isSuccess);
    }
}
