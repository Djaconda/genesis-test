<?php

namespace core\console;

use core\app\ErrorHandling;
use core\base\ControllerActionLogger;
use core\contracts\Application;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Mixins\LoggerAccess;
use yii\console\Controller as BaseConsoleController;
use yii\helpers\Console;

/**
 * Base controller for all of the console commands.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Controller extends BaseConsoleController implements ContainerAware, ServiceLocatorAware {
    use LoggerAccess;
    use ControllerActionLogger;
    use ContainerAccess;
    use ServiceLocatorAccess;
    use ErrorHandling;

    protected function printHeaderMessage(string $message): void {
        $this->stdout($message . PHP_EOL, Console::BOLD, Console::BG_BLUE);
    }

    protected function printInfoMessage(string $message): void {
        $this->stdout($message . PHP_EOL, Console::FG_GREEN);
        $this->logInfo($message);
    }

    protected function printWarningMessage(string $message): void {
        $this->stdout($message . PHP_EOL, Console::FG_YELLOW);
        $this->logInfo($message);
    }

    protected function printErrorMessage(string $message): void {
        $this->stdout($message . PHP_EOL, Console::FG_RED);
        $this->logError($message);
    }

    protected function shouldLogAction(): bool {
        return CONSOLE_CONTROLLERS_INFO_LOGGING_ENABLED;
    }
}
