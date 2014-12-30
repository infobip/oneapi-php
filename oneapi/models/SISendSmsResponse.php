<?php

namespace infobip\models;

// require_once('SISendMessageResult');
use infobip\Models;
use infobip\ObjectArrayConversionRule;

/**
 * Send sms response (Social Invite) object.
 */
class SISendSmsResponse extends AbstractObject {

  public $bulkId;
  public $deliveryInfoUrl;
  public $responses;

  public function __construct($array=null, $success=true) {
      parent::__construct($array, $success);
  }
}

Models::register('infobip\models\SISendSmsResponse', array (
  new ObjectArrayConversionRule('infobip\models\SISendMessageResult', 'responses')
));

?>
