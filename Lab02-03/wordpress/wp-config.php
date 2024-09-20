<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_db' );

/** Database username */
define( 'DB_USER', 'wp_admin' );

/** Database password */
define( 'DB_PASSWORD', '12345' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'V%dkv~Okrhm*FE)p4ZZs$+*n$tm&[m[[2c ~g0?X:9Tx+p|F( .PGLp_;0`r^}<b' );
define( 'SECURE_AUTH_KEY',  'i_X1Z<QGk@f+TG?vqJiKzMhEK)A;UuWDAa^(IJV9J}A~*cj4>z3L0(3r~_8}][.B' );
define( 'LOGGED_IN_KEY',    'Y,AMq=SryN=fCo;i<=swllG<u}&9Lq0Wr8?QR8jaQQf-Z/9Tl4YyH#<!!Ar7qm>j' );
define( 'NONCE_KEY',        'D=Kg7@^$U`ZhEjs3i6)q9vSgHC[=zue(&/)4]1Q8V(dU*iN&O#(&qn<ZeKd9}67-' );
define( 'AUTH_SALT',        'T.Sv~2cIs;Y]kzaJ6=*e#;eCWQDJra[y/j1be=HS>F=STC7Tsg@xhksu_@5@!3#H' );
define( 'SECURE_AUTH_SALT', '859Y78)ygd/JC:-p.~954^($W}rU2BrrxuiV&1y<^cL9*Kzc5EY}7%/n~$M D7Dr' );
define( 'LOGGED_IN_SALT',   ')DF!ND@ev0m)A_T$KH`fKBSh.YbXk3iqwq.c67J#PjE>{bq_0RDX BP$c02J&ry2' );
define( 'NONCE_SALT',       'P;9E}[2d{3@cIR[F9vAvu/Fbp]`( `KAY.`;?X@:q8)5xVz*N;sRFxOtqsar]aPK' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
