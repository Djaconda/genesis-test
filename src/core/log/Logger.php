<?php

namespace core\log;

use yii\log\Logger as BaseLogger;

/**
 * Extends base logger to provide ability to log messages with trace for any specific use case event if {@link traceLevel}
 * is disabled.
 * Such function useful for exception logging as on production trace level is disabled but for exceptions it's very important to include
 * trace level to message.
 *
 * @package core\log
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Logger extends BaseLogger {
    public $defaultTraceLevel = 7;

    public function logWithTrace($message, $level, $category = 'application') {
        if (!$this->traceLevel && $this->defaultTraceLevel) {
            $oldTraceLevel = $this->traceLevel;
            $this->traceLevel = $this->defaultTraceLevel;
            parent::log($message, $level, $category);
            $this->traceLevel = $oldTraceLevel;
        } else {
            parent::log($message, $level, $category);
        }
    }

    /**
     * Overridden to add $customColumns parameter to be able to specify values for custom columns not only at
     * initialisation stage but also at runtime
     *
     * @param array|string $message
     * @param int $level
     * @param string $category
     * @param array $customColumns
     */
    public function log($message, $level, $category = 'application', array $customColumns = []) {
        $time = microtime(true);
        $traces = [];
        if ($this->traceLevel > 0) {
            $count = 0;
            $ts = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_pop($ts); // remove the last trace since it would be the entry script, not very useful
            foreach ($ts as $trace) {
                if (isset($trace['file'], $trace['line']) && !str_starts_with($trace['file'], YII2_PATH)) {
                    unset($trace['object'], $trace['args']);
                    $traces[] = $trace;
                    if (++$count >= $this->traceLevel) {
                        break;
                    }
                }
            }
        }
        $this->messages[] = [
            $message,
            $level,
            $category,
            $time,
            $traces,
            memory_get_usage(),
            $customColumns,
        ];
        if ($this->flushInterval > 0 && count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }
}
