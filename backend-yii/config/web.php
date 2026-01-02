<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'api/datasets/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'UFNsdOtr6swkOklGfCKpplNmgl1TC-63',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'errorHandler' => [
            'errorAction' => null,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST api/auth/login' => 'api/auth/login',
                'GET api/datasets' => 'api/datasets/index',
                'GET api/datasets/<id:\\d+>' => 'api/datasets/view',
                'GET api/categories' => 'api/categories/index',

                'GET api/admin/dashboard-summary' => 'api/admin-dashboard/summary',
                'GET api/admin/rekapan-user' => 'api/admin-dashboard/rekapan-user',

                'GET api/admin/categories' => 'api/admin-categories/index',
                'POST api/admin/categories' => 'api/admin-categories/create',
                'DELETE api/admin/categories/<id:\\d+>' => 'api/admin-categories/delete',

                'GET api/admin/users' => 'api/admin-users/index',
                'GET api/admin/users/<id:\\d+>' => 'api/admin-users/view',
                'PATCH api/admin/users/<id:\\d+>/freeze' => 'api/admin-users/freeze',
                'PATCH api/admin/users/<id:\\d+>/unfreeze' => 'api/admin-users/unfreeze',
                'DELETE api/admin/users/<id:\\d+>' => 'api/admin-users/delete',

                'GET api/admin/datasets' => 'api/admin-datasets/index',
                'POST api/admin/datasets' => 'api/admin-datasets/create',
                'GET api/admin/datasets/<id:\\d+>' => 'api/admin-datasets/view',
                'POST api/admin/datasets/<id:\\d+>/approve' => 'api/admin-datasets/approve',
                'POST api/admin/datasets/<id:\\d+>/update' => 'api/admin-datasets/update',
                'DELETE api/admin/datasets/<id:\\d+>' => 'api/admin-datasets/delete',

                'POST api/admin/profile' => 'api/admin-profile/update',

                'OPTIONS api/<path:.*>' => 'api/auth/options',
            ],
        ],
    ],
    'modules' => [
        'api' => [
            'class' => 'app\\modules\\api\\Module',
        ],
    ],
    'params' => $params,
];

$config['on beforeRequest'] = function () {
    $path = Yii::$app->request->pathInfo;
    if ($path === '' || strncmp($path, 'api', 3) !== 0) {
        Yii::$app->response->statusCode = 404;
        Yii::$app->end(json_encode([
            'success' => false,
            'error' => 'Not Found',
        ]));
    }
};

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
