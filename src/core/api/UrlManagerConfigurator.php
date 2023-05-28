<?php

namespace core\api;

use Composer\Autoload\ClassLoader;
use core\frontend\UrlManagerConfigurator as BaseUrlManagerConfigurator;

/**
 * Represents service for configuring urlManager component
 *
 * @property string $urlRulesCachePath
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class UrlManagerConfigurator extends BaseUrlManagerConfigurator {
    public function __construct(ClassLoader $loader, Application $application) {
        $this->loader = $loader;
        $this->application = $application;
    }

    protected function getConfigPath(string $moduleClass): string {
        $path = dirname(realpath($this->loader->findFile($moduleClass)));
        $configName = '/api.php';
        $configPath = $path . '/Config' . $configName;

        if (file_exists($configPath)) {
            return $configPath;
        }

        return $path . '/Config/Api' . $configName;
    }
}
