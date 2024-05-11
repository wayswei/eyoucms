<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

return array(
    'mysql' => [
        '1045:0'        => "数据库账号/密码有误，请查看配置文件 application/database.php 里信息是否正确。",
        '1049:0'        => "数据库不存在，请查看配置文件 application/database.php 里信息是否正确。",
        '2002:0'        => "连接数据库失败，请点击：<a href='http://www.eyoucms.com/wenda/6236.html' target='_blank'>查看教程</a>",
        '2054:0'        => "数据库账号/密码有误，请查看配置文件 application/database.php 里信息是否正确。",
        '2002:2002'     => "连接数据库失败，请查看配置文件 application/database.php 里信息是否正确。",
        '22001:1406'    => "输入过长，已超出最大长度值",
        '42000:1055'    => "数据库sql_mode模式对GROUP BY聚合操作，请点击：<a href='http://www.eyoucms.com/wenda/744.html' target='_blank'>查看教程</a>",
        '42000:1118'    => "当前数据类型的字段已超过数据库允许的最大个数，请添加其他数据类型的字段",
        '42S02:1146'    => "数据表或视图不存在",
        'HY000:1017'    => "数据表或视图不存在",
        'HY000:1030'    => "磁盘临时空间不够导致，请联系空间服务商，进行清空/tmp目录，或者修改my.cnf中的tmpdir参数，指向具有足够空间的目录。",
        'HY000:1045'    => "数据库配置不对，请查看配置文件 application/database.php 里信息是否正确。",
        'HY000:1049'    => "数据库不存在，请查看配置文件 application/database.php 里信息是否正确。",
        'HY000:1615'    => "由于个别空间的数据库配置问题，请点击：<a href='https://www.eyoucms.com/ask/?ct=question&askaid=3771' target='_blank'>查看教程</a>",
        'HY000:2000'    => "数据库配置不对，请查看配置文件 application/database.php 里信息是否正确。",
        'HY000:2002'    => "你的主机不支持 localhost 连接数据，导致报错，请点击：<a href='http://www.eyoucms.com/wenda/5711.html' target='_blank'>查看教程</a>",
        'HY000:2013'    => "可能MySQL服务器不支持127.0.0.1连接，请点击：<a href='http://www.eyoucms.com/wenda/5950.html' target='_blank'>查看教程</a>",
        'HY000:1290'    => "请重启MySql数据库，或者联系空间服务商处理。",
        'HY000:-1366'    => "可能编辑器里存在特殊表情、符号等导致，请点击：<a href='https://www.eyoucms.com/plus/view.php?aid=28325' target='_blank'>查看教程</a>",
    ],
    'exception' => [
        '11602'        => "缓存写入失败，请点击：<a href='https://www.eyoucms.com/ask/list_1_0/5507.html' target='_blank'>查看教程</a>",
    ],
    'wechat' => [
        '-1'    => '系统繁忙，此时请开发者稍候再试',
        '0'     => '请求成功',
        '40001'     => '获取 access_token 时 AppSecret 错误，或者 access_token 无效。请开发者认真比对 AppSecret 的正确性，或查看是否正在为恰当的公众号调用接口',
        '40002'     => '不合法的凭证类型',
        '40003'     => '不合法的 OpenID ，请开发者确认 OpenID （该用户）是否已关注公众号，或是否是其他公众号的 OpenID',
        '40004'     => '不合法的媒体文件类型',
        '40005'     => '不合法的文件类型',
        '40006'     => '不合法的文件大小',
        '40007'     => '不合法的媒体文件 id',
        '40008'     => '不合法的消息类型',
        '40009'     => '不合法的图片文件大小',
        '40010'     => '不合法的语音文件大小',
        '40011'     => '不合法的视频文件大小',
        '40012'     => '不合法的缩略图文件大小',
        '40013'     => '不合法的 AppID ，请开发者检查 AppID 的正确性，避免异常字符，注意大小写',
        '40014'     => '不合法的 access_token ，请开发者认真比对 access_token 的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
        '40015'     => '不合法的菜单类型',
        '40016'     => '不合法的按钮个数',
        '40017'     => '不合法的按钮类型',
        '40018'     => '不合法的按钮名字长度',
        '40019'     => '不合法的按钮 KEY 长度',
        '40020'     => '不合法的按钮 URL 长度',
        '40021'     => '不合法的菜单版本号',
        '40022'     => '不合法的子菜单级数',
        '40023'     => '不合法的子菜单按钮个数',
        '40024'     => '不合法的子菜单按钮类型',
        '40025'     => '不合法的子菜单按钮名字长度',
        '40026'     => '不合法的子菜单按钮 KEY 长度',
        '40027'     => '不合法的子菜单按钮 URL 长度',
        '40028'     => '不合法的自定义菜单使用用户',
        '40029'     => '无效的 oauth_code',
        '40030'     => '不合法的 refresh_token',
        '40031'     => '不合法的 openid 列表',
        '40032'     => '不合法的 openid 列表长度',
        '40033'     => '不合法的请求字符，不能包含 \uxxxx 格式的字符',
        '40035'     => '不合法的参数',
        '40038'     => '不合法的请求格式',
        '40039'     => '不合法的 URL 长度',
        '40048'     => '无效的url',
        '40050'     => '不合法的分组 id',
        '40051'     => '分组名字不合法',
        '40060'     => '删除单篇图文时，指定的 article_idx 不合法',
        '40117'     => '分组名字不合法',
        '40118'     => 'media_id 大小不合法',
        '40119'     => 'button 类型错误',
        '40120'     => '子 button 类型错误',
        '40121'     => '不合法的 media_id 类型',
        '40125'     => '无效的appsecret',
        '40132'     => '微信号不合法',
        '40164'     => '调用接口的IP地址不在白名单中，请在接口IP白名单中进行设置。',
        '89503'     => '此IP调用需要管理员确认,请联系管理员',
        '89501'     => '此IP正在等待管理员确认,请联系管理员',
        '89506'     => '24小时内该IP被管理员拒绝调用两次，24小时内不可再使用该IP调用',
        '89507'     => '1小时内该IP被管理员拒绝调用一次，1小时内不可再使用该IP调用',
    ],
);
