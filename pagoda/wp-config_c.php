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
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* start: custom code */

		/*configuring wp-admin for read-only filesystem */
		define('DISALLOW_FILE_EDIT',true);
		define('DISALLOW_FILE_MODS',true);
		
		/* error reporting */
		ini_set('display_errors', 'On');
		error_reporting(E_ALL | E_NOTICE);
		
		/* custom error handler */
		function pagoda_error_handler($errno, $errstr, $filename, $line) {
			$logfile = '/var/www/logs/WordPress_'.date('Y-m-d').'.log';
			if(!file_exists($logfile)){touch($logfile);}
			$entry = date('[H:i:s]').'[ '.str_pad($errno, 4, ' ', STR_PAD_LEFT).' > '.$filename.' @ '.$line.']'.$errstr."\n";
			file_put_contents($logfile, $entry, FILE_APPEND);
		}
		set_error_handler('pagoda_error_handler');
	
/* end: custom code */

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
?>