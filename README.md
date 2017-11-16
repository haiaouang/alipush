# alipush
[![Latest Stable Version](http://www.maiguoer.com/haiaouang/alipush/stable.svg)](https://packagist.org/packages/haiaouang/alipush)
[![License](http://www.maiguoer.com/haiaouang/alipush/license.svg)](https://packagist.org/packages/haiaouang/alipush)

laravel阿里推送包

## 安装

在你的终端运行以下命令

`composer require haiaouang/alipush`

或者在composer.json中添加

`"haiaouang/alipush": "1.0.*"`

然后在你的终端运行以下命令

`composer update`

安装依赖包 [haiaouang/support](https://github.com/haiaouang/support)

安装依赖包 [haiaouang/pusher](https://github.com/haiaouang/pusher)


设置推送信息的参数 config/pushers.php

## 调用

修改config/pushers.php对应的配置

```php
<?php

return [
    'launchers' => [
        'alipush' => [
            'driver' => 'alipush',

            'url' => '',

            'action' => '',

            'format' => '',

            'version' => '',

            'region_id' => '',

            'access_key' => '',
            
            'access_key_secret' => '',
            
            'android' => [
                //包名
                'bundle_id' => '',
                //app id
                'app_id' => '',
                //app key
                'app_key' => '',
                //app secret
                'app_secret' => ''
            ],

            'ios' => [
                //包名
                'bundle_id' => '',
                //app id
                'app_id' => '',
                //app key
                'app_key' => '',
                //app secret
                'app_secret' => ''
            ],
            
            //前缀
            'prefix' => env('PUSH_PREFIX' , 'test_')
        ]
    ],
];
```

## 依赖包

* haiaouang/support : https://github.com/haiaouang/support
* haiaouang/pusher : https://github.com/haiaouang/pusher
