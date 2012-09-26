<?php

/**
 * SMS request object.
 */
class SMSRequest extends AbstractObject {

	public $senderAddress;

	public $senderName;

	public $message;

    /** Alternative to message -- hex encoded binary body. */
    public $binary;

    /** null for default GSM7, or "UTF-16" */
    public $charset;

	public $address;

    /** Used later for querying about the message status. */
	public $clientCorrelator;

    /** 
     * If not empty -- this is the url where the delivery notification will be pushed. 
     *
     * If empty -- the delivery notification may be queried using the 
     * clientCorrelator string.
     */
	public $notifyURL;

    /** Artibtrary string that will be pushed if notifyURL is set. */
	public $callbackData;
    
    // TODO
    // public $dataCoding;

    public function __construct($array=null, $success=true) {
        parent::__construct($array, $success);
    }

}

Models::register('SMSRequest');

?>
