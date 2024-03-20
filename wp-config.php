<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', 'content' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          '_)zP!/qn6 ]bJ;k~ F38ui-1[lh--TQ@hW[i-ffPAm>m&f*8h/J,0p[5EJB2}{of' );
define( 'SECURE_AUTH_KEY',   ':+})FNmXOJ)/,;)IkbmTxLvgjeYGqKC^Mlq.x%3qrxP$F0xlW|KM-1?MkejZ|-ow' );
define( 'LOGGED_IN_KEY',     '5k8pSu$;|Fpv(O^_fFUidS=m!g8t6YP-|C^Hn)Y6&WZ~ph3><L)e,gm~8KW|Jh(<' );
define( 'NONCE_KEY',         'par-j !~th!|%X&lc;h:[:40qF&0;@SnrRhy;Y` #VZ2Cjf_M}Sg|oY6$aEVw}ZP' );
define( 'AUTH_SALT',         '[;/I51_YIBha=Rn[%5Zw/-OsR`/Zod8br2!6$UX)?ykVDjbMt09qKb=%Z|$;B`m+' );
define( 'SECURE_AUTH_SALT',  'iLtl`U*3e.MK!q_$2$V,b]FfZk^.Y3/5FA}G- TKLQobT4=ZB8N @V0;|CJM=;-.' );
define( 'LOGGED_IN_SALT',    'acsqMYA9!B>~.$7=Nc^obUV`j}@IR0oT!+MK!akdz5;zLFfIzu]]v?%!nUx49V>:' );
define( 'NONCE_SALT',        'l_ 9e0G ;jp(=f{/}E,*J Md4)vtyK?WytQ>O.N~%|t^O&zj~c`Ww#U%&;/Q0`7j' );
define( 'WP_CACHE_KEY_SALT', '6jJp+il#Zz2)?^I{c{QJ 2z0R*xdyw;BO;6o1p-$Q,,RVAG(08~!T2DwMbK/>;U?' );


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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
