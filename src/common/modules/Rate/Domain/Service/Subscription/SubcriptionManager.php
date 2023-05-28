<?php

namespace Rate\Domain\Service\Subscription;

use yii\web\ConflictHttpException;

/**
 * Represents sevice for Subcription management
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final readonly class SubcriptionManager {
    public function __construct(private SubcriptionStore $store) {
    }

    public function addEmail(string $email): bool {
        if (!$this->store->isExist($email)) {
            $res = $this->store->add($email);
            if ($res !== false) {
                return true;
            }
        }

        throw new ConflictHttpException('Conflict');
    }

    public function getEmails(): iterable {
        return $this->store->getList();
    }
}
