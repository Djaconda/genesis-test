<?php

namespace Rate\App\Controller\Console;

use core\console\Controller;
use Rate\Domain\Service\Rate\RateManager;
use yii\helpers\Console;

/**
 * Represents Rate console commands
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final class RateController extends Controller {
    public $defaultAction = 'refresh';

    public function actionRefresh(): void {
        $this->container->create(RateManager::class)->refresh();

        $this->logInfo('Rate updated');
        $this->stdout('Rate updated' . PHP_EOL, Console::BG_GREEN);
    }
}
