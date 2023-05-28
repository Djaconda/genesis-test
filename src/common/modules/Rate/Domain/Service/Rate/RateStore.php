<?php

namespace Rate\Domain\Service\Rate;

use Yii;

/**
 * Represents sevice for keeping Rate data
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final readonly class RateStore {
    private string $path;

    public function __construct() {
        $this->path = Yii::getAlias('@data') . DIRECTORY_SEPARATOR . 'rate.txt';
    }

    public function get(): float {
        return $this->getLastRate();
    }

    public function save(float $rate): bool {
        $data = date('d-m-Y H:i' . ' | ' . $rate . PHP_EOL);

        return file_put_contents($this->path, $data, FILE_APPEND | LOCK_EX);
    }

    public function clear(): bool {
        return file_put_contents($this->path, '', LOCK_EX);
    }

    private function getLastRate(): float {
        $file = fopen($this->path, 'rb');
        $line = '';
        if ($file) {
            fseek($file, -2, SEEK_END);
            while (($char = fgetc($file)) !== false) {
                if ($char === "\n") {
                    break;
                }
                $line = $char . $line;
                fseek($file, -2, SEEK_CUR);
            }
            fclose($file);
        }
        $data = array_filter(explode('|', $line));
        if ($data) {
            [$time, $rate] = $data;
        }

        return isset($rate) ? (float)trim($rate) : 0;
    }
}
