<?php

namespace infobip\models;


use infobip\Models;

class Encoding  extends AbstractObject {
    public $name;
    
    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}    
Models::register('infobip\models\Encoding');


?>
