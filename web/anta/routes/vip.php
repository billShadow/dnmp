<?php

use Illuminate\Http\Request;


# 用户注册相关路由
Route::group(['prefix'=>'user'], function() {
    Route::any('regist', 'Vip\UserController@regist');                  #注册用户
    Route::any('usecode', 'Vip\UserController@usecode');                  #注册用户
    Route::any('test22', 'Vip\UserController@test22');                  #注册用户

    #debug
    //Route::post('makeqrcode', 'Vip\UserController@makeqrcode');         //二维码
    Route::any('token', 'Vip\UserController@token');          #获取注册用户测试tokne

    Route::post('actAlert', 'Vip\UserController@actAlert');                #活动弹框
});

# 活动相关路由
Route::group(['prefix'=>'user', 'middleware'=>['api.token']], function() {
    Route::any('setPhone', 'Vip\UserController@setPhone');                 #授权电话号码接口
    Route::any('index', 'Vip\UserController@index');                       #抽签首页接口
    Route::any('userinfo', 'Vip\UserController@userinfo');                 #获取用户信息
    Route::any('room', 'Vip\UserController@room');                         #玩咖空间列表接口
    Route::any('support', 'Vip\UserController@support');                   #玩咖点赞
    Route::any('comment', 'Vip\UserController@comment');                   #玩咖评论
    Route::any('mycard', 'Vip\UserController@mycard');                     #我的卡券
    Route::any('moneylist', 'Vip\UserController@moneylist');               #金币明细
    Route::any('levellist', 'Vip\UserController@levellist');               #成长值明细
    Route::post('toprize', 'Vip\UserController@toprize');                  #抽奖
    Route::post('tasklist', 'Vip\UserController@tasklist');                #任务中心
    Route::post('goodslist', 'Vip\UserController@goodslist');              #商城
    Route::post('exchange', 'Vip\UserController@exchange');                #商城兑换
    Route::post('dotask', 'Vip\UserController@dotask');                    #做任务
    Route::post('gettaskprize', 'Vip\UserController@getTaskPrize');        #获取任务奖励
    Route::post('levelgift', 'Vip\UserController@levelgift');              #等级礼物列表
    Route::post('prizelist', 'Vip\UserController@prizelist');              #抽奖的转盘数据接口
    Route::post('commentSupport', 'Vip\UserController@commentSupport');    #玩咖评论点赞
    Route::post('getlevelgift', 'Vip\UserController@getlevelgift');        #获取等级礼物，金币
    Route::post('skipApp', 'Vip\UserController@skipApp');                  #跳转app记录task_history
    Route::post('pushCard', 'Vip\UserController@pushCard');                #领取券
//    Route::post('actAlert', 'Vip\UserController@actAlert');                #活动弹框
    Route::post('actGet', 'Vip\UserController@actGet');                    #活动领取券
    Route::post('globalAlert', 'Vip\UserController@globalAlert');          #alert
    Route::any('getGlobalCard', 'Vip\UserController@getGlobalCard');       #get alert card

    //地址相关操作
    Route::post('editreceiv', 'Vip\AddressController@editreceiv');          #地址修改
    Route::post('addreceiv', 'Vip\AddressController@addreceiv');            #地址添加
    Route::post('listreceiv', 'Vip\AddressController@listreceiv');          #地址列表

    Route::any('xiaobin', 'Vip\UserController@xiaobin');
    Route::any('pushvipcard', 'Vip\UserController@pushvipcard');

});

# 活动相关路由
Route::group(['prefix'=>'test'], function() {
    Route::any('del', 'Vip\TestController@delRedis');
});

# 提供给anta调用接口
Route::group(['prefix'=>'api'], function() {
    Route::post('getmoney', 'Vip\UserController@getmoney');
    Route::post('decmoney', 'Vip\UserController@decmoney');
    Route::post('incmoney', 'Vip\UserController@incmoney');
    Route::post('userpay', 'Vip\UserController@userpay');
    Route::post('usecard', 'Vip\UserController@usecard');
    Route::post('info', 'Vip\UserController@info');
});