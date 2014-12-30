<?php
/**
 * Created by PhpStorm.
 * User: mmilivojevic
 * Date: 12/30/14
 * Time: 12:27 PM
 */

namespace infobip;

use infobip\models\Captcha;

class CaptchaClient extends AbstractOneApiClient {

    public function __construct($username = null, $password = null, $baseUrl = null) {
        parent::__construct($username, $password, $baseUrl);
    }

    /**
     * Get captcha
     */
    // TODO(TK)
    public function getCaptcha($width=200,$height=50,$imageFormat="png") {
        $restUrl = $this->getRestUrl('/1/captcha/generate',Array(
            'width' => $width,
            'height' => $height,
            'imageFormat' => $imageFormat
        ));
        list($isSuccess, $content) = $this->executePOST($restUrl);

        return new Captcha($content, $isSuccess);
    }

}
