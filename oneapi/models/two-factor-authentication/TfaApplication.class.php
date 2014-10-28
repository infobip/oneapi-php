<?php

class TfaApplication extends AbstractObject {

    public $applicationId;
    public $name;
    public $enabled;
    public $processId;
    public $configuration;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('TfaApplication', array (
  new SubObjectConversionRule('TfaApplicationConfiguration', 'configuration')
));

?>
