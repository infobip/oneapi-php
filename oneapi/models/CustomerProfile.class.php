<?php

class CustomerProfile extends AbstractObject {
    public $id;
    public $username;
    public $forename;
    public $surname;
    public $street;
    public $city;
    public $zipCode;
    public $telephone;
    public $gsm;
    public $fax;
    public $email;
    public $msn;
    public $skype;
    public $countryId;
    public $timezoneId;
    public $primaryLanguageId;
    public $secondaryLanguageId;
    
    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}
	
Models::register('CustomerProfile');	

?>
