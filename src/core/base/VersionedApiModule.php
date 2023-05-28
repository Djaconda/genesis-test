<?php

namespace core\base;

use common\ar\user\User as UserIdentity;
use core\api\ApiApplication;
use core\api\User;
use Yii;
use yii\base\Controller;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\User as BaseUser;

/**
 * @TODO FEE-34 need refactoring
 *
 * Versioned API module.
 * During controller instantiation adds version number to the controller namespace based on Accept header
 *
 * Example 1:
 * Accept: application/json; version=1.1
 * will result
 * \User\Account\App\Controller\Api\v1_1\SomeController
 * if this class doesn't exist
 * \User\Account\App\Controller\Api\SomeController
 * will be used instead
 *
 * Example 2:
 * The newest version is always used.
 * If available versions are: '2.0', '1.1', '1.0'.
 * If '2.0' was requested, the version '2.0' will be used.
 * If there is no version in the "Accept" header, the newest version '2.0' will be used.
 *
 *
 * @see Application
 * @see YiiStaticMethodsAccess
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
abstract class VersionedApiModule extends Module {
    public function createControllerByID($id) {
        if (!$this->serviceLocator instanceof ApiApplication) {
            return parent::createControllerByID($id);
        }

        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }

        if ($this->isIncorrectNameOrPrefix($className, $prefix)) {
            return null;
        }

        $className = preg_replace_callback('%-([a-z0-9_])%i', function ($matches) {
                return ucfirst($matches[1]);
            }, ucfirst($className)) . 'Controller';

        $fullClassName = $this->getFullClassName($className, $prefix);

        if (str_contains($fullClassName, '-') || !class_exists($fullClassName)) {
            return null;
        }

        if (is_subclass_of($fullClassName, Controller::class)) {
            $controller = Yii::createObject($fullClassName, [$id, $this]);

            return $controller::class === $fullClassName ? $controller : null;
        }

        if (YII_DEBUG) {
            throw new InvalidConfigException('Controller class must extend from \\yii\\base\\Controller.');
        }

        return null;
    }

    public function getFullClassName(string $className, string $prefix): string {
        $defaultVersion = ($this->serviceLocator->params['apiVersions'] ?? ['1.0'])[0];
        $request = $this->serviceLocator->request;
        $accept = $request->getHeaders()
                          ->get('Accept', "application/json; version=$defaultVersion");
        $acceptParsed = $request->parseAcceptHeader($accept);
        $requestedVersion = $acceptParsed['application/json']['version'] ??
            $acceptParsed['application/xml']['version'] ??
            $defaultVersion;

        $availableVersions = $this->getAvailableVersions($requestedVersion);
        if (!in_array($requestedVersion, $availableVersions, true)) {
            throw new BadRequestHttpException('Wrong API version. Available versions: ' .
                implode(', ', $availableVersions));
        }

        $fullClassName = ltrim(
            $this->controllerNamespace . '\\' .
            str_replace('/', '\\', $prefix) .
            $className, '\\');
        foreach ($availableVersions as $availableVersion) {
            $availableVersion = str_replace('.', '_', (string)$availableVersion);
            $versionedClassName = ltrim(
                $this->controllerNamespace . '\\' .
                str_replace('/', '\\', $prefix) .
                'v' . $availableVersion . '\\' .
                $className, '\\'
            );
            if (class_exists($versionedClassName)) {
                $fullClassName = $versionedClassName;
                $this->replaceDateFormat();
                break;
            }
        }

        return $fullClassName;
    }

    private function replaceDateFormat() {
        // @todo refactor to use this config in config/general/api/main.php
        $identity = $this->serviceLocator->user->getIdentity(false);
        $userConfig = [
            'class' => User::class,
            'identityClass' => UserIdentity::class,
            'enableAutoLogin' => true,
            'enableSession' => false,
        ];
        $this->serviceLocator->set('user', $userConfig);
        $this->serviceLocator->getContainer()->set(BaseUser::class, $userConfig);
        if ($identity) {
            $this->serviceLocator->user->setIdentity($identity);
        }
        // @todo move this part to api/params.php when all API calls use the 'Y-m-d' format
        $this->serviceLocator->params['dateTimeFormats'] = [
            'SQL_DATE' => 'Y-m-d',
            'SQL_TIME' => 'H:i:s',
            'SQL_DATE_TIME' => 'Y-m-d H:i:s',
            'ADMIN_DATE' => 'Y-m-d',
            'ADMIN_TIME' => 'H:i:s',
            'ADMIN_DATE_TIME' => 'Y-m-d H:i:s',
            'MAX_VALID_DATE' => '2050-01-01',
            'MAX_VALID_SERVICE_DATE' => '2037-12-31',
            'MIN_VALID_DATE' => '1970-01-01',
            'XML_IMPORT_FORMAT' => '%Y-%m-%dT%H:%M:%S',
        ];
    }

    private function getAvailableVersions($requestedVersion): array {
        [$requestedMajorVersion, $requestedMinorVersion] = explode('.', (string)$requestedVersion);

        return array_filter(
            $this->serviceLocator->params['apiVersions'] ?? ['1.0'],
            static function ($value) use ($requestedMajorVersion, $requestedMinorVersion) {
                [$restMajorVersion, $restMinorVersion] = explode('.', $value);
                if ($restMajorVersion > $requestedMajorVersion) {
                    return false;
                }
                if ($restMajorVersion === $requestedMajorVersion && $restMinorVersion > $requestedMinorVersion) {
                    return false;
                }

                return true;
            }
        );
    }

    private function isIncorrectNameOrPrefix($className, $prefix): bool {
        if (!preg_match('%^[a-z][a-z0-9\\-_]*$%', (string)$className)) {
            return true;
        }
        if ($prefix !== '' && !preg_match('%^[a-z0-9_/]+$%i', (string)$prefix)) {
            return true;
        }

        return false;
    }
}
