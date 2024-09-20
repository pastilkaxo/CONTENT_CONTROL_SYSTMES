<?php
/**
 * Plugin Name: Captcha Them All
 * Plugin URI: http://www.fuzzguard.com.au/plugins/captcha-them-all
 * Description: Provides Captcha display on login, registration, lost password and commenting pages in WordPress (Also works for WooCommerce)
 * Version: 1.4.2
 * Author: <a href="http://www.fuzzguard.com.au/"><strong>Benjamin Guy</strong></a>
 * Author URI: http://www.fuzzguard.com.au
 * Text Domain: captcha-them-all
 * WC requires at least: 3.0.0
 * WC tested up to: 8.4.0
 * License: GPL2

    Copyright 2017  Benjamin Guy  (email: support@fuzzguard.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


/**
* Don't display if wordpress admin class is not found
* Protects code if wordpress breaks
* @since 0.1
*/
if ( ! function_exists( 'is_admin' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

/**
* Option name used to store plugin configuration
* @since 0.1
**/
$CTA_opt_name = 'captcha-them-all';

/**
* DB name used to store failed login attempts by IP address
* @since 1.1
**/
$CTA_ipFailedAttTbl = 'CTA_ip_failed_atts';

/**
* Option name used by visualCaptcha for namespace designation
* @since 0.1
**/
$CTA_visualCaptcha_namespace = 'CTA_visualCaptcha';

/**
* Store version DB for updates
* @since 1.1
**/
$CTA_DB_version = '1.1.1';

class CaptchaThemAll {

        /**
        * Loads localization files for each language
        * @since 1.4
        */
        function _action_init()
        {
                // Localization
                load_plugin_textdomain('captcha-them-all', false, 'captcha-them-all/lang/');
        }

/**
* Function to Register scripts for use in the frontend
* @since 0.1
* @updated 1.3
*/
function register_scripts() {

        //global $version;
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );

	//Register all global scripts
	wp_register_style('cta-css', plugins_url().'/captcha-them-all/css/cta.css');

	//Register all per-captcha-type scripts
        switch ($opt_val['captcha-type']) {
                case 'google-recaptcha':
                        wp_register_script('captcha-them-all-google', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl='.get_locale(), '1.0.0', true);
                break;

		case 'visualCaptcha':
    			wp_enqueue_style( 'visualcaptcha', plugins_url( 'visualcaptcha/public/visualcaptcha.css', __FILE__), '5.0.6');
    			wp_enqueue_script( 'jquery' );
    			wp_enqueue_script( 'visualcaptcha.jquery', plugins_url( 'visualcaptcha/public/visualcaptcha.jquery.js', __FILE__ ), array( 'jquery' ), '5.0.6', true );
    			wp_enqueue_script( 'visualcaptcha.bootstrap', plugins_url( 'visualcaptcha/public/visualcaptcha.bootstrap.js', __FILE__ ), array( 'visualcaptcha.jquery' ), '5.0.6', true );
    			wp_localize_script(
        			'visualcaptcha.bootstrap',
        			'captchaParams',
        				array(
            					'imgPath' => plugins_url( 'visualcaptcha/public/img/', __FILE__ ),
						'url' => home_url(),
            					//'url' => plugins_url( 'visualcaptcha/app.php', __FILE__ ),
            					'language' => array(
                					'accessibilityAlt' => __( 'Sound icon', 'captcha-them-all' ),
                					'accessibilityTitle' => __( 'Accessibility option: listen to a question and answer it!', 'captcha-them-all' ),
                					'accessibilityDescription' => __( 'Type below the <strong>answer</strong> to what you hear. Numbers or words:', 'captcha-them-all' ),
                					'explanation' => __( 'Click or touch the <strong>ANSWER</strong>', 'captcha-them-all' ),
                					'refreshAlt' => __( 'Refresh/reload icon', 'captcha-them-all' ),
                					'refreshTitle' => __( 'Refresh/reload: get new images and accessibility option!', 'captcha-them-all' )
            					)
        				)
    			);
		break;		

                default;
                break;
        }
}


/**
* Gets the IP address of the remote client
* @since 1.1
*/
function clientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip =esc_sql($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = esc_sql($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            $ip =  esc_sql($_SERVER['REMOTE_ADDR']);
        }


        return $ip;
}

/**
* Clears DB table $CTA_ipFailedAttTbl with failed login for the remote client IP address
* Only do this if the user has set $opt_val['protected']['login_form'] as 'Y' and $opt_val['protected']['login_form_attempts'] > 0
* @since 1.1
*/
function clear_failed_login_attempts() {
        global $CTA_opt_name, $wpdb, $CTA_ipFailedAttTbl;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        if (ISSET($opt_val['protected']['login_form']) && $opt_val['protected']['login_form'] == 'Y' && (ISSET($opt_val['protected']['login_form_attempts']) && $opt_val['protected']['login_form_attempts'] > 0)) {
                $ip = $this->clientIP();
                $table = $wpdb->prefix.$CTA_ipFailedAttTbl;
                $ipAttempts = $wpdb->get_results( "SELECT `id`, `ip`,`attempts`,`last_attempt` FROM  `$table`   WHERE `ip` =  '$ip'  ORDER BY `id` DESC LIMIT 1 " );
                $wpdb->delete($table, array( 'id' =>  $ipAttempts[0]->id));
        }
return $user;
}

/**
* Clears DB table $CTA_ipFailedAttTbl for any entry older than 15 days (STATIC SET)
* Only do this if the user has set $opt_val['protected']['login_form'] as 'Y' and $opt_val['protected']['login_form_attempts'] > 0 (Check run in function count_failed_login_attempts)
* @since 1.1
*/
function clearDeadWood() {
        global $wpdb, $CTA_ipFailedAttTbl;
        $table = $wpdb->prefix.$CTA_ipFailedAttTbl;
        $currTime = date('Y-m-d G:i:s', strtotime('-15 days'));
        $wpdb->query( "DELETE FROM $table WHERE last_attempt < '$currTime'" );
}

/**
* Increments DB table $CTA_ipFailedAttTbl with failed login for the remote client IP address
* Only do this if the user has set $opt_val['protected']['login_form'] as 'Y' and $opt_val['protected']['login_form_attempts'] > 0
* @since 1.1
*/
function count_failed_login_attempts($username) {
        global $CTA_opt_name, $wpdb, $CTA_ipFailedAttTbl;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        if (ISSET($opt_val['protected']['login_form']) && $opt_val['protected']['login_form'] == 'Y' && (ISSET($opt_val['protected']['login_form_attempts']) && $opt_val['protected']['login_form_attempts'] > 0)) {
                $ip = $this->clientIP();
                $this->clearDeadWood();
                $table = $wpdb->prefix.$CTA_ipFailedAttTbl;
                $ipAttempts = $wpdb->get_results( "SELECT `id`, `ip`,`attempts`,`last_attempt` FROM  `$table`   WHERE `ip` =  '$ip'  ORDER BY `id` DESC LIMIT 1 " );
                if (count($ipAttempts)==1) {
                        $attempts = $ipAttempts[0]->attempts;
                        $attemptsAllowed = $opt_val['protected']['login_form_attempts'];
                        if( $attempts<$attemptsAllowed){
                                $attempts++;
                                $updateAttempts = array(
                                        'id' =>  $ipAttempts[0]->id ,
                                        'attempts'  =>   $attempts,
                                        'last_attempt' => date('Y-m-d G:i:s')
                                );
                                $wpdb->update($table,$updateAttempts,array('id'=>$ipAttempts[0]->id ) );
                        }
                } else {
                        $newAttempt = array(
                                'ip' => $ip,
                                'attempts' =>  1 , 
                                'last_attempt' => date('Y-m-d G:i:s')
                        );
                        $wpdb->insert($table, $newAttempt);
                }
        }
}


/**
* Return the securImage distortion level converted from 0-10 to 0.0-1.0
* @since 0.1
*/
function securImageDistortion() {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        return ($opt_val['securImage']['distortion']/10);
}

/**
* Load the Google ReCaptcha render JS function to display ReCaptcha on webpage. Sets sitekey, theme and type
* @since 0.1
*/
function googleRecaptchaInstanceJS() {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        $googleRecaptchaInstanceScript =  '<script type="text/javascript" charset="utf-8">

                var onloadCallback = function() {
                        var recaptchas = document.querySelectorAll(\'div[class=g-recaptcha]\');
                        for( i = 0; i < recaptchas.length; i++) {
                                grecaptcha.render( recaptchas[i].id, {
                                        \'sitekey\' : \''.$opt_val['google']['grecaptcha-sitekey'].'\',
                                        \'theme\' : \''.$opt_val['google']['grecaptcha-theme'].'\',
                                        \'type\' : \''.$opt_val['google']['grecaptcha-type'].'\',
                                });
                        }
                }
        </script>';

        return $googleRecaptchaInstanceScript;
}



/**
* Function to add the Captcha field to the Login Page
* @since 0.1
* @updated 1.1
*/
function CTA_add_captcha_login_fields() {
        global $CTA_opt_name, $CTA_ipFailedAttTbl, $wpdb;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        $table = $wpdb->prefix.$CTA_ipFailedAttTbl;
        $ip = $this->clientIP();
        $ipAttempts = $wpdb->get_results( "SELECT `id`, `ip`,`attempts`,`last_attempt` FROM  `$table`   WHERE `ip` =  '$ip'  ORDER BY `id` DESC LIMIT 1 " );
        if (ISSET($opt_val['protected']['login_form']) && $opt_val['protected']['login_form'] == 'Y' && (!ISSET($opt_val['protected']['login_form_attempts']) || $opt_val['protected']['login_form_attempts']==0)) {
                echo $this->CTA_add_captcha_fields();
        } else if (isset($opt_val['protected']['login_form_attempts']) && count($ipAttempts)==1 && $opt_val['protected']['login_form_attempts'] <= $ipAttempts[0]->attempts){
                echo $this->CTA_add_captcha_fields();
        }
}


/**
* Function to add the Captcha field to the checkout registration form
* @since 1.3
*/
function CTA_add_captcha_register_fields_to_checkout() {
	$checkout = new WC_Checkout();
	if ( ! is_user_logged_in() &&  $checkout->is_registration_enabled()) {
		echo '<div class="create-account">';
			$this->CTA_add_captcha_register_fields();
		echo '</div>';
	}

}


/**
* Function to add the Captcha field to the Register Page
* @since 0.1
*/
function CTA_add_captcha_register_fields() {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        if (isset($opt_val['protected']['register_form']) && $opt_val['protected']['register_form'] == 'Y') {
                echo $this->CTA_add_captcha_fields();
        }
}


/**
* Function to add the Captcha field to the Lost Password Page
* @since 0.1
*/  
function CTA_add_captcha_lostpassword_fields() {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
		
        if (isset($opt_val['protected']['lostpassword_form']) && $opt_val['protected']['lostpassword_form'] == 'Y') {
                echo $this->CTA_add_captcha_fields();
        }
}


/**
* Function to add the Captcha field to the Comment Page
* @since 0.1
*/  
function CTA_add_captcha_comment_fields() {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        if (isset($opt_val['protected']['comment_form']) && $opt_val['protected']['comment_form'] == 'Y') {
                echo $this->CTA_add_captcha_fields();
        }
}


/**
* Function to format and load the Captcha fields
* @since 0.1
* @updated 1.3
*/  
function CTA_add_captcha_fields() {

        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );


        $dataToEcho = $this->generatedCaptchaBox($opt_val);
	$dataToEcho .= '<input id="emailHP-'.uniqid().'" type="text" class="hPot_checker emailHP" name="emailHP" size="25" value="" placeholder="Do not fill in this field" />';
        switch ($opt_val['captcha-type']) {
                case 'google-recaptcha':
                        $dataToEcho .= $this->googleRecaptchaInstanceJS();
                break;
                default;
                break;
        }
        return $dataToEcho;

}

/**
* Function to load and display visual captcha image to FE
* @since 1.3
*/
function visualCaptcha_redirect() {
        global $CTA_opt_name, $CTA_visualCaptcha_namespace;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
    if ($opt_val['captcha-type'] == 'visualCaptcha') {
        if( isset($_GET['namespace'])){ //&& $_GET['namespace'] == $CTA_visualCaptcha_namespace ) {
		require_once( dirname(__FILE__). '/visualcaptcha/vendor/autoload.php' );
		// Initialize Session
		session_cache_limiter( false );

		if ( session_id() == '' ) {
    			session_start();
		}

		\Slim\Slim::registerAutoloader();

		$app = new \Slim\Slim();

		// Setup CORS
		$app->response[ 'Access-Control-Allow-Origin' ] = '*';

		// Inject Session object into app
		if ( $namespace = $app->request->params( 'namespace' ) ) {
    			$app->session = new \visualCaptcha\Session( 'visualcaptcha_' . $namespace );
		} else {
    			$app->session = new \visualCaptcha\Session();
		}

		// Populates captcha data into session object
			// -----------------------------------------------------------------------------
		// @param howmany is required, the number of images to generate
		$app->get( '/start/:howmany', function( $howMany ) use( $app ) {
    			$captcha = new \visualCaptcha\Captcha( $app->session );
    			$captcha->generate( $howMany );

    			$app->response[ 'Content-Type' ] = 'application/json';

    			echo json_encode( $captcha->getFrontEndData() );
		} );
		// Streams captcha images from disk
		// -----------------------------------------------------------------------------
		// @param index is required, the index of the image you wish to get
		$app->get( '/image/:index', function( $index ) use( $app ) {
    			$captcha = new \visualCaptcha\Captcha( $app->session );

    			if ( ! $captcha->streamImage( $app->response, $index, $app->request->params( 'retina' ) ) ) {
        			$app->pass();
    			}
		} );

		// Streams captcha audio from disk
		// -----------------------------------------------------------------------------
		// @param type is optional and defaults to 'mp3', but can also be 'ogg'
		$app->get( '/audio(/:type)', function( $type = 'mp3' ) use( $app ) {
    			$captcha = new \visualCaptcha\Captcha( $app->session );
    			if ( ! $captcha->streamAudio( $app->response, $type ) ) {
        			$app->pass();
    			}
		} );

		$app->run();
		exit;
	}
     }
}


/**
* Function to load and display secureImage captcha image to FE
* @since 1.3
* @updated 1.3.2
*/
function securImage_redirect() {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
    if ($opt_val['captcha-type'] == 'built-in' || $opt_val['captcha-type'] == '') {
    	if( isset($_GET['cta_securimage']) && !empty( $_GET['cta_securimage']) ) {
    		require_once( dirname(__FILE__) . '/securimage/securimage.php' );
		$img = new Securimage();

		$img->ttf_file        = dirname(__FILE__) .'/securimage/AHGBold.ttf';
		if (ISSET($_GET['mathOrText']) && !empty($_GET['mathOrText']) && $_GET['mathOrText']=='math') {
        		$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
		} elseif (ISSET($_GET['mathOrText']) && !empty($_GET['mathOrText']) && $_GET['mathOrText']=='word') {
			$img->captcha_type    = Securimage::SI_CAPTCHA_WORDS; // show a simple word problem instead of text
		}
		//$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
		$img->image_width = 250;
		$img->image_height = (int)($img->image_width * 0.25);
		//$img->image_height    = 50;                                // height in pixels of the image
		//$img->image_width     = $img->image_height * 3.5;          // a good formula for image size based on the height
		$img->perturbation    = $_GET['distortion'];                              // 1.0 = high distortion, higher numbers = more distortion
		$img->image_signature = $_GET['signature'];
		$img->signature_font = dirname(__FILE__) .'/securimage/AHGBold.ttf';
		$img->wordlist_file = dirname(__FILE__) .'/securimage/words/words.txt';
		//$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
		//$img->text_color      = new Securimage_Color("#EAEAEA");   // captcha text color
		//$img->num_lines       = 8;                                 // how many lines to draw over the image
		//$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
		//$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
		if (ISSET($_GET['signatureColour']) && !empty($_GET['signatureColour']) && preg_match("/^#(?:[0-9a-fA-F]{3}){1,2}$/"
, $_GET['signatureColour'])) {
        		$img->signature_color = new Securimage_Color('#'.$_GET['signatureColour'].'');
		}
		//$img->signature_color = new Securimage_Color(rand(0, 64),
		//                                             rand(64, 128),
		//                                             rand(128, 255));  // random signature color

		// see securimage.php for more options that can be set

		// set namespace if supplied to script via HTTP GET
		if (!empty($_GET['namespace'])) $img->setNamespace($_GET['namespace']);


		$img->show();  // outputs the image and content headers to the browser
		// alternate use:
		// $img->show('/path/to/background_image.jpg');
        	exit;
    	}
    }
}

    /**
     * Generate the Captcha Box
     * @since 0.1
     * @updated 1.3
     * Argument 1 is deprecated.  $opt_val will be generated from this function instead of being passed
     */
function generatedCaptchaBox($opt_val = '') {
	global $CTA_visualCaptcha_namespace;
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );

	//enqueue global scripts/style
	wp_enqueue_style('cta-css');

        switch ($opt_val['captcha-type']) {
                case 'google-recaptcha':
                        wp_enqueue_script('captcha-them-all-google');
                        $response = '<label for="g-recaptcha">'.__('Recaptcha','captcha-them-all').'</label><div class="g-recaptcha" id="'.uniqid().'" data-theme="'.$opt_val['google']['grecaptcha-theme'].'" data-type="'.$opt_val['google']['grecaptcha-type'].'" data-sitekey="'.$opt_val['google']['grecaptcha-sitekey'].'" style="transform:scale(0.90);-webkit-transform:scale(0.90);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>';
                break;

                case 'built-in':
                        $signatureColour = str_replace('#', '', $opt_val['securImage']['signatureColour']);
						if ($opt_val['securImage']['mathOrText'] == "math") {
								$placeholder = "Answer...";
						} else {
								$placeholder = "Enter Code Above...";
						}
                        $response = '<p><label for="g-recaptcha">'.__('Recaptcha','captcha-them-all').'</label> <img id="captcha" src="'.get_site_url().'/?cta_securimage=Y&distortion='.$this->securImageDistortion().'&signature='.$opt_val['securImage']['signature'].'&signatureColour='.$signatureColour.'&mathOrText='.$opt_val['securImage']['mathOrText'].'" style="display: block; margin: 0 auto;" alt="CAPTCHA Image" /><span style="text-align: center; display: block;"><a href="#" onclick="document.getElementById(\'captcha\').src = \''.get_site_url().'/?cta_securimage=Y&distortion='.$this->securImageDistortion().'&signature='.$opt_val['securImage']['signature'].'&mathOrText='.$opt_val['securImage']['mathOrText'].'&signatureColour='.$signatureColour.'&\' + Math.random(); return false">'.__('Change Image', 'captcha-them-all').'</a></span><input type="text" placeholder="'.$placeholder.'" name="captcha_code" size="15" maxlength="25" /></p>';
                break;

		case 'visualCaptcha':
			if ($opt_val['visualCaptcha']['numberOfImages'] <= 3) {
				$opt_val['visualCaptcha']['numberOfImages'] = 4;
			}
			$captcha = array(
				'namespace' => $CTA_visualCaptcha_namespace.'_'.uniqid(),
				'numberOfImages' => $opt_val['visualCaptcha']['numberOfImages']
			);
        		$response = '<div data-captcha=\''.json_encode( $captcha ).'\'></div>';		
		break;

                case 'captchasNet':
                        require_once( dirname(__FILE__) . '/CaptchasDotNet.php' );
                        $captchas = new CaptchasDotNet ($opt_val['captchasNet']['username'], $opt_val['captchasNet']['secret'],
                                sys_get_temp_dir().'/captchasnet-random-strings','3600',
                                'abcdefghkmnopqrstuvwxyz','8',
                                '240','80', str_replace('#', '', $opt_val['captchasNet']['colour']));
                                $response = '<p><label for="g-recaptcha">'.__('Recaptcha','captcha-them-all').'</label><input type="hidden" name="captchas-net-random" value="'.$captchas->random().'" />';
                                $response .= $captchas->image(False, 'fg-captchas', 'display: block; margin: 0 auto').'<span style="text-align: center; display: block;"><a href="javascript:captchas_image_reload(\'fg-captchas\')">Change Image</a> | <a href="'.$captchas->audio_url().'" target="_BLANK">Phonetic spelling (mp3)</a></span>';
                                $response .= '<input type="text" class="input" placeholder="Enter Code Above..." name="captchas-net-response" size="10" /></p>';
                break;

                default:
                        $signatureColour = str_replace('#', '', $opt_val['securImage']['signatureColour']);
						if ($opt_val['securImage']['mathOrText'] == "math") {
								$placeholder = "Answer...";
						} else {
								$placeholder = "Enter Code Above...";
						}
                        $response = '<p><label for="g-recaptcha">'.__('Recaptcha','captcha-them-all').'</label> <img id="captcha" src="'.get_site_url().'/?cta_securimage=Y&distortion='.$this->securImageDistortion().'&signature='.$opt_val['securImage']['signature'].'&signatureColour='.$signatureColour.'&mathOrText='.$opt_val['securImage']['mathOrText'].'" style="display: block; margin: 0 auto;" alt="CAPTCHA Image" /><span style="text-align: center; display: block;"><a href="#" onclick="document.getElementById(\'captcha\').src = \''.get_site_url().'/?cta_securimage=Y&distortion='.$this->securImageDistortion().'&signature='.$opt_val['securImage']['signature'].'&mathOrText='.$opt_val['securImage']['mathOrText'].'&signatureColour='.$signatureColour.'&\' + Math.random(); return false">'.__('Change Image', 'captcha-them-all').'</a></span><input type="text" placeholder="'.$placeholder.'" name="captcha_code" size="15" maxlength="25" /></p>';
                break;
        }
return $response;
}



/**
* Validates Captcha response for WordPress Core Comment Form
* @since 0.1
*/
function CTA_validate_captcha_comment($approved, $commentdata)
{
        if ( ! is_user_logged_in() ) {
                global $CTA_opt_name;
                $opt_name = $CTA_opt_name;
                $opt_val = $this->escape_values(get_option( $opt_name ) );
                if (isset($opt_val['protected']['comment_form']) && $opt_val['protected']['comment_form'] == 'Y') {
                        $response = $this->validateCaptchaResponse($opt_val);
                        if (!$response) {
                                return new WP_Error( 'comment_not_approved', __( '<strong>ERROR</strong>: Captcha validation failed.' ), 409 );
                        }
                }
        }

    return $approved;
}

/**
* Validates Captcha response for Woocommerce Registration 
* @since 1.3
*/
function CTA_validate_captcha_register_woocommerce_test($errors)
{
        if (!$this->CTA_validate_captcha_register()) {
                $errors = __( 'Captcha validation failed.' );
        }
    return $errors;
}

/**
* Validates Captcha response for WooCommerce Register Form
* @since 0.1
* @update 1.1
*/
function CTA_validate_captcha_register_woocommerce($errors, $sanitized_user_login, $user_email)
{
        if (!$this->CTA_validate_captcha_register()) {
                $errors = new WP_Error( 'denied', __( 'Captcha validation failed.' ) );
        } 
    return $errors;
}

/**
* Validates Captcha response for WordPress Core Register Form
* @since 1.1
*/
function CTA_validate_captcha_register_core($errors, $sanitized_user_login, $user_email)
{
        if (!$this->CTA_validate_captcha_register()) {
                $errors = new WP_Error( 'denied', __( '<strong>ERROR</strong>: Captcha validation failed.' ) );
        } 
    return $errors;
}


/**
* Validates Captcha response for WordPress Core Register Form
* @since 0.1
* @updated 1.1
*/
function CTA_validate_captcha_register() {
                global $CTA_opt_name;
                $opt_name = $CTA_opt_name;
                $opt_val = $this->escape_values(get_option( $opt_name ) );
                $response = true;
                if (isset($opt_val['protected']['register_form']) && $opt_val['protected']['register_form'] == 'Y') {
                      $response = $this->validateCaptchaResponse($opt_val);
                }

    return $response;
}

/**
* Validates Captcha response for WordPress Core Lost Password Form
* @since 0.1
* @updated 1.1
*/
function CTA_validate_captcha_lostpassword($errors)
{
                global $CTA_opt_name;
                $opt_name = $CTA_opt_name;
                $opt_val = $this->escape_values(get_option( $opt_name ) );
                if (isset($opt_val['protected']['lostpassword_form']) && $opt_val['protected']['lostpassword_form'] == 'Y') {
                        $response = $this->validateCaptchaResponse($opt_val);
                        if (!$response) {
                                if (is_object($errors)) {
                                        $errors->add('denied', __("<strong>ERROR</strong>: Captcha validation failed.") );
                                } else  {
                                        $errors = new WP_Error( 'denied', __("<strong>ERROR</strong>: Captcha validation failed."));
                                }
                        }
                }

    return $errors;
}

/**
* Validates Captcha response for WordPress Core Login Form
* @since 0.1
* @updated 1.1
*/
function CTA_validate_captcha_login($user, $username, $password)
{
        if (!$user=='' || !$password=='') {
        global $CTA_opt_name, $CTA_ipFailedAttTbl, $wpdb;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        $table = $wpdb->prefix.$CTA_ipFailedAttTbl;
        $ip = $this->clientIP();
        $ipAttempts = $wpdb->get_results( "SELECT `id`, `ip`,`attempts`,`last_attempt` FROM  `$table`   WHERE `ip` =  '$ip'  ORDER BY `id` DESC LIMIT 1 " );
        if (ISSET($opt_val['protected']['login_form']) && $opt_val['protected']['login_form'] == 'Y' && (!ISSET($opt_val['protected']['login_form_attempts']) || $opt_val['protected']['login_form_attempts']==0)) { 
                $response = $this->validateCaptchaResponse($opt_val);
                if (!$response) {
                        remove_action('authenticate', 'wp_authenticate_username_password', 20);
                        $user = new WP_Error( 'denied', __("<strong>ERROR</strong>: Captcha validation failed.") );
                }
        } else if (isset($opt_val['protected']['login_form_attempts']) && count($ipAttempts)==1 && $opt_val['protected']['login_form_attempts'] <= $ipAttempts[0]->attempts){
                $response = $this->validateCaptchaResponse($opt_val);
                if (!$response) {
                        remove_action('authenticate', 'wp_authenticate_username_password', 20);
                        $user = new WP_Error( 'denied', __("<strong>ERROR</strong>: Captcha validation failed.") );
                }
        }
        }

    return $user;
}


    /**
     * Validate the Captcha response
     * @since 0.1
     * @updated 1.3
     */
function validateCaptchaResponse($opt_val) {
	global $CTA_visualCaptcha_namespace;
        $response = false;
	if ( !empty($_POST['emailHP']) ) {
		$response = false;
	} else {
        	switch ($opt_val['captcha-type']) {
                	case 'google-recaptcha':
                        	if( isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']) ) {
                                	$resp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$opt_val['google']['grecaptcha-secret']."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
                			if ($resp==NULL || $resp=='') {
                        			$resp=$this->curl_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$opt_val['google']['grecaptcha-secret']."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
                			}
                			if ($resp==NULL || $resp=='') {
                        			$response = false;
                        			new WP_Error( 'denied', __("Could not detect CURL or file_get_contents support.  Contact your webhost for more help") );
                			}
                                	$answer = json_decode($resp);


                                	if($answer->success === FALSE) {
                                        	$response = false;
                                	} else {
                                        	$response = true;
                                	}
                        	} else {
                                	$response = false;
                        	}
                	break;

                	case 'built-in':
                        	require_once( dirname(__FILE__) . '/securimage/securimage.php' );
                        	$securimage = new Securimage();
                        	if ($securimage->check($_POST['captcha_code']) == false) {
                                	$response = false;
                        	} else {
                                	$response = true;
                        	}
                	break;

			case 'visualCaptcha':
    				require_once( dirname(__FILE__). '/visualcaptcha/vendor/autoload.php' );
    				// Initialize Session
    				session_cache_limiter( false );
    				if ( session_id() == '' ) {
        				session_start();
    				}
				if(ISSET($_POST['namespace'])) {
					$namespace = $_POST['namespace'];
				}
        			$session = new \visualCaptcha\Session('visualcaptcha_' . $namespace);
    				$captcha = new \visualCaptcha\Captcha( $session );
    				$frontendData = $captcha->getFrontendData();
    				// If captcha is present, try to validate it
    				if ( $frontendData ) {
        				$response = false;
        				// If an image field name was submitted, try to validate it
        				if ( ( $imageAnswer = $_POST[ $frontendData[ 'imageFieldName' ] ] ) && !empty( $imageAnswer ) ) {
            					if ( $captcha->validateImage( $imageAnswer ) ) {
                					$response = true;
            					}
        				} else if ( ( $audioAnswer = $_POST[ $frontendData[ 'audioFieldName' ] ] ) && !empty( $audioAnswer ) ) {
            					if ( $captcha->validateAudio( $audioAnswer ) ) {
                					$response = true;
            					}
        				}
				}
       				// Clear current session after captcha has been validated
        			$session->clear();
			break;

                	case 'captchasNet':
                        	require_once( dirname(__FILE__) . '/CaptchasDotNet.php' );
                        	$captchas = new CaptchasDotNet ($opt_val['captchasNet']['username'], $opt_val['captchasNet']['secret'],
                        	        sys_get_temp_dir().'/captchasnet-random-strings','3600',
                        	        'abcdefghkmnopqrstuvwxyz','8',
                                	'240','80', str_replace('#', '', $opt_val['captchasNet']['colour']));
                        	if (isset($_REQUEST['captchas-net-response']) && !empty($_REQUEST['captchas-net-response']) && isset($_REQUEST['captchas-net-random']) && !empty($_REQUEST['captchas-net-random'])) {
                                	$captchas_net_response = $_REQUEST['captchas-net-response'];
                                	$captchas_net_random = $_REQUEST['captchas-net-random'];
                                	// Check the random string to be valid and return an error message
                                	// otherwise.
                                	if (!$captchas->validate ($captchas_net_random)) {
                                        	$response = false;
                                	} elseif (!$captchas->verify ($captchas_net_response)) {
                                        	$response = false;
                                	} else {
                                        	$response = true;
                                	}
                        	} else {
                                	$response = false;
                        	}
                	break;

                	default:
                        	require_once( dirname(__FILE__) . 'securimage/securimage.php' );
                        	$securimage = new Securimage();
                        	if ($securimage->check($_POST['captcha_code']) == false) {
                                	$response = false;
                        	} else {
                                	$response = true;
                        	}
                	break;
        	}
	}
	return $response;
}



/**
* Exports IP log to CSV
* @since 1.3
*/
function export_IP_log() {
	global $CTA_ipFailedAttTbl, $wpdb;
	$table = $wpdb->prefix.$CTA_ipFailedAttTbl;
	if (isset($_GET['cta_export_log']) && $_GET['cta_export_log']=='yes' && check_admin_referer( 'exporting-IP-log', '_exporting-IP-log' )) {
		$rows = $wpdb->get_results( "SELECT * FROM `$table`", ARRAY_A);
		$fh = fopen('php://temp', 'rw');

		fputs($fh, "IP Address,RDNS,Last Attempt Seen".PHP_EOL);

		// create line with field names
		//for($i = 0; $i < $fields; $i++) {
  		//	$csv_export.= mysql_field_name($query,$i).';';
		//}
		// newline (seems to work both on Linux & Windows servers)
		// loop through database query and fill export variable
		foreach ( $rows as $row ) {
  			// create line with field values
			fputs($fh, $row['ip'].','.gethostbyaddr($row['ip']).','.$row['last_attempt'].PHP_EOL);
		}
		rewind($fh);
		$csv = stream_get_contents($fh);
		fclose($fh);
		// Export the data and prompt a csv file for download
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=export-".uniqid().".csv");
		echo($csv);
		exit;
	} else if (isset($_GET['cta_delete_log']) && $_GET['cta_delete_log']=='yes' && check_admin_referer( 'delete-IP-log', '_delete-IP-log' )) {
		
		$wpdb->query("DELETE FROM `$table`");
		header('Location: '.admin_url('options-general.php?page=captcha-them-all-options&log-cleared=yes'));
	}
}


    /**
     * Add settings link to wordpress plugins page
     * @since 1.4
     */
function cta_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'captcha-them-all-options',
		get_admin_url() . 'options-general.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_unshift(
		$links,
		$settings_link
	);
	return $links;
}


    /**
     * Sanitizes opt_val array data before updating database
     * @since 1.4
     */
