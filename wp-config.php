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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'rF+3F]KCbPwN/PPp{RV<@u0N2MEi6zV{D+wjZ$PMgmsl?w/5yGKH(P-`Q@fu!RrG' );
define( 'SECURE_AUTH_KEY',   'I~6!L?S/$^gL9a=^2{nuwJ4j,4<]C)()Br>mT8Qze.2j0{?`wLe^)K%i[fAr{juV' );
define( 'LOGGED_IN_KEY',     '<n.h8AE_EPm:qA!s[?fVVucsjkiJ+G!on7}R&Mw$q9({EI!OD2Gi[-R_{5[.<M@;' );
define( 'NONCE_KEY',         ',6f|5]:Un6nd/au<UlpSgZcf{7yTQRT~AK`4eB)VWDD~82q>gnOdJTxT]:eoD<Z`' );
define( 'AUTH_SALT',         '3,I`omK|OP!H-,IC%iFm6o7!%|/`4;/o;4=m-*-/50(sXn]`|f*5Z2GtZ&srGI,D' );
define( 'SECURE_AUTH_SALT',  'lW>B<iQ*On^%RP%PpGLb% &A}.e?ge{$P8=.cG%l h,(^I@C1=t-t8^XGi1J0@P_' );
define( 'LOGGED_IN_SALT',    'HOsoDpXC?Jf)0McC~_Suy.`>b-w.AgK#.caD5V~*zFTmoCnu`-~RN!/-+3ylO3c=' );
define( 'NONCE_SALT',        'h?8r_j<}paL,-^WRX[o,J.Nh)%2NjoD{|MiBjS@DV/M4~n]N%:EhRIx,<A2js*Z[' );
define( 'WP_CACHE_KEY_SALT', '~1ewDrbO|-aCPmk`M 1/!:HZSn`:PTg~ _+54P|ED_kn?F+fGhgAczEMNal]wETh' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
// Enable full debugging
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
