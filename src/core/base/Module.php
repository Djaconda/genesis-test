<?php

namespace core\base;

use core\api\ApiApplication;
use core\app\ErrorHandling;
use core\contracts\Application;
use core\contracts\WebApplication;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

/**
 * Base module for any application modules.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Module extends BaseModule implements ContainerAware, ServiceLocatorAware {
    use ErrorHandling;
    use ServiceLocatorAccess;
    use ContainerAccess;

    /**
     * Shortcut to allow easy set-up of a module url rules through configuration.
     *
     * @param array $rules url rules being passed to {@link \yii\web\UrlManager::addRules}
     */
    public function setUrlRules(array $rules): void {
        //for WebAplication | ApiApplication UrlManagerConfigurator works instead
        if (!$this->serviceLocator instanceof WebApplication && !$this->serviceLocator instanceof ApiApplication) {
            $this->serviceLocator->getUrlManager()->addRules($rules, false);
        }
    }

    public function behaviors(): array {
        if ($this->serviceLocator instanceof WebApplication && $this->accessRules()) {
            return [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => $this->accessRules(),
                ],
            ];
        }

        return [];
    }

    protected function accessRules(): array {
        return [];
    }

    /**
     * Allows to load module configuration from file and configure current module with loaded configuration.
     * Only files that return configuration array are supported.
     *
     * @param string $filePathOrAlias full path or valid Yii alias to a file contains module configuration.
     */
    protected function loadConfigurationFormFile($filePathOrAlias) {
        $app = $this->serviceLocator;
        $filePath = $app->getAlias($filePathOrAlias);
        // variables for configuration file
        $currentModule = $this;
        $currentModuleId = $this->id;
        if (file_exists($filePath)) {
            $config = require($filePath);
        } else {
            $config = [];
        }
        $app->configureObject($this, $config);

        return $config;
    }

    public function registerRepositories($filePathOrAlias) {
        $app = $this->serviceLocator;
        $filePath = $app->getAlias($filePathOrAlias);
        $repositories = require($filePath);
        foreach ($repositories as $className => $definition) {
            $this->container->setSingleton($className, $definition);
        }
    }
}
