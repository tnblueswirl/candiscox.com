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
define('DB_NAME', 'candiscox');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '10CkD@wN');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

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
define('AUTH_KEY',         'n&^c&NNm@X6CB*hE8Je&!aBgk)<7FvN&^i/&oK5u}/S?r,h{{<~Tm^E&Ax5t<^3w');
define('SECURE_AUTH_KEY',  'Me&eeb[m5n|XX6lbCI_fV,w6$ V[U8wI1,XHZ2mCtTc>M4<msFw<$qKqQ+X7<>>w');
define('LOGGED_IN_KEY',    '*kEqS}1xcV4H7GOG89DHs4-c0T+E=f9hD${` ]Y.dtjQ<PlzRIEEAeCnS46F0Zmb');
define('NONCE_KEY',        'QrIAXHM,]|21/ByqRs_t9XfTXSr5Uq+s$bPxX1` ve;=*s 1B$){hQv4q%J6;idB');
define('AUTH_SALT',        'SPg |R`nO&y=a[;FwQ%y2An=B&@wjZY1j09v9 =bTYwMht)8;*e:4(>/a{fQq@AS');
define('SECURE_AUTH_SALT', 'xL$?Z%.Dr~w|N/70?t}-XwgJ$W]dkGV?O+JjA^HZZtdQaGbRK&^0x0Nqc!|AR+1]');
define('LOGGED_IN_SALT',   'MRt1, )aRmPfeMU]jdZo>?.#>IB*m=?C=]h4TL%tbEUN3d*(OAV+Y&H/(*^#~Wz2');
define('NONCE_SALT',       '(n1N$)Par[oA%.8>/K_zmvsuZ>ja^.?T&9K?^5b%[n{e2dLJ1qi:XtKd0LLu=5DH');

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
