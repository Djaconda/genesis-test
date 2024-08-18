<?php

namespace Rate\Domain\Service\Subscription;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Represents a service for keeping Subscription data
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final readonly class SubscriptionStore {
    private string $emailBucket;

    public function __construct() {
        $this->emailBucket = Yii::getAlias('@data') . DIRECTORY_SEPARATOR . 'emails';
    }

    public function add(string $email): false|int {
        return file_put_contents($this->getFilePathByEmail($email), '', LOCK_EX);
    }

    public function isExist(string $email): bool {
        return is_file($this->getFilePathByEmail($email));
    }

    public function getList(): iterable {
        $directory = Yii::getAlias('@data/emails');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                yield $file->getFilename();
            }
        }
    }

    private function getFilePathByEmail(string $email): string {
        $subDir = $this->getSubDirPathByEmail($email);

        if (!is_dir($subDir)) {
            $this->createSubFolder($subDir);
        }

        return $subDir . DIRECTORY_SEPARATOR . $email;
    }

    private function getSubDirPathByEmail(string $email): string {
        preg_match('/^([^@]+)@(.+)$/', $email, $matches);
        $domain = $matches[2] ?? '';

        return implode(DIRECTORY_SEPARATOR, array_filter([$this->emailBucket, $domain]));
    }

    private function createSubFolder(string $directory): void {
        if (!mkdir($directory, 0755) && !is_dir($directory)) {
            throw new ServerErrorHttpException(sprintf('Directory "%s" was not created', $directory));
        }
    }
}
