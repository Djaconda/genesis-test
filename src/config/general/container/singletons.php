<?php

use core\api\ApiApplication;
use core\console\ConsoleApplication;
use core\contracts\Application;
use Rate\Domain\Contract\RateClient as RateClientContract;
use Rate\Domain\Service\Rate\RateClient;

return [
    Application::class => function () {
        return Yii::$app;
    },
    ApiApplication::class => function () {
        return Yii::$app;
    },
    ConsoleApplication::class => function () {
        return Yii::$app;
    },
    RateClientContract::class => RateClient::class,
];
