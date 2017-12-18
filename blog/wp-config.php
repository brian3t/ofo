<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
if(stristr($_SERVER['HTTP_HOST'], 'ajcates') || stristr($_SERVER['HTTP_HOST'], 'local')) {
	/** The name of the database for WordPress */
	define('DB_NAME', 'oilfliters-blog');
	
	/** MySQL database username */
	define('DB_USER', 'egghead');
	 
	/** MySQL database password */
	define('DB_PASSWORD', 'rTrapok)1');
	
	/** MySQL hostname */
	define('DB_HOST', 'localhost');
	
	/** Database Charset to use in creating database tables. */
	define('DB_CHARSET', 'utf8');
	
	/** The Database Collate type. Don't change this if in doubt. */
	define('DB_COLLATE', '');
	
} else {
	/** The name of the database for WordPress */
	define('DB_NAME', 'ofoblog');
	
	/** MySQL database username */
	define('DB_USER', 'root');
	
	/** MySQL database password */
	define('DB_PASSWORD', 'rTrapok)1');
	
	/** MySQL hostname */
	define('DB_HOST', 'localhost');
	
	/** Database Charset to use in creating database tables. */
	define('DB_CHARSET', 'utf8');
	
	/** The Database Collate type. Don't change this if in doubt. */
	define('DB_COLLATE', '');

}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'w?0wEB6Xh}YdKpseBbC`-~uGk!joFj@zh}7!rlTdPlqN_fU(4=&=5Ns8Z3Bzj?IN');
define('SECURE_AUTH_KEY',  'R@ ;kGih.)vXBKM8tjL;0ofR&7A+!MEFN+&x#U0W5{[8dTwC8Sp{mR.cOqv^{tb<');
define('LOGGED_IN_KEY',    'e;Vt|ojv7NiV,7n-xomLIf3N<Ahw7fJo?S|]2{T5Z)dIOMf!4R<c O%p|Ud)1}+k');
define('NONCE_KEY',        '`%~8%v-J sM_a>wtMDXmkM~!*B3nsxhlz-CwKV:kc2g1@[)-uQ?X1{ woj4kq*A7');
define('AUTH_SALT',        '-o5R}Tby6<4KW<e<sD?GR7ZmD+l$4e#X_6Q(.|Rw,Ld?Z8qILJT{W*WI,~x~yN*W');
define('SECURE_AUTH_SALT', 'BYoN=}>D?NC[]4rn{%N_UhF`L)MsQ@t&Hp!F@IVE]L==_zJS]S-=0{z=jw8.))>W');
define('LOGGED_IN_SALT',   '5#)=9.R*a{MmVd3%Bv*F8=V_K6w!-JfH+~;QxW&ItWYUszTk_#{[oOF6]aD~sqlC');
define('NONCE_SALT',       'v1rLur-<)OFjuw[R@]G-<>3>n,IKdCZdV24-R2Q2~k}<MbXD2M`G~s%!$d{KMWZE');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
