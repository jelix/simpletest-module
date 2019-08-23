<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2019 Laurent Jouanneau
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
use \Jelix\Installer\Module\API\ConfigurationHelpers;

class junittestsModuleConfigurator extends \Jelix\Installer\Module\Configurator {

    public function configure(ConfigurationHelpers $helpers)
    {
        // install the simpletest.php script
        $helpers->createEntryPoint('scripts/tests.php', 'simpletest',
            'simpletest/config.ini.php',
            'cmdline', 'scripts/configtests.ini.php');

    }
}