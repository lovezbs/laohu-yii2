<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'LaoHu',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/default/login'],//登录的url，权限管理需要，游客访问一些需要权限的url时会往该链接跳转

            //配下面这一块的话，不管是自动登录还是主动登录，都会记录登录时间和IP，但我只要主动登录的
            /*'on beforeLogin' => function($event) {
                $user = $event->identity;
                $user->last_login = time();
                $user->last_ip = $_SERVER['REMOTE_ADDR'];
                $user->save();
            },*/
        ],
        'errorHandler' => [
            'errorAction' => '/core/default/error',//挪地方了
        ],
        //邮箱
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
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
        'db' => require(__DIR__ . '/db.php'),
        //权限管理
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['Guest'],//默认角色
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'defaultRoute' => '/portal/music',//访问入口文件后默认转到的url
    'modules' => [
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu',
            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'idField' => 'user_id',
                    'searchClass' => 'app\models\search\UserSearch',
                ]
            ],
        ],
        //封装和继承一些代码的地方
        'core' => [
            'class' => 'app\modules\core\Module',
        ],
        //后台
        'manage' => [
            'class' => 'app\modules\manage\Module',
        ],
        //前台用户用到的，比如登录、登出、注册、找回密码、修改密码什么的
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
        //前台
        'portal' => [
            'class' => 'app\modules\portal\Module',
        ],
    ],
    //访问控制
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        //允许所有身份访问的路由，开头不要像写url那样加/，亲测无效，无限跳转到登录
        'allowActions' => [
//            'debug/*',
//            'gii/*',
//            'admin/*',
            'core/*',
            'portal/*',
            'user/*',
        ]
    ],
];

/**
 * TODO 因为后台菜单需要，所以需要把站点根目录设置为/web，不会配置的就去学一下吧，以后解决了后台菜单的问题再说
 */
$config['components']['urlManager'] = [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        'login' => '/user/default/login',
        'logout' => '/user/default/logout',
        'register' => '/user/default/register',
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['modules']['debug']['allowedIPs'] = [
        '::1',
        '127.0.0.1',
        '192.168.*.*',
    ];
    //外网专用，有兴趣的去研究一下$_SERVER与header的关系
    if (isset($_SERVER['HTTP_LaoHu'])) {
        $config['modules']['debug']['allowedIPs'] = ['*.*.*.*'];
    };

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
