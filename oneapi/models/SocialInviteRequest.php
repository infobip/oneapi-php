<?php

namespace infobip\models;
use infobip\Models;

/**
 * Social Invite request object.
 */
class SocialInviteRequest extends AbstractObject {

  public $senderAddress;

  public $recipients;

  public $messageKey;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\SocialInviteRequest');

?>
