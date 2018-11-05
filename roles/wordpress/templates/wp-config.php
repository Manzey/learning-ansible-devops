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
define('DB_NAME', '{{db_databaseName}}');

/** MySQL database username */
define('DB_USER', '{{db_user}}');

/** MySQL database password */
define('DB_PASSWORD', '{{db_password}}');

/** MySQL hostname */
define('DB_HOST', '{{db1_fixed_ip}}');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '@,+LI6e+P&XY~|67{),HZ+nFtKQ&1w]y]T=jE4}su-jw4Kg#:^WF.*%keZFY_,oI');
define('SECURE_AUTH_KEY',  'mI+pq(]4g3hV1NN_COk=ufW_#ze4Qk*>vnrl|^:lr[ty|w4m%F}rxvZQG0lSV||1');
define('LOGGED_IN_KEY',    '=h;zv}ljI`q/Cdek$[G$Y>$k6wJ}U6so/8d&pkd,o#lY3Rl!Nw#+*)A)z|33opjI');
define('NONCE_KEY',        ' Xbr.vw-548!@LLi^)5Q+B>lnHyTyo7!8Ntxh-tW@ZyE:~ypAU%-i-R(X,XD]<o/');
define('AUTH_SALT',        'p}Ko``45-2}<Hs}iTeJYY6Wg$a-[|-BJy:.nQ-}>YRZ}bGlYT<3.^#|;`A@^fhiL');
define('SECURE_AUTH_SALT', 'hIMK>(UZ!d{mWu.L%mh,49N!>_?9MO  3 sQId|VVDQFlzb.$ok0X/UQl=|n$5Un');
define('LOGGED_IN_SALT',   '_@9~R7prg/G?NF(>N{*%_0K+8ny~Z!%xMlM9oo4=V#5m}~Z_t86Nl-.];U8E!NBF');
define('NONCE_SALT',       's.2$:Xz?(zH{i-2eVXV>l! DEpwa;=((4x#Gj^{?2/dN=x1Fg[B=AYLS8&V!T:iK');

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
