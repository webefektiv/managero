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
define('DB_NAME', 'managero_mdb');

/** MySQL database username */
define('DB_USER', 'managero_muser');

/** MySQL database password */
define('DB_PASSWORD', 'fi%,K6C=zC{r');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_HOME','http://managero.ro');

define('WP_SITEURL','http://managero.ro');

define( 'WP_MEMORY_LIMIT', '2048M' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'QvYyA3xkV6SxvX0joITRFlV7DzYMHa7ywIAqVOLrYw2mzmWve3vhr3Bc3alBEhat');
define('SECURE_AUTH_KEY', 'dlKHTyZczbuFCJy3PYq3FZtSy00fpjnBdR2hco8IPd1t0CK5S55UJ01jezdl4U6G');
define('LOGGED_IN_KEY', 'LhGNv7ILkjoIrIhfRy2eRY3p4SSUkxhPL2U3VRxlzZVpSYvJLmBRPO7aeSL0cF6v');
define('NONCE_KEY', 'iTItzBmPz2SR31HjX2ef0R8KJZLHWxFkjxhWBzg9nxczchEbk8Ib0eXQpSLRfO5o');
define('AUTH_SALT', 'Z8BGgASsVja6lpSHw0TSIr4SdvEjhNqqlu1At9hRFLozPnWeytoK9UeoTaTXcGbl');
define('SECURE_AUTH_SALT', 'zeKxqyWKBcCppUEypLRjoWihWVkYyfARvEk9KKa37E1puHeB7f6XtwZlT4p91zqj');
define('LOGGED_IN_SALT', 'SvfwxdD85Wbb5IgGJvQOtsNy2YHn7hFrHvjuaxpBWsD2RtMXPfkrjuMDhQX5eLHM');
define('NONCE_SALT', 'uHdNR6cJW96WPl4FUuvHWEHtA2fYzUhEuEWLUpS0o5acaRzeiBdffESFsaNCAR3L');

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