function sanitize_opt_val( $data ) {
    $sanitized_data = array();

    // Sanitize each field based on its type
    foreach( $data as $key => $value ) {
        if ( is_array( $value ) ) {
            // Recursively sanitize nested arrays
            $sanitized_data[ $key ] = $this->sanitize_opt_val( $value );
        } else {
            // Sanitize fields based on their key or type
            switch ( $key ) {
                case 'grecaptcha-sitekey':
                case 'grecaptcha-secret':
                case 'username':
                case 'secret':
                    $sanitized_data[ $key ] = sanitize_text_field( $value );
                    break;
                case 'grecaptcha-theme':
                case 'grecaptcha-type':
                case 'mathOrText':
                    $sanitized_data[ $key ] = sanitize_text_field( $value );
                    break;				
                case 'colour':
                case 'signatureColour':
                    $sanitized_data[ $key ] = sanitize_hex_color( $value );
                    break;
                case 'numberOfImages':
                case 'distortion':
                    $sanitized_data[ $key ] = intval( $value );
                    break;
                default:
                    $sanitized_data[ $key ] = sanitize_text_field( $value );
            }
        }
    }

    return $sanitized_data;
}

    /**
     * Escape HTML from all fields
     * @since 1.4
     */
function escape_values($value) {
    if (is_array($value)) {
        $escaped_array = array();
        foreach ($value as $key => $subvalue) {
            $escaped_array[$key] = $this->escape_values($subvalue);
        }
        return $escaped_array;
    } else {
        return esc_html($value);
    }
}

    /**
     * Adds the Extra Setting menu option to the Settings Menu
     * @since 0.1
     * @updated 1.1.1
     */
        function add_captcha_settings_options() {
                $this->activate_options();
                add_options_page( 'Captcha Them All', 'Captcha Them All', 'manage_options', 'captcha-them-all-options', array($this, 'captcha_wordpress_options'));
        }

     /**
     * Displays the Captcha Them All options page
     * @since 0.1
     * @updated 1.1
     * @updated 1.3
     * @updated 1.4
     */
