<?php

namespace core\base;

use yii\base\ActionEvent;
use yii\base\Controller;
use yii\log\Logger;

/**
 * Common event handlers for controllers.
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait ControllerActionLogger {
    public function init(): void {
        if ($this->shouldLogAction()) {
            $this->attachLogHandlers();
        }
        parent::init();
    }

    protected function shouldLogAction(): bool {
        return true;
    }

    protected function attachLogHandlers(): void {
        $app = $this->serviceLocator;
        $category = static::class;
        $this->on(Controller::EVENT_BEFORE_ACTION, function (ActionEvent $event) use ($app, $category) {
            $app->getLogger()
                ->log('Action: "' . $event->action->id . '" started', Logger::LEVEL_INFO, $category);
        });
        $this->on(Controller::EVENT_AFTER_ACTION, function (ActionEvent $event) use ($app, $category) {
            $app->getLogger()
                ->log('Action: "' . $event->action->id . '" ended', Logger::LEVEL_INFO, $category);
        });
    }
}
