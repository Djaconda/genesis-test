<?php

namespace Rate\Domain\Service\Subscription;

use core\base\Model;

/**
 * Model for vslidation Subscription data
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class SubscriptionModel extends Model {
    public string $email;

    public function rules(): array {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }
}
