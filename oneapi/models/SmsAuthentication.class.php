<?php

class SmsAuthentication extends AbstractObject {
    const AUTH_TYPE_BASIC = 0;
    const AUTH_TYPE_OAUTH = 1;
    const AUTH_TYPE_IBSSO = 2;

	public $authType = self::AUTH_TYPE_BASIC;	
    
	//BASIC Authentication parameters
	public $username = "";
	public $password = "";
	//OAUTH Authentication parameter 
	public $accessToken = "";
	//IBSSO Authentication parameter
	public $ibssoToken = "";
    
    // is user verified
    public $authenticated = false;
    public $verified = false;
    
    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

    public function isAuthenticated() {
        return $this->authenticated;
    }
    
    public function isVerified() {
        return $this->verified;
    }
    
    public function setBasicAuthentication($uname,$pass) {
        $this->authType = self::AUTH_TYPE_BASIC;	
        $this->username = $uname;
        $this->password = $pass;
        $this->authenticated = true;
        $this->verified = true;
        $this->ibssoToken = "";
    }
}

Models::register(
    'SmsAuthentication',
    new ObjectConversionRule(function($object, $json) {
        $data = Utils::getArrayValue($json,'login',Utils::getArrayValue($json,'signup',''));
        
        if(Utils::getArrayValue($data,'ibAuthCookie','') !== '') {
            $object->authType = SmsAuthentication::AUTH_TYPE_IBSSO;
            $object->username = '';
            $object->password = '';            
            $object->ibssoToken = Utils::getArrayValue($data,'ibAuthCookie','');
            $object->userVerified = Utils::getArrayValue($data,'verified','false') === 'true';            
            $object->accessToken = '';
            $object->authenticated = $object->ibssoToken !== '';
            
        } else if(Utils::getArrayValue($json,'username','') !== '') {
            $object->authType = SmsAuthentication::AUTH_TYPE_BASIC;
            $object->username = Utils::getArrayValue($json,'username','');
            $object->password = Utils::getArrayValue($json,'password','');
            $object->ibssoToken = '';
            $object->authenticated = true;
            $object->userVerified = true; // kako znamo da je verificiran? ne prolazi login proces 
        } else {
            $object->authenticated = false;
            $object->userVerified = false;            
        }
    })
);

?>
