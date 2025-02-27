<?php
// Load Dotenv if not already loaded
require_once __DIR__ . '/vendor/autoload.php';  // Ensure this path is correct
if ( file_exists( __DIR__ . '/.env' ) ) {
    $dotenv = Dotenv\Dotenv::createImmutable( __DIR__ );
    $dotenv->load();
}
foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
    $_SERVER[$key] = $value;
}
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
// Use .env values for WordPress configuration
/** The name of the database for WordPress */
define( 'DB_NAME', getenv('DB_NAME') );
/** Database username */
define( 'DB_USER', getenv('DB_USER') );
/** Database password */
define( 'DB_PASSWORD', getenv('DB_PASSWORD') );
/** Database hostname */
define( 'DB_HOST', getenv('DB_HOST') );
define( 'WP_HOME', getenv('WP_HOME') );
define( 'WP_SITEURL', getenv('WP_SITEURL') );

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
define( 'AUTH_KEY',          'A/AyU1;j^D55S8Az9i[]Vd$5*xl=?s8uq=K/7W:G%VonMA0w[:>r}Fd?KQCF.(ay' );
define( 'SECURE_AUTH_KEY',   '-.=G0^Mu#:fHo[lbGpC$]2R+)JN T <03 r{^mXqShDsooFO4DxcX(s|](t ghsT' );
define( 'LOGGED_IN_KEY',     'a8u=^ XA>B6:%oQS9>Sax`/;:R;Dr4W:Qboi]9A|S?I>B6U97+d_L[gos##^EBw0' );
define( 'NONCE_KEY',         'DFjaK0=iA!nsD1/uH7I+x|&Ud!c=VcA0!h@hX~!.3K#A`.vG]po-Bu/Up8ltZMn*' );
define( 'AUTH_SALT',         '}]QXOfXEe{-eKdpG 6p:AicwKsha>)!9tr1XmUH.g$5*6{E1gqD=GHl_gedbDGdJ' );
define( 'SECURE_AUTH_SALT',  'W+*CW.C*bK#KYFVL}^GaryFcHB69(i(vWv68Rj5EuL[R},vNZ}~.U<q}V1rmN7,A' );
define( 'LOGGED_IN_SALT',    'et4i=^r1+NgEMiJ<nzEM}6N6Q5-<Z-(-}aA)P5-CIFV`%z5o;lFKsLOWRN}^vFwZ' );
define( 'NONCE_SALT',        '=<8g?>GBYY{4Q/(d}n[ZMjU]:}{Ve6_^9>!,lSbxV5iCN}_[?bg6Z(E2d_ZUU{`W' );
define( 'WP_CACHE_KEY_SALT', 'L[%WIDCVnJ*?D!%/<ee+*O(ArDOX4(Y{4gVWme8uG.>OrzqG4xT%O*Dw%5zv@r9K' );


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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);
}

define( 'WP_ENVIRONMENT_TYPE', getenv('WP_ENVIRONMENT_TYPE') );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