function captcha_wordpress_options() {
    //must check that the user has the required capability
        if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

    wp_enqueue_script('jquery');
    wp_enqueue_script('rcwp-admin-js', plugins_url( 'js/admin.js', __FILE__ ), array('jquery'), '1.1', false);
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_style( 'wp-color-picker' );

    // variables for the field and option names
    global $CTA_opt_name;
    $opt_name = $CTA_opt_name;
    $hidden_field_name = 'captcha_submit_hidden';
    $data_field_name = $opt_name;

    // Read in existing option value from database
    $opt_val = $this->sanitize_opt_val($this->escape_values(get_option( $opt_name )));

if (isset($_GET['log-cleared']) && $_GET['log-cleared']=='yes') {
        echo '<div class="updated"><p><strong>'.__('IP log has been cleared', 'captcha-them-all' ).'</strong></p></div>';
}

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' && wp_verify_nonce( $_REQUEST['_wpnonce'], 'captcha-settings' )) {

        // Read their posted value
        $opt_val = $this->sanitize_opt_val($this->escape_values($_POST[$data_field_name]));

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an settings updated message on the screen

                echo '<div class="updated"><p><strong>'.__('Settings saved.', 'captcha-them-all' ).'</strong></p></div>';

    }
        if (!ISSET($opt_val['captcha-type']) || empty($opt_val['captcha-type'])) {
                $opt_val['captcha-type'] = 'built-in';
        }
        echo '<label>'.__("You can edit the settings for your installation below", 'captcha-them-all' ).'</label>';	
	$exportWpnonce = wp_create_nonce( 'exporting-IP-log' );
	$deleteWpnonce = wp_create_nonce( 'delete-IP-log' );
	echo '<br><a href="'.admin_url('?cta_export_log=yes&_exporting-IP-log='.$exportWpnonce).'">Download IP log</a> | <a href="'.admin_url('?cta_delete_log=yes&_delete-IP-log='.$deleteWpnonce).'">Clear IP Log</a>';
