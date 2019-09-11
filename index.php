<?php

define('APPLICATION_PATH', dirname(__FILE__));

require_once(APPLICATION_PATH.'/vendor/autoload.php');

define('TOP_SDK_WORK_DIR', '/var/log/ali');
define('TOP_SDK_DEV_MODE', true);
define('TOP_AUTOLOADER_PATH', APPLICATION_PATH.'/application/library/Ali');
require_once(APPLICATION_PATH.'/application/library/Ali/TopSdk.php');

define('JDK_AUTOLOADER_PATH', APPLICATION_PATH.'/application/library/Jd');
require_once(APPLICATION_PATH.'/application/library/Jd/JdSdk.php');

$application = new Yaf\Application(APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
