<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id' => 'vacancies-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower'   => '@vendor/bower-asset',
        '@npm'     => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'test-dev-key-not-for-prod',
            'parsers' => [
               
                'application/json' => \yii\web\JsonParser::class,
            ],
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'db' => $db,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                
                [
                    'class' => \yii\rest\UrlRule::class,
                    'controller' => 'vacancy',
                    'pluralize' => true,  // /vacancies вместо /vacancy
                    'prefix' => 'api',
                    'only' => ['index', 'view', 'create'],  // GET list, GET one, POST create
                ],
               
                'GET /api/health' => 'site/health',
                'GET /site/error' => 'site/error',
            ],
        ],
    ],
    'controllerNamespace' => 'app\\controllers',
    'params' => $params,
];

if (YII_ENV_DEV || YII_DEBUG) {

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.*.*', '172.*.*.*'],
    ];
}

return $config;
