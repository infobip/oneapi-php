<?php

namespace infobip\models;
use infobip\Models;
use infobip\SubObjectConversionRule;

/**
 * Social Invite response object.
 */
class SocialInviteResponse extends AbstractObject {

  public $sendSmsResponse;

  public function __construct($array=null, $success=true) {
      parent::__construct($array, $success);
  }
}

Models::register('infobip\models\SocialInviteResponse', array (
  new SubObjectConversionRule('infobip\models\SISendSmsResponse', 'sendSmsResponse')
));

?>
