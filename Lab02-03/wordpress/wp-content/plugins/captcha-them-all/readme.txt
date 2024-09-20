=== Captcha Them All ===
Contributors: fuzzguard
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G8SPGAVH8RTBU
tags: antispam, capcha, captcha, captcha numbers, captcha plugin, captcha protection, protection, security, simple captcha, spam blocker, text captcha, web form protection, recaptcha, recapcha, simple recaptcha, recaptcha numbers, recaptcha plugin, recaptcha protection, nospam, lost password, registration, login, comment, securimage, securimage captcha, captchas.net, google recaptcha
Requires at least: 4.7
Tested up to: 6.4.2
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
In any online website, hackers and unscrupulous users will try and attack your website.  Whether it is trying to attack your website by brute forcing login of users accounts, registering bogus user accounts, spamming lost password emails or posting spam comments, this can create quite a headache in the day-to-day operation of you WordPress site.

A way to fix this issue is to use a Captcha protection service.  This plugin offers multiple different captcha services for you to use to protect your WordPress site from unscrupulous users.

This plugin also fully supports the [WooCommerce Core Plugin](https://wordpress.org/plugins/woocommerce/), and [Theme My Login](https://en-au.wordpress.org/plugins/theme-my-login/) plugins.


= NEW CAPTCHA TYPE =
[VisualCatpcha] (http://visualcaptcha.net/) has been added as a new captcha service.  It is built-in and does not require any third-party API's for validation

= NEW OPTION =
* Captcha protection can now be added for login for every login, or on first, second, third, forth, fifth failed attempt.  Setting is in admin settings Settings->Captcha Them All
* Honeypot protection has been added to all submitted forms for extra spam protection

= Captcha services offered in this plugin =
        * [Google Recaptcha] (https://www.google.com/recaptcha/intro/index.html)
	    * [Visual Captcha] (http://visualcaptcha.net/)
        * [Securimage Captcha] (https://www.phpcaptcha.org/)
        * [Captchas.net] (https://captchas.net/)
Muliple Options are available for all the different Captcha types.


= Supported WordPress Core Components =
        * Login Form
        * AJAX Login Form
        * Registration Page
        * Lost Password Form
        * Comments Form (Does not display for Logged in User)


= WooCommerce Supported Protection =
This plugin also fully supports the [WooCommerce Core Plugin](https://wordpress.org/plugins/woocommerce/), and not any other WooCommerce extensions.  The components in the WooCommerce Core are below:

        * Login Form
        * AJAX Login Form
        * Registration Page
        * Lost Password Form

= Theme My Login Supported Protection =

        * Login Form
        * AJAX Login Form
        * Registration Page
        * Lost Password Form

= Contact Form Protection =
This plugin now supports the following Contact Forms:

        * Contact Form 7


= Google Recatpcha Language Support =
Captcha Them All supports all languages for Google Recaptcha.  The language of the local WordPress website will be used.  Supported languages are shown [here](https://developers.google.com/recaptcha/docs/language)


= Future Captcha services to be provided =
        * Fun Captcha (http://www.funcaptcha.com/)


== Installation ==

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' area in WordPress
1. Go to Settings -> Captcha Them All
1. Select the areas to protect with captcha. Login Form, Register Form, Lost Password Form, Comments Form
1. Choose type of captcha to use
1. Fill out any required fields for the captcha type selected
1. Click 'Save Settings' button

== Frequently Asked Questions ==

= Language is still in English on Google Recaptcha =
This is commonly because the Language you are trying to use is not supported by Google Recaptcha service.  Please see the list of supported Google Recatpcha languages [here](https://developers.google.com/recaptcha/docs/language)



If your language is supported, make sure the language is set to the one you want in WordPress Admin Panel ***Settings -> General, 'Site Language'** setting.  If this is set to your language and it is still not displaying the correct language for Google Recaptcha, please make a [Support Ticket](https://wordpress.org/support/plugin/captcha-them-all)



= I see the word 'ReCaptcha' but I dont see a Google Recaptcha box? =
This is generally because the **'Site Key'** or **'Secret Key'** for Google Recaptcha in **Settings -> Captcha Them All** is missing or invalid.  Fix this setting and the box should display.



= I get the error 'Error when parsing configuration: Unknow client:' for the image when displayed for Captchas.Net =
This happens because the **Captchas.Net username** in **Settings -> Captcha Them All** is missing or invalid.



= I always see 'Captcha Validation Failed' when using Captchas.Net =
This is most likely because your **Captchas.Net secret key** in **Settings -> Captcha Them All** is missing or invalid.



= It said this plugin supports WooCommerce?  But I can't see the Captcha box!!  =
This plugin also fully supports the [WooCommerce Core Plugin](https://wordpress.org/plugins/woocommerce/), and not any other WooCommerce extensions.  The components in the WooCommerce Core are below:

        * Login Form
        * Registration Form
        * Login Form and Registration Form when on the same page
        * Product Review Form (Does not display for Logged in User)
        * Lost Password Form
        * Checkout Login Form

If you would like to see support for a WooCommerce Extension added, please contact us at support@fuzzguard.com.au or make a [Support Ticket](https://wordpress.org/support/plugin/captcha-them-all)



= I see no Captcha box on my Login Form, Registration Form, Lost Password Form or Comment Form =
Please make sure the checkboxes for the areas you want protected with Captcha are selected in **Settings -> Captcha Them All**.  The particular area of WordPress will only be protected if you select the corresponding checkbox.  The available areas to be protected are:

        * Login Form
        * Registration Form
        * Lost Password Form
        * Comment Form



= I'm a Plugin Developer and I want to have Captcha Them All supported. =
Are you a plugin developer and would like to have Captcha Them All support your existing or developing plugin? It can be a Contact Form, Support Ticket System, Product Review, Tesimonials or anything else you need protection from Hacking and unscrupulous users.  If so then contact us at support@fuzzguard.com.au or make a [Support Ticket](https://wordpress.org/support/plugin/captcha-them-all)



= Im having major issues with this plugin and I need help!!! =
Don't Panic!!  We can help!!  Just make a [Support Ticket](https://wordpress.org/support/plugin/captcha-them-all) and we will get back to you ASAP!

== Screenshots ==

1. Login Form
2. Registration Form
3. Lost Password Form
4. Comments Form
5. VisualCaptcha
6. Securimage Captcha
7. Captchas.net Captcha
8. Admin Options

== Changelog ==
= 1.4.2 =
* Fixed issue where Math or String could not be chosen from the Secure Image (Built-In Captcha)
* Changed Placeholder text from "Enter Code Above..." to "Answer" when using Math option in Secure Image (Built-In Captcha)

= 1.4.1 =
* Fixed CSS height issue for Google Captcha Verification Image popup.  iFrame will now sit at 100% of the proper height instead of 580px.  This was causing issues for websites with Languages other than English.


= 1.4 =
* Fixed issue with XSS (Vunerability discovered by Yuki Haruma - Patched v1.4)
* Fixed PHP 8.2 compatibility (string{i} to substr($string, $i, 1) - Fix provided by egingell@gmail.com - Patched v1.4)
* Added settings link to plugin page

= 1.3.3 =
* Fixed issue when uninstalling plugin

= 1.3.2 =
* Fixed potential security issue identified by Alexander Svechkaryov from 86com for SecurImage captcha type

= 1.3.1 =
* Fixed file paths

= 1.3 =
* NEW CAPTCHA TYPE ADDED: [Visual Captcha] (https://visualcaptcha.net/)
* Moved generation of SecurImage image file from PHP file to wordpress function.  Conforms better to wordpress standards
* Removed .htaccess file from captcha-them-all/securimage/ as the SecurImage is not generated by PHP wordpress function.
* Added in securImage 'word' generation.  So options for captcha now are: Random String (Default), Math challenge, or Word challenge
* Added honeypot protection for captcha on all submits.  Compliments captcha protection on all protected forms.
* Added in ability to see IP addresses that have had failed attempts.  This DB will not be filled up if you use "Show on every login" option.  Will also only show failed addresses from the last 15 days.
* Added in ability to clear the IP addresses from the log.
* Fixed issue from [Wordpress.org] (https://wordpress.org/support/topic/notices-17/)
* Added in new [WooCommerce version check] (https://woocommerce.wordpress.com/2017/08/28/new-version-check-in-woocommerce-3-2/) and tested plugin with WooCommerce version 3.3.2
* Fixed woocommerce registration form to make sure captcha check is done before email exists check
* Fixed woocommerce registration on checkout where the captcha them all form would not display

= 1.2 =
* Fixed issue with CF7 not displaying on '[cta_recaptcha* cta_recaptcha]' shortcode
* Fixed validation for captcha code when Contact Form 7 is displayed.
* Fixed response codes for Contact Form 7 responses - Removed HTML tags
* Changed WooCommerce Hook 'allow_password_reset' to 'lostpassword_post' as WooCommerce added my suggestion to respect same format for 'lostpassword_post' as WP Core does.  See on [GitHub] (https://github.com/woocommerce/woocommerce/pull/13558)
* Split WooCommerce registration form hook to 'woocommerce_register_form' instead of 'register_form' as this conforms more correctly to WooCommerce Documentation.
* Added code to check for 'Google Captcha (reCAPTCHA) by BestWebSoft' plugin and give warning notice as plugins can conflict.

= 1.1.1 =
* Updated admin menu options to properly run 'activate_options' function when plugin is updated.  Not just on initial activation of plugin

= 1.1 =
* Changed filter line from "add_filter( 'wp_authenticate_user', array($CTA, 'CTA_validate_captcha_login'), 10, 2 );" to "add_filter( 'authenticate', array($CTA, 'CTA_validate_captcha_login'), 10, 3 );" to validate captcha response BEFORE password and username are checked (Better Security)
* Changed "add_action( 'register_post', array($CTA, 'CTA_validate_captcha_register'), 10, 3 );" to "add_filter( 'registration_errors', array($CTA, 'CTA_validate_captcha_register'), 10, 3 );" (More correct check for validation errors for registrations page)
* Changed "$errors->add( 'denied', __( '<strong>ERROR</strong>: Captcha validation failed.' ) );" to "$errors = new WP_Error( 'denied', __( '<strong>ERROR</strong>: Captcha validation failed.' ) );" so the errors returned to the user for registrations are overwritten unless captcha is validated (Better Security - Malicious users cannot hit website to discover valid email addresses or usernames) - DOES NOT WORK FOR WOOCOMMERCE.  DUE TO WAY THEY VALIDATE EMAIL ADDRESSES
* Added in support to show login captcha on every login, or only on 1-5 failed attempts - New option added to settings page.  Default option is 'every login'
* Added DB table %prefix%_CTA_ip_failed_atts to track failed IP logins
* Added check to clear the failed login attempts per IP from DB after 15 days since last failed attempt (Keeps DB cleaner)

= 1.0.2 =
* Theme My Login support
* Removed static support for Contact Form 7.  Use shortcode [cta_recaptcha* cta_recaptcha] in contact form.
* Removed protection option call "Contact Form 7" in admin area.  Made reference to [cta_recaptcha* cta_recaptcha] shortcode.
* Added option for changing SecurImage Captcha type for either Text or Math Problem Captcha

= 1.0.1 =
* Added in support for Contact Form 7.  Does not use NATIVE Contact Form 7 reCAPTCHA code, but uses Captcha Them All code.
* Added another protection option call "Contact Form 7" in admin area.  Default state is OFF
* Changed "Phonetic spelling (mp3)" link to use target="_BLANK" so it opens in a new window.
* Added .htaccess file to captcha-them-all/securimage/ to allow access to captcha-them-all/securimage/securimage_show.php as SUCURI and other security plugins blocked access to this file.
* Added check for securimage signature colour.  If this was not set the plugin would fail to load.

= 1.0 =
* Gold release
