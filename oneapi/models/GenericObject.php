<?php

namespace infobip\models;
use infobip\Models;

/** Generic object to be used for API methods that return without body. */
class GenericObject extends AbstractObject {

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('infobip\models\GenericObject');
?>
