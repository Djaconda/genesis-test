<?php

use User\Account\AccountModule;

/**
 * @var $currentModule AccountModule
 * @var $currentModuleId string
 */

return [
    'controllerNamespace' => 'Rate\App\Controller\Api',
    'defaultRoute' => 'rate',
    'urlRules' => [
        "GET,OPTIONS rate" => "{$currentModuleId}/rate",
        "POST,OPTIONS subscribe" => "{$currentModuleId}/subscription",
        "POST,OPTIONS sendEmails" => "{$currentModuleId}/notification",
    ],
];
