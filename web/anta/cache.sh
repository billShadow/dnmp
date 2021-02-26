#!/bin/bash

#Laravel框架缓存优化

#缓存配置信息
php artisan config:cache

#缓存路由信息
php artisan route:cache

#类映射加载优化
php artisan optimize --force

#composer 自动加载优化。把 PSR-0 和 PSR-4 转换为一个类映射表，来提高类的加载速度
composer dumpautoload -o