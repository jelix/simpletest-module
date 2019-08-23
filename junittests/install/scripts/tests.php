<?php
/**
* @package  testapp
* @subpackage scripts
* @author       Laurent Jouanneau
* @contributor
* @copyright
*/

require_once ('../application.init.php');

require_once (JELIX_LIB_CORE_PATH.'request/jCmdLineRequest.class.php');

jApp::loadConfig('simpletest/config.ini.php');

$jelix = new jCoordinator();
$jelix->process(new jCmdLineRequest(false, 'junittests'));
