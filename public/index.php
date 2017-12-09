<?php

define('PROJECT_PATH',str_replace('\\', '/', realpath(dirname(__FILE__) . '/..')).'/');
define('MY_PATH',PROJECT_PATH.'library/');
if (!defined('APPLICATION_NAME') || APPLICATION_NAME=='') {
	define('APPLICATION_NAME','webshop');
}

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));



// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

//autoload
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setDefaultAutoloader(create_function('$class',
"include str_replace('_', '/', \$class) . '.php';"
));

//die("ok". APPLICATION_PATH);
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();