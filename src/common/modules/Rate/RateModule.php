<?php

namespace Rate;

use Client\CustomerPortal\Config\Provider\CustomerPortalServiceProvider;
use Client\Profile\App\Daemon\DuplicatesHandler;
use Client\Profile\Domain\Di\PhotoProcessorServiceProvider;
use core\api\ApiApplication;
use core\base\VersionedApiModule;
use core\console\ConsoleApplication;

/**
 * Represents User module in the system
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RateModule extends VersionedApiModule {
    public $controllerNamespace = 'Rate\App\Controller';

    public function init(): void {
        parent::init();

        $app = $this->serviceLocator;
        if ($app instanceof ConsoleApplication) {
            $this->loadConfigurationFormFile(__DIR__ . '/Config/console.php');
        } elseif ($app instanceof ApiApplication) {
            $this->loadConfigurationFormFile(__DIR__ . '/Config/api.php');
        }
    }
}
