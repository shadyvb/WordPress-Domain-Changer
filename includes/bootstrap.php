<?php
/**
 * Bootstrap
 */

/* Environment */
if(in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1'))) {
  define('ENV', 'development');
} else {
  define('ENV', 'production');
}

/* Paths */
define( 'WPDC_PATH'     , realpath( dirname(__FILE__) . '/../') );
define( 'INCLUDES_PATH' , WPDC_PATH     . '/includes');
define( 'CLASSES_PATH'  , INCLUDES_PATH . '/classes');
define( 'VIEWS_PATH'    , WPDC_PATH     . '/views');
define( 'LAYOUTS_PATH'  , VIEWS_PATH    . '/layouts');
define( 'ASSETS_PATH'   , WPDC_PATH     . '/assets');

/* URLs */
define( 'WPDC_URL' ,  '');


/* Cookies */
define('WPDC_COOKIE_NAME_AUTH'    , 'WPDC_COOKIE_AUTH');
define('WPDC_COOKIE_NAME_EXPIRE'  , 'WPDC_COOKIE_EXPIRE');
define('WPDC_COOKIE_LIFETIME'     , 60 * 5); // Five Minutes




// Load Classes
require_once CLASSES_PATH . '/SerializedString.php';
require_once CLASSES_PATH . '/WordPressConfigFile.php';
require_once CLASSES_PATH . '/WordPressDatabase.php';
require_once CLASSES_PATH . '/ViewHelper.php';

require_once CLASSES_PATH . '/WordPressDomainChanger.php';

// Load Simple Configuration File
require_once WPDC_PATH . '/config.php';

// Load Simple Functions File

?>
