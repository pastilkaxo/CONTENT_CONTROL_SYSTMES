<?php
/**
* Don't display if wordpress admin class is not found
* Protects code if wordpress breaks
* @since 0.2
*/
if ( ! function_exists( 'is_admin' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

require(dirname(__FILE__).'/captcha-them-all.php');

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$CTA = new CaptchaThemAll();

delete_option( $CTA->$CTA_opt_name );

// For site options in multisite
delete_site_option( $CTA->$CTA_opt_name  );  

global $wpdb;
$table = $wpdb->prefix.$CTA->CTA_ipFailedAttTbl;
if($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table ){
	$wpdb->query( "DROP TABLE IF EXISTS `$table`");
}

?>
