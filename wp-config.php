<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'veedolap_veedola');

/** MySQL database username */
define('DB_USER', 'veedolap_veedola');

/** MySQL database password */
define('DB_PASSWORD', 'veedolap_veedola');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'N:.+kU]@(?1y](#6CuN7)e4xQ*e.dII`Wid}Co42kg]@H+!a_p9WRI%aCgi(9EjT');
define('SECURE_AUTH_KEY',  '<C6e(x[Ktk#K7#`b/$#4MC2OLCGNv-w)*-szlTu/EAhk%Gw,4#AqI|hSBG&m`r@Y');
define('LOGGED_IN_KEY',    '%q=MLKEbClpu!BK:ipLytZ4QwHRS*rM_$IL(r7!j#AXps:z#{`nSp2Du[Vy2w02p');
define('NONCE_KEY',        '%^j4wgS%@}w2S`.BemvQ&F?q&gir,zcs:g~NqKb5KN5v,|=:P6,w~at}PL$/MU/m');
define('AUTH_SALT',        '$hX5_p]s^Nj:=[@gC`N)}oMgqC(F?%6Z ?o,06F]N,,( C|9TP2G#sGi0WaJx*BC');
define('SECURE_AUTH_SALT', '(Sw%49lgUPD!nM?=Gs,RpR?]2a!ANp0[Id%9a`N]5r8MX$]EnF^RUANdz %xV9TJ');
define('LOGGED_IN_SALT',   'neAI][r4DE-4${1SYv0p)W_/*Iu$v%575g7Cax/doqn%aX#YUR!Dg)r62-Td1GqW');
define('NONCE_SALT',       'oN#[leyolp?8sq>Y7 ;J=b@@BM,3OqpBcB2~,ye1G*%i)tvJEe=UuVv|qj[g.R@*');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
