<?php

namespace infobip\models;
use infobip\Models;
use infobip\ObjectArrayConversionRule;

/**
 * Description of Encodings
 *
 * @author rbelusic
 */
class Encodings extends AbstractObject {

    public $encodings;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register(
        'infobip\models\Encodings',
        new ObjectArrayConversionRule('infobip\models\Encoding', 'encodings')
);
        


?>