?>
<script type='text/javascript'>
    jQuery(document).ready(function($) {
        $('.my-color-picker').wpColorPicker();
    });
</script>
        <form name="settings" method="post" action="">
                <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
                 <h2 class="title"><?php _e('Choose Captcha-protected areas', 'captcha-them-all' ); ?></h2>
                <table class="form-table">
                        <tbody>
                                <tr>
                                        <th scope="row"><label for="page_handler"><?php _e('Protect these areas', 'captcha-them-all' ); ?></label></th>
                                        <td>
                                                <fieldset><legend class="screen-reader-text"><span><?php _e('Protect Login Form', 'captcha-them-all' ); ?></span></legend></fieldset>
						<input type="checkbox" id="protectedLoginForm" name="<?php echo $data_field_name; ?>[protected][login_form]" value="Y" <?php checked(ISSET($opt_val['protected']['login_form']) ? $opt_val['protected']['login_form'] : 'N',  'Y'); ?>> Login Form  

                                                <p class="description" id="discover-description"><?php _e('Display Captcha protection on Login Form', 'captcha-them-all' ); ?></p>
                                                <br>
                                                <fieldset id="login_form_attempts_fieldset"><legend class="screen-reader-text"><span><?php _e('Protect Login Form After failed attempts', 'captcha-them-all' ); ?></span></legend>
                                                <select id="login_form_attempts" name="<?php echo $data_field_name; ?>[protected][login_form_attempts]" required>
                                                        <option value="0" <?php selected( ISSET($opt_val['protected']['login_form_attempts']) ? $opt_val['protected']['login_form_attempts'] : 0, 0 ); ?>>Show on every login</option>
                                                        <option value="1" <?php selected( ISSET($opt_val['protected']['login_form_attempts']) ? $opt_val['protected']['login_form_attempts'] : 0, 1 ); ?>>Show after first failure</option>
                                                        <option value="2" <?php selected( ISSET($opt_val['protected']['login_form_attempts']) ? $opt_val['protected']['login_form_attempts'] : 0, 2 ); ?>>Show after second failure</option>
                                                        <option value="3" <?php selected( ISSET($opt_val['protected']['login_form_attempts']) ? $opt_val['protected']['login_form_attempts'] : 0, 3 ); ?>>Show after third failure</option>
                                                        <option value="4" <?php selected( ISSET($opt_val['protected']['login_form_attempts']) ? $opt_val['protected']['login_form_attempts'] : 0, 4 ); ?>>Show after forth failure</option>
                                                        <option value="5" <?php selected( ISSET($opt_val['protected']['login_form_attempts']) ? $opt_val['protected']['login_form_attempts'] : 0, 5 ); ?>>Show after fifth failure</option>
                                                </select>
                                                <p class="description" id="discover-description"><?php _e('Display Captcha protection on Login Form if "n" failed attempts', 'captcha-them-all' ); ?></p>
                                                </fieldset>
                                                <br>
						<input type="checkbox" name="<?php echo $data_field_name; ?>[protected][register_form]" value="Y" <?php checked(ISSET($opt_val['protected']['register_form']) ? $opt_val['protected']['register_form'] : 'N',  'Y'); ?>> Register Form
                                                <p class="description" id="discover-description"><?php _e('Display Captcha protection on Register Form', 'captcha-them-all' ); ?></p>
                                                <br>
						<input type="checkbox" name="<?php echo $data_field_name; ?>[protected][lostpassword_form]" value="Y" <?php checked(ISSET($opt_val['protected']['lostpassword_form']) ? $opt_val['protected']['lostpassword_form'] : 'N',  'Y'); ?>> Lost Password Form
                                                <p class="description" id="discover-description"><?php _e('Display Captcha protection on Lost Password Form', 'captcha-them-all' ); ?></p>
                                                <br>
						<input type="checkbox" name="<?php echo $data_field_name; ?>[protected][comment_form]" value="Y" <?php checked(ISSET($opt_val['protected']['comment_form']) ? $opt_val['protected']['comment_form'] : 'N',  'Y'); ?>> Comments
                                                <p class="description" id="discover-description"><?php _e('Display Captcha protection on Comments', 'captcha-them-all' ); ?></p>
                                                <br>
                                                Contact Form 7 - Captcha ShortCode: [cta_recaptcha* cta_recaptcha]
                                                <p class="description" id="discover-description"><?php _e('Display Captcha protection on all Contact Form 7 Forms (Does not use native Contact Form 7 reCAPTCHA code, uses Captcha Them All code)', 'captcha-them-all' ); ?></p>
                                        </td>
                                </tr>
                        </tbody>
                </table>
                 <h2 class="title"><?php _e('Choose Captcha Type', 'captcha-them-all' ); ?></h2>
                <table class="form-table">
                        <tbody>
                                <tr>
                                        <th scope="row"><label for="page_handler"><?php _e('Type of Captcha', 'captcha-them-all' ); ?></label></th>
                                        <td>
                                                <fieldset><legend class="screen-reader-text"><span><?php _e('Type of Captcha', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                <select id="captchaType" name="<?php echo $data_field_name; ?>[captcha-type]" required>
                                                        <option value="google-recaptcha" <?php selected( $opt_val['captcha-type'], 'google-recaptcha' ); ?>>Google Recaptcha</option>
                                                        <option value="visualCaptcha" <?php selected( $opt_val['captcha-type'], 'visualCaptcha' ); ?>>Visual Captcha (Built-In Captcha)</option>
                                                        <option value="built-in" <?php selected( $opt_val['captcha-type'], 'built-in' ); ?>>Secure Image (Built-In Captcha)</option>
                                                        <option value="captchasNet" <?php selected( $opt_val['captcha-type'], 'captchasNet' ); ?>>Captchas.Net</option>
                                                </select>
                                                <p class="description" id="discover-description"><?php _e('Only one type of Captcha can be used at once.', 'captcha-them-all' ); ?></p>
                                        </td>
				</tr>
                        </tbody>
                </table>
                <div id="google-recaptcha" class="rcwp_tabs" style="display: none;">
                        <h2 class="title"><?php _e('Google Recaptcha Settings', 'captcha-them-all' ); ?></h2>
                        <table class="form-table">
                                <tbody>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Google Recaptcha site key', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Google Recaptcha site key', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="textbox" name="<?php echo $data_field_name; ?>[google][grecaptcha-sitekey]" id="grecaptcha-sitekey" value="<?php echo ISSET($opt_val['google']['grecaptcha-sitekey']) ? $opt_val['google']['grecaptcha-sitekey'] : ''; ?>" size="55">
                                                        <p class="description" id="discover-description"><?php _e('Getting Started', 'captcha-them-all' ); ?>: <a href="https://developers.google.com/recaptcha/docs/start" target="_BLANK">https://developers.google.com/recaptcha/docs/start</a>
                                                        <br>
                                                        <?php _e('Get your Key', 'captcha-them-all' ); ?>: <a href="https://www.google.com/recaptcha/intro/index.html" target="_BLANK">https://www.google.com/recaptcha/intro/index.html</a></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Google Recaptcha secret key', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Google Recaptcha secret key', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="textbox" name="<?php echo $data_field_name; ?>[google][grecaptcha-secret]" id="grecaptcha-secret" value="<?php echo ISSET($opt_val['google']['grecaptcha-secret']) ? $opt_val['google']['grecaptcha-secret'] : ''; ?>" size="55">
                                                        <p class="description" id="discover-description"><?php _e('Getting Started', 'captcha-them-all' ); ?>: <a href="https://developers.google.com/recaptcha/docs/start" target="_BLANK">https://developers.google.com/recaptcha/docs/start</a>
                                                        <br>
                                                        <?php _e('Get your Key', 'captcha-them-all' ); ?>: <a href="https://www.google.com/recaptcha/intro/index.html" target="_BLANK">https://www.google.com/recaptcha/intro/index.html</a></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Google Recaptcha Theme', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Google Recaptcha Theme', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                <select id="grecaptcha-theme" name="<?php echo $data_field_name; ?>[google][grecaptcha-theme]" required>
                                                        <option value="light" <?php selected( ISSET($opt_val['google']['grecaptcha-theme']) ? $opt_val['google']['grecaptcha-theme'] : 'light', 'light' ); ?>>Light Theme</option>
                                                        <option value="dark" <?php selected( ISSET($opt_val['google']['grecaptcha-theme']) ? $opt_val['google']['grecaptcha-theme'] : 'light', 'dark' ); ?>>Dark Theme</option>
                                                </select>
                                                        <p class="description" id="discover-description">
                                                        <?php _e('Dark or Light Theme?', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Google Recaptcha Type', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Google Recaptcha Type', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                <select id="grecaptcha-theme" name="<?php echo $data_field_name; ?>[google][grecaptcha-type]" required>
                                                        <option value="image" <?php selected( ISSET($opt_val['google']['grecaptcha-type']) ? $opt_val['google']['grecaptcha-type'] : 'image', 'image' ); ?>>Image</option>
                                                        <option value="audio" <?php selected( ISSET($opt_val['google']['grecaptcha-type']) ? $opt_val['google']['grecaptcha-type'] : 'image', 'audio' ); ?>>Audio</option>
                                                </select>
                                                        <p class="description" id="discover-description">
                                                        <?php _e('Image or Audio Captcha?', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
                <div id="visualCaptcha" class="rcwp_tabs" style="display: none;">
                        <h2 class="title"><?php _e('Visual Captcha (Built-in)', 'captcha-them-all' ); ?></h2>
                          <table class="form-table">
                                <tbody>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Number of Challenge Images', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Number of Challenge Images', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                <select id="numberOfImages" name="<?php echo $data_field_name; ?>[visualCaptcha][numberOfImages]" required>
                                                        <option value="4" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '4' ); ?>>4</option>
                                                        <option value="5" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '5' ); ?>>5</option>
                                                        <option value="6" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '6' ); ?>>6</option>
                                                        <option value="7" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '7' ); ?>>7</option>
                                                        <option value="8" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '8' ); ?>>8</option>
                                                        <option value="9" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '9' ); ?>>9</option>
                                                        <option value="10" <?php selected( ISSET($opt_val['visualCaptcha']['numberOfImages']) ? $opt_val['visualCaptcha']['numberOfImages'] : 4, '10' ); ?>>10</option>
                                                </select>
                                                        <p class="description" id="discover-description"><?php _e('The amount of challenge images show to the user to choose from.  Minimum is 4.', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
                <div id="built-in" class="rcwp_tabs" style="display: none;">
                        <h2 class="title"><?php _e('Securimage (Built-in) Captcha', 'captcha-them-all' ); ?></h2>
                          <table class="form-table">
                                <tbody>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Signature', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Signature', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="textbox" name="<?php echo $data_field_name; ?>[securImage][signature]" id="captchasNet-username" value="<?php echo ISSET($opt_val['securImage']['signature']) ? $opt_val['securImage']['signature'] : ''; ?>" size="55">
                                                        <p class="description" id="discover-description"><?php _e('Add a signature to your captcha image', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Random String Captcha, Math Problem or Word Captcha', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Random String Captcha, Math Problem or Word Captcha', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                <select id="mathOrText" name="<?php echo $data_field_name; ?>[securImage][mathOrText]" required>
                                                        <option value="text" <?php selected( ISSET($opt_val['securImage']['mathOrText']) ? $opt_val['securImage']['mathOrText'] : 'text', 'text' ); ?>>Random String</option>
                                                        <option value="math" <?php selected( ISSET($opt_val['securImage']['mathOrText']) ? $opt_val['securImage']['mathOrText'] : 'text', 'math' ); ?>>Math</option>
							<option value="word" <?php selected( ISSET($opt_val['securImage']['mathOrText']) ? $opt_val['securImage']['mathOrText'] : 'text', 'word' ); ?>>Word</option>
                                                </select>
                                                        <p class="description" id="discover-description"><?php _e('Random String, Math or Word problem for the Catcha', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Signature Colour', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Signature', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="text" value="<?php echo ISSET($opt_val['securImage']['signatureColour']) ? $opt_val['securImage']['signatureColour'] : '#000000'; ?>" name="<?php echo $data_field_name; ?>[securImage][signatureColour]" class="my-color-picker" data-default-color="#000000" />
                                                        <p class="description" id="discover-description"><?php _e('Colour of signature in Captcha (NOTE: Be careful as some colours may bleed into the background)', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Distortion', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Distortion', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        Lowest(0)<input type="range" min="0" max="10" step="1" value="<?php echo ISSET($opt_val['securImage']['distortion']) ? $opt_val['securImage']['distortion'] : 5; ?>" name="<?php echo $data_field_name; ?>[securImage][distortion]" onchange="rangevalue.value=value" />Highest(10)
                                                        <br><output style="margin-left: 13.5%;" id="rangevalue"><?php echo ISSET($opt_val['securImage']['distortion']) ? $opt_val['securImage']['distortion'] : 5; ?></output>
                                                        <p class="description" id="discover-description"><?php _e('Amount of distortion in Captcha Image (0 lowest, 10 highest)', 'captcha-them-all' ); ?>`</p>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
                <div id="captchasNet" class="rcwp_tabs" style="display: none;">
                        <h2 class="title"><?php _e('Captchas.Net Settings', 'captcha-them-all' ); ?></h2>
                          <table class="form-table">
                                <tbody>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Captchas.Net username', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Captchas.Net username', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="textbox" name="<?php echo $data_field_name; ?>[captchasNet][username]" id="captchasNet-username" value="<?php echo ISSET($opt_val['captchasNet']['username']) ? $opt_val['captchasNet']['username'] : ''; ?>" size="55">
                                                        <p class="description" id="discover-description"><?php _e('Getting Started', 'captcha-them-all' ); ?>: <a href="http://captchas.net/" target="_BLANK">http://captchas.net/</a>
                                                        <br>
                                                        <?php _e('Get your Key', 'captcha-them-all' ); ?>: <a href="http://captchas.net/registration/" target="_BLANK">http://captchas.net/registration/</a></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Captchas.Net secret key', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Captchas.Net secret key', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="textbox" name="<?php echo $data_field_name; ?>[captchasNet][secret]" id="grecaptcha-secret" value="<?php echo ISSET($opt_val['captchasNet']['secret']) ? $opt_val['captchasNet']['secret'] : ''; ?>" size="55">
                                                        <p class="description" id="discover-description"><?php _e('Getting Started', 'captcha-them-all' ); ?>: <a href="http://captchas.net/" target="_BLANK">http://captchas.net/</a>
                                                        <br>
                                                        <?php _e('Get your Key', 'captcha-them-all' ); ?>: <a href="http://captchas.net/registration/" target="_BLANK">http://captchas.net/registration/</a></p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row"><label for="page_handler"><?php _e('Colour', 'captcha-them-all' ); ?></label></th>
                                                <td>
                                                        <fieldset><legend class="screen-reader-text"><span><?php _e('Colour', 'captcha-them-all' ); ?></span></legend></fieldset>
                                                        <input type="text" value="<?php echo ISSET($opt_val['captchasNet']['colour']) ? $opt_val['captchasNet']['colour'] : '#000000'; ?>" name="<?php echo $data_field_name; ?>[captchasNet][colour]" class="my-color-picker" data-default-color="#000000" />
                                                        <p class="description" id="discover-description"><?php _e('Colour of the captcha (NOTE: Make sure this is a colour your users can see on your site)', 'captcha-them-all' ); ?></p>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
                <?php wp_nonce_field( 'captcha-settings' ); ?>
                <p class="submit">
                        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Settings') ?>" />
                </p>
        </form>
<?php
}



/**
* load tagcode ctc_recaptcha for WPCF7
* @since 1.0.1
*/
function custom_wpcf7_add_tagcode() {	
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
        wpcf7_add_form_tag( 'cta_recaptcha*', array($this, 'get_recaptcha_tagcode') ); // "clock" is the type of the form-tag
        if (is_admin()) {
        	$tag_generator = WPCF7_TagGenerator::get_instance();
        	$tag_generator->add( 'cta_recaptcha', __( 'CTA ReCAPTCHA', 'captcha-them-all' ),
        			array($this, 'cta_tag_generator_recaptcha'), array( 'nameless' => 1 ) );
        }
}
 
/**
* load captcha fields for ctc_recaptcha tagcode
* @since 1.0.1
* @updated 1.3
*/
function get_recaptcha_tagcode( $tag ) {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
	switch ($opt_val['captcha-type']) {
		case 'visualCaptcha':
        		$script = '
                		<script type="text/javascript">
                        		jQuery(window).on(\'wpcf7:invalid\', function() {
                                        	jQuery(\'.visualCaptcha-refresh-button a\').click();
                        		});
                		</script>';
		break;
		default:
			$script = '';
		break;
	}
	$errorTrailer = '<span class="wpcf7-form-control-wrap cta_recaptcha"></span>';

    return $script.$this->CTA_add_captcha_fields().$errorTrailer;
}

/**
 * load captcha fields for ctc_recaptcha tagcode
 * @since 1.2
 */
function cta_tag_generator_recaptcha( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );


	$description = sprintf( esc_html( __( "Generate a form-tag for a CTA ReCAPTCHA widget. For more details, see %s.", 'captcha-them-all' ) ), '<a href="'.admin_url('options-general.php?page=captcha-them-all-options').'" target="_BLANK" >'.__( 'Captcha Them All Settings', 'captcha-them-all' ).'</a>'  );


?>
<div class="control-box">
	<fieldset>
		<legend><?php echo $description; ?></legend>
	</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="cta_recaptcha* cta_recaptcha" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'captcha-them-all' ) ); ?>" />
	</div>
</div>
<?php
}


/**
* Validate the captcha response for Contact Form 7
* @since 1.0.1
* @updated 1.2
*/
function wpcf7_confirmation_validation_filter( $result, $tag ) {
        global $CTA_opt_name;
        $opt_name = $CTA_opt_name;
        $opt_val = $this->escape_values(get_option( $opt_name ) );
                $tag = new WPCF7_FormTag( $tag );

                $tag->name = 'cta_recaptcha';
                if ( 'cta_recaptcha' == $tag->name ) {
                        $response = $this->validateCaptchaResponse($opt_val);
                        if (!$response) {
                                $result->invalidate( $tag, "Captcha validation failed." );
                        }
                }
    return $result;
}

/**
* Set all default options and create IP failed attempts DB
* @since 1.1.1
* @updated 1.3
*/

function run_db_actions() {
        global $wpdb, $CTA_DB_version, $CTA_ipFailedAttTbl;
        $opt_name = 'captcha-them-all';
        $table = $wpdb->prefix.$CTA_ipFailedAttTbl;
        if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table ){
 
                $sql = "CREATE TABLE `$table`  (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `ip` VARCHAR(50) NOT NULL,
                        `attempts` INT(11) NOT NULL,
                        `last_attempt` DATETIME,
                        PRIMARY KEY  (id)
                );";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($sql);
        }

        $opt_val = $this->escape_values(get_option( $opt_name ) );
        if (!ISSET($opt_val) || empty($opt_val)){
                $opt_val = array(
                        'protected' => array(
                                'login_form' => 'N', 'login_form_attempts' => 0, 'register_form' => 'N', 'lostpassword_form' => 'N', 'comment_form' => 'N'
                        ),
                        'captcha-type' => 'built-in',
                        'google' => array
                        (
                                'grecaptcha-sitekey' => '',
                                'grecaptcha-secret' => '',
                                'grecaptcha-theme' => 'light',
                                'grecaptcha-type' => 'image'
                        ),
                        'securImage' => array
                        (
                                'signature' => '',
                                'mathOrText' => 'text',
                                'signatureColour' => '#000000',
                                'distortion' => 5
                        ),
			'visualCaptcha' => array
			(
				'numberOfImages' => 4
			),
                        'captchasNet' => array
                        (
                                'username' => '',
                                'secret' => '',
                                'colour' => '#000000'
                        )
                );
                update_option($opt_name, $opt_val);
        }
        if (!ISSET($opt_val['protected']['login_form_attempts']) || empty($opt_val['protected']['login_form_attempts'])) {
        		$opt_val['protected']['login_form_attempts'] = 0;
        		update_option($opt_name, $opt_val);
        }
        if (!ISSET($opt_val['visualCaptcha']['numberOfImages']) || empty($opt_val['visualCaptcha']['numberOfImages'])) {
		//Runs for versions earlier than 1.3 to create default visualCaptcha options
                        $opt_val['visualCaptcha']['numberOfImages'] = 4;
                        update_option($opt_name, $opt_val);
        }
}

/**
* Set all default options and create IP failed attempts DB
* @since 1.0.1
* @updated 1.1.1
*/
function activate_options() {
        global $CTA_DB_version;
        $Curr_DB_version = $this->escape_values(get_option('CTA_DB_version') );
        if ($Curr_DB_version != $CTA_DB_version) {
                $this->run_db_actions();
                update_option('CTA_DB_version', $CTA_DB_version);
        }
}

/**
 *Call admin notice for Plugin Depenancies
 * @since 1.2
 */
function GC_dependancy_error_notice() {
 echo '<div class="notice notice-error">
 <p>'.__( 'Sorry, but the plugin', 'captcha-them-all' ).' <b>Google Captcha (reCAPTCHA) by BestWebSoft</b> '.__( 'cannot be active with', 'captcha-them-all').' <b>Captcha Them All</b>.  '.__( 'Please either deactivate', 'captcha-them-all').' <b>Google Captcha (reCAPTCHA) by BestWebSoft</b> '.__( 'or', 'captcha-them-all').' <b>Captcha Them All</b>.';
 echo '</p></div>';
 }

/**
 * Action to call the function to check for Plugin Dependancies
 * @since 1.2
 */
 function check_plugin_dependancies() {
 	// Require parent plugin
 	if (  is_plugin_active( 'google-captcha/google-captcha.php' ) and current_user_can( 'activate_plugins' ) ) {
 		add_action('admin_notices', array($this, 'GC_dependancy_error_notice'));
 	}
 }

/**
 * Add CTA field to WP-Forms Form Generator in Admin Panel
 * @since 1.3
 */
//function wpforms_add_button($fields) {
//	array_push($fields['standard']['fields'], array('name' => 'CTA Recaptcha', 'type' => 'captcha', 'icon' => 'fa-wrench', 'order' => 99));
//return $fields;
//}

//function wpforms_add_field($fields) {
//	array_push($fields, 'captcha');
//	return $fields;
//}

//function wpforms_validate_captcha($errors, $form_data) {
//
//              global $CTA_opt_name;
//                $opt_name = $CTA_opt_name;
//                $opt_val = $this->escape_values(get_option( $opt_name ) );
//                $response = $this->validateCaptchaResponse($opt_val);
//		//file_put_contents('/tmp/testValidate', $response);
//	return $errors;
//}

}

/**
* Define the Class
* @since 0.1
*/
$CTA = new CaptchaThemAll();

/**
* Action to call the function to display captcha Code for Login/Register/Lost Password/Comments
* @since 0.1
*/
add_action( 'login_form', array($CTA, 'CTA_add_captcha_login_fields') ); //Works for WP Core
add_action( 'woocommerce_login_form', array($CTA, 'CTA_add_captcha_login_fields') ); //Works for WooCommerce Core
add_action( 'register_form', array($CTA, 'CTA_add_captcha_register_fields') ); //Works for WP Core
add_action( 'woocommerce_register_form', array($CTA, 'CTA_add_captcha_register_fields') ); //Works for WooCommerce Core
add_action( 'lostpassword_form', array($CTA, 'CTA_add_captcha_lostpassword_fields') ); //Works for WP Core
add_action( 'woocommerce_lostpassword_form', array($CTA, 'CTA_add_captcha_lostpassword_fields'), 10, 0 ); //Works for WooCommerce Core
add_action( 'comment_form_after_fields', array($CTA, 'CTA_add_captcha_comment_fields') );

/**
* Action to call the function to load the custom tag code
* @since 1.0.1
*/
add_action( 'wpcf7_init', array($CTA, 'custom_wpcf7_add_tagcode') );

/**
* Filter to call the function to validate the captcha code for Contact Form 7
* @since 1.0.1
*/
add_filter( 'wpcf7_validate_cta_recaptcha*', array($CTA, 'wpcf7_confirmation_validation_filter'), 20, 2 );


/**
* Filter to call the function to add the CTA button to Wp Forms
* @since 1.0.1
*/
//add_filter( 'wpforms_builder_fields_buttons', array($CTA, 'wpforms_add_button'), 10, 1 );


/**
* Filter to call the function to add 'captcha' type to fields
* @since 1.3
*/
//add_filter('wpforms_load_fields', array($CTA, 'wpforms_add_field'), 10, 1 );


//add_filter('wpforms_process_initial_errors', array($CTA, 'wpforms_validate_captcha'), 20, 2);

/**
* Filter to run the function to validate captcha response for LOGIN
* @since 0.1
* @updated 1.1
*/
add_filter( 'authenticate', array($CTA, 'CTA_validate_captcha_login'), 10, 3 );  //Works for WP Core Login Page & WooCommerce Core Login Page

/**
* Action to call the function to validate captcha response for REGISTER
* @since 0.1
* @updated 1.1
*/
add_filter( 'registration_errors', array($CTA, 'CTA_validate_captcha_register_core'), 10, 3 ); //Works for WP Core Registration Page
add_filter( 'woocommerce_registration_errors', array($CTA, 'CTA_validate_captcha_register_woocommerce'), 10, 3 ); //Works for WooCommerce Core Registration Page
add_filter('woocommerce_registration_error_email_exists', array($CTA, 'CTA_validate_captcha_register_woocommerce_test'), 10, 2);


/**
* Filter to call the function to add settings link to wordpress plugin page
* @since 1.4
*/
add_filter( 'plugin_action_links_captcha-them-all/captcha-them-all.php', array($CTA, 'cta_settings_link'));

/**
* Action to call the function to validate captcha response for LOST PASSWORD
* @since 0.1
* @updated 1.2
*/
//add_filter( 'allow_password_reset', array($CTA, 'CTA_validate_captcha_lostpassword'), 10, 2 ); // Depreciated
add_action( 'lostpassword_post', array($CTA, 'CTA_validate_captcha_lostpassword'), 10, 1);

/**
* Filter to run the function to validate captcha response for COMMENT
* @since 0.1
*/
add_filter( 'pre_comment_approved', array($CTA, 'CTA_validate_captcha_comment'), 10, 2);

/**
* Action to redirect for cta_securimage parameter requests to generate image for securimage captcha challenges
* @since 1.3
*/
add_action('send_headers', array($CTA, 'securImage_redirect'));

/**
* Action to redirect for cta_visualcaptcha parameter requests to generate image for visual captcha challenges
* @since 1.3
*/
add_action('send_headers', array($CTA, 'visualCaptcha_redirect'));

/**
* Add captcha code to checkout registration form in woocommerce
* @since 1.3
*/
add_action('woocommerce_after_checkout_registration_form', array($CTA, 'CTA_add_captcha_register_fields_to_checkout'));


/**
* Action to redirect for cta_export_log parameter requests to generate CSV for exporting log
* @since 1.3
*/
add_action('admin_init', array($CTA, 'export_IP_log'));

/**
* Action to call the function to register required scripts
* @since 0.1
*/
add_action( 'login_enqueue_scripts', array($CTA, 'register_scripts'), 8 );
add_action( 'wp_enqueue_scripts', array($CTA, 'register_scripts'), 8 );

/**
* Action to call custom menu for 'Settings menu'
* @since 0.1
*/
add_action( 'admin_menu', array($CTA, 'add_captcha_settings_options'));

/**
* Load plugin options on install
* @since 1.0.2
*/
register_activation_hook( __FILE__, array($CTA, 'activate_options' ) );

/**
* Count failed login attempts
* @since 1.1
*/
add_action('wp_login_failed', array($CTA, 'count_failed_login_attempts'));

/**
* Clear failed login attempts on successful login
* @since 1.1
*/
add_action( 'wp_login', array($CTA, 'clear_failed_login_attempts'));

/**
 * Action to call the function to check for Plugin Dependancies
 * @since 1.1.2
 */
add_action( 'admin_init', array($CTA, 'check_plugin_dependancies'));
?>
