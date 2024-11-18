<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Utility;

use \stdClass;

defined('_JEXEC') or die;

/**
 * Class ReCaptcha
 */
class ReCaptcha
{
    /**
     * Site verify url
     *
     * @var string
     */
    private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
    /**
     * Secret key
     *
     * @var string
     */
    private $_secret;

    /**
     * Constructor.
     *
     * @param string $secret shared secret between site and ReCAPTCHA server.
     */
    public function __construct($secret)
    {
        $this->_secret = $secret;
    }

    /**
     * Encodes the given data into a query string format.
     *
     * @param array $data array of string elements to be encoded.
     *
     * @return string - encoded request.
     */
    private function _encodeQS($data)
    {
        $req = "";
        foreach ($data as $key => $value) {
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }

        // Cut the last '&'
        $req = substr($req, 0, strlen($req)-1);
        return $req;
    }

    /**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $path url path to recaptcha server.
     * @param array  $data array of parameters to be sent.
     *
     * @return bool|string
     */
    private function _submitHTTPGet($path, $data)
    {
        $req = $this->_encodeQS($data);
        $url = $path . $req;
        if ($this->_isCurl()) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $url);
            $response = curl_exec($c);
            curl_close($c);
        } else {
            $response = file_get_contents($url);
        }
        return $response;
    }

    /**
     * Checks curl extension is enable
     *
     * @return bool
     */
    private function _isCurl() {
        return function_exists('curl_version');
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $response response string from recaptcha verification.
     *
     * @return stdClass
     */
    public function verifyResponse($response)
    {
        // Discard empty solution submissions
        if ($response == null || strlen($response) == 0) {
            $recaptchaResponse = new stdClass();
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = 'missing-input';
            return $recaptchaResponse;
        }

        $getResponse = $this->_submitHttpGet(
            self::$_siteVerifyUrl,
            array (
                'secret' => $this->_secret,
                'response' => $response
            )
        );
        $answers = json_decode($getResponse, true);
        $recaptchaResponse = new stdClass();

        if (trim($answers['success']) == true) {
            $recaptchaResponse->success = true;
        } else {
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = $answers['error-codes'];
        }

        return $recaptchaResponse;
    }
}

?>
