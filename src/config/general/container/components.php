<?php

use Psr\Http\Client\ClientInterface;
use yii\caching\CacheInterface;
use yii\caching\FileCache;
use yii\helpers\ArrayHelper;
use yii\mail\MailerInterface;

$devComponents = [];
$prodComponents = [
    CacheInterface::class => FileCache::class,
    ClientInterface::class => GuzzleHttp\Client::class,
    MailerInterface::class => [
        'class' => yii\symfonymailer\Mailer::class,
        'useFileTransport' => true,
        'transport' => [
            'scheme' => 'smtps',
            'host' => 'smtp.mailtrap.io',
            'username' => 'djaconda@mailtrap.io',
            'password' => '@M3VstYqMg!Pu6!',
            'port' => '587',
            'encryption' => 'tls',
            'dsn' => 'native://default',
        ],
        'messageConfig' => [
            'charset' => 'UTF-8',
            'from' => 'noreply@mailtrap.io',
            'bcc' => 'djaconda@mailtrap.io',
        ],
    ],
];

if (YII_ENV_DEV) {
}

return $devComponents ? ArrayHelper::merge($prodComponents, $devComponents) : $prodComponents;
