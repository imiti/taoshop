<?php
return [
    'adminEmail' => 'admin@example.com',
    //不需要登录的路由
    'notNeedLogin' => [
        'admin/login',
        'admin/captcha',
        
    ],
    'dataCache' => [
        'default' => ['cacheid'=>'cache'],
    ],
];
