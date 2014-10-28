<?php

class IamException extends AbstractObject {

    public $requestError;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('IamException', array (
  new SubObjectConversionRule('IamRequestError', 'requestError')
));

?>
