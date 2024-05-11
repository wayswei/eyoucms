<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-2-20
 */
namespace app\admin\model;

use think\Db;
use think\Model;

/**
 * 会员
 */
class Member extends Model
{   
    public $name = 'users';
    private $admin_lang = 'cn';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->admin_lang = get_admin_lang();
    }

    // 判断会员属性中必填项是否为空
    // 传入参数：
    // $post_users:会员属性信息数组
    // return 为空项
    public function isEmpty($post_users = [])
    {
        if (empty($post_users)) {
            return false;
        }
        // 会员属性
        $where = array(
            'lang'        => $this->admin_lang,
            'is_hidden'   => 0, // 是否隐藏属性，0为否
            'is_required' => 1, // 是否必填属性，1为是
        );
        $para_data =Db::name('users_parameter')->where($where)->field('title,name')->select();

        // 判断提交的属性中必填项是否为空
        foreach ($para_data as $value) {
            if (isset($post_users[$value['name']])) {
                $attr_value = trim($post_users[$value['name']]);
                if (empty($attr_value)) {
                    return $value['title'].'不能为空！';;
                }
            }
        }
    }

    // 判断邮箱和手机是否存在
    // 传入参数：
    // $post_users:会员属性信息数组
    // $users_id:会员ID，注册时不需要传入，修改时需要传入。
    // return 存在项
    public function isRequired($post_users = [],$users_id=0)
    {
        if (empty($post_users)) {
            return false;
        }
        
        // 匹配手机和邮箱数据
        $where_1 = [
            'is_system'=> 1,
            'lang'     => $this->admin_lang,
        ];
        $where_1[] = Db::raw(" ( name LIKE 'email_%' OR name LIKE 'mobile_%' ) ");
        $users_parameter = Db::name('users_parameter')->where($where_1)->field('para_id,title,name')->getAllWithIndex('name');

        // 判断手机和邮箱格式是否正确
        foreach ($post_users as $key => $val) {
            if (preg_match('/^email_/i', $key)) {
                if (!empty($val) && !check_email($val)) {
                    return $users_parameter[$key]['title'].'格式不正确！';
                }
            } else if (preg_match('/^mobile_/i', $key)) {
                if (!empty($val) && !check_mobile($val)) {
                    return $users_parameter[$key]['title'].'格式不正确！';
                }
            }
        }

        // 判断手机和邮箱是否已存在
        foreach ($users_parameter as $key => $value) {
            if (isset($post_users[$value['name']])) {
                $where_2 = [
                    'para_id'  => ['EQ', $value['para_id']],
                    'info'     => trim($post_users[$value['name']]),
                    'users_id' => ['NEQ', $users_id],
                    'lang'     => $this->admin_lang,
                ];

                // 若users_id为空，则清除条件中的users_id条件
                if (empty($users_id)) { unset($where_2['users_id']); }

                $users_list = Db::name('users_list')->where($where_2)->field('info')->find();
                if (!empty($users_list['info'])) {
                    return $value['title'].'已存在！';
                }
            }
        }
    }

    // 查询会员属性信息表的邮箱和手机字段
    // 必须传入参数：
    // users_id 会员ID
    // field    查询字段，email仅邮箱，mobile仅手机号，*为两项都查询。
    // return   Data
    public function getUsersListData($field,$users_id)
    {   
        $Data = array();
        if ('email' == $field || '*' == $field) {
            // 查询邮箱
            $parawhere = [
                'name'      => ['LIKE', "email_%"],
                'is_system' => 1,
                'lang'      => $this->admin_lang,
            ];
            $paraData = Db::name('users_parameter')->where($parawhere)->field('para_id')->find();
            $listwhere = [
                'para_id'   => $paraData['para_id'],
                'users_id'  => $users_id,
                'lang'      => $this->admin_lang,
            ];
            $listData = Db::name('users_list')->where($listwhere)->field('users_id,info')->find();
            $Data['email'] = !empty($listData['info']) ? $listData['info'] : '';
            $Data['is_email'] = !empty($Data['email']) ? 1 : 0;
        }

        if ('mobile' == $field || '*' == $field) {
            // 查询手机号
            $parawhere_1 = [
                'name'      => ['LIKE', "mobile_%"],
                'is_system' => 1,
                'lang'     => $this->admin_lang,
            ];
            $paraData_1 = Db::name('users_parameter')->where($parawhere_1)->field('para_id')->find();
            $listwhere_1 = [
                'para_id'   => $paraData_1['para_id'],
                'users_id'  => $users_id,
                'lang'     => $this->admin_lang,
            ];
            $listData_1 = Db::name('users_list')->where($listwhere_1)->field('users_id,info')->find();
            $Data['mobile'] = !empty($listData_1['info']) ? $listData_1['info'] : '';
            $Data['is_mobile'] = !empty($Data['mobile']) ? 1 : 0;
        }

        return $Data;
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @author 陈风任 by 2019-2-20
     */
    public function getDataPara()
    {
        // 字段及内容数据处理
        $where = array(
            'lang'       => $this->admin_lang,
            'is_hidden'  => 0, // 是否隐藏属性，0为否
        );

        $row =Db::name('users_parameter')->field('*')
            ->where($where)
            ->order('sort_order asc,para_id asc')
            ->select();

        // 根据所需数据格式，拆分成一维数组
        $addonRow = array();

        // 根据不同字段类型封装数据
        $list = $this->showViewFormData($row, 'users_', $addonRow);
        return $list;
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @author 陈风任 by 2019-2-20
     */
    public function getDataParaList($users_id)
    {
        // 字段及内容数据处理
        $row = Db::name('users_parameter')->field('a.*,b.info,b.users_id')
            ->alias('a')
            ->join('__USERS_LIST__ b', "a.para_id = b.para_id AND b.users_id = {$users_id}", 'LEFT')
            ->where([
                'a.lang'       => $this->admin_lang,
                'a.is_hidden'  => 0, // 是否隐藏属性，0为否
            ])
            ->order('a.sort_order asc,a.para_id asc')
            ->select();

        // 根据所需数据格式，拆分成一维数组
        $addonRow = [];
        foreach ($row as $key => $val) {
            $addonRow[$val['name']] = $val['info'];
        }

        // 根据不同字段类型封装数据
        $list = $this->showViewFormData($row, 'users_', $addonRow);
        return $list;
    }

    /**
     * 处理页面显示字段的表单数据
     * @param array $list 字段列表
     * @param array $formFieldStr 表单元素名称的统一数组前缀
     * @param array $addonRow 字段的数据
     * @author 陈风任 by 2019-2-20
     */
    public function showViewFormData($list, $formFieldStr, $addonRow = array())
    {
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                $val['fieldArr'] = $formFieldStr;
                switch ($val['dtype']) {
                    case 'int':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = $addonRow[$val['name']];
                        } else {
                            if(preg_match("#[^0-9]#", $val['dfvalue']))
                            {
                                $val['dfvalue'] = "";
                            }
                        }
                        break;
                    }

                    case 'float':
                    case 'decimal':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = $addonRow[$val['name']];
                        } else {
                            if(preg_match("#[^0-9\.]#", $val['dfvalue']))
                            {
                                $val['dfvalue'] = "";
                            }
                        }
                        break;
                    }

                    case 'select':
                    {
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $dfTrueValue = !empty($dfvalueArr[0]) ? $dfvalueArr[0] : '';
                            $val['trueValue'] = array();
                        }
                        break;
                    }

                    case 'radio':
                    {
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $dfTrueValue = !empty($dfvalueArr[0]) ? $dfvalueArr[0] : '';
                            $val['trueValue'] = array($dfTrueValue);
                        }
                        break;
                    }

                    case 'checkbox':
                    {
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $val['trueValue'] = array();
                        }
                        break;
                    }

                    case 'img':
                    {
                        $val['dfvalue'] = handle_subdir_pic($addonRow[$val['name']]);
                        break;
                    }

                    case 'imgs':
                    {
                        $val[$val['name'].'_eyou_imgupload_list'] = array();
                        if (isset($addonRow[$val['name']]) && !empty($addonRow[$val['name']])) {
                            $eyou_imgupload_list = explode(',', $addonRow[$val['name']]);
                            /*支持子目录*/
                            foreach ($eyou_imgupload_list as $k1 => $v1) {
                                $eyou_imgupload_list[$k1] = handle_subdir_pic($v1);
                            }
                            /*--end*/
                            $val[$val['name'].'_eyou_imgupload_list'] = $eyou_imgupload_list;
                        }
                        break;
                    }

                    case 'datetime':
                    {
                        if (!empty($addonRow[$val['name']])) {
                            if (is_numeric($addonRow[$val['name']])) {
                                $val['dfvalue'] = date('Y-m-d H:i:s', $addonRow[$val['name']]);
                            } else {
                                $val['dfvalue'] = $addonRow[$val['name']];
                            }
                        } else {
                            $val['dfvalue'] = date('Y-m-d H:i:s');
                        }
                        break;
                    }

                    case 'htmltext':
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        /*支持子目录*/
                        $val['dfvalue'] = handle_subdir_pic($val['dfvalue'], 'html');
                        /*--end*/
                        break;
                    }
                    case 'file':
                    {
                        $ext = tpCache('basic.file_type');
                        $val['ext'] = !empty($ext) ? $ext : "zip|gz|rar|iso|doc|xls|ppt|wps";
                        $val['filesize'] = upload_max_filesize();
                        break;
                    }

                    default:
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        /*支持子目录*/
                        if (is_string($val['dfvalue'])) {
                            $val['dfvalue'] = handle_subdir_pic($val['dfvalue'], 'html');
                            $val['dfvalue'] = handle_subdir_pic($val['dfvalue']);
                        }
                        /*--end*/
                        break;
                    }
                }
                $list[$key] = $val;
            }
        }
        return $list;
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据删除之后做的相应处理操作, 使用时手动调用
     */
    public function afterDel($users_ids = [])
    {
        if (!empty($users_ids)) {
            eyou_statistics_data(4, count($users_ids), '', 'dec');//统计新增会员数
            try {
                /*同步删除会员投稿数据*/
                Db::name('archives')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除文章订单表数据*/
                Db::name('article_order')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除内置问答表数据*/
                Db::name('ask')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除内置问答表数据*/
                Db::name('ask_answer')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除问答点赞表数据*/
                Db::name('ask_answer_like')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除下载记录表数据*/
                Db::name('download_log')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除下载订单表数据*/
                Db::name('download_order')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除留言表数据*/
                Db::name('guestbook')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除视频播放记录表数据*/
                Db::name('media_log')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除视频订单表数据*/
                Db::name('media_order')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除视频播放时长表数据*/
                Db::name('media_play_record')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除礼品兑换记录表数据*/
                Db::name('memgiftget')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除搜索锁定表数据*/
                Db::name('search_locking')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除搜索关键词表数据*/
                Db::name('search_word')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城收货地址表数据*/
                Db::name('shop_address')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除购物车表数据*/
                Db::name('shop_cart')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除优惠券领券记录表数据*/
                Db::name('shop_coupon_use')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单表数据*/
                Db::name('shop_order')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单评论表数据*/
                Db::name('shop_order_comment')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单详情表数据*/
                Db::name('shop_order_details')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单日志表数据*/
                Db::name('shop_order_log')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单售后表数据*/
                Db::name('shop_order_service')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单售后日志表数据*/
                Db::name('shop_order_service_log')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除商城订单统一支付表数据*/
                Db::name('shop_order_unified_pay')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除邮件发送记录表数据*/
                Db::name('smtp_record')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除我的收藏表数据*/
                Db::name('users_collection')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除我的足迹数据*/
                Db::name('users_footprint')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除转发记录表数据*/
                Db::name('users_forward')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除我喜欢的表数据*/
                Db::name('users_like')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除会员属性表数据*/
                Db::name('users_list')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除会员登录日志表数据*/
                Db::name('users_login_log')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除金额明细表数据*/
                Db::name('users_money')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除站内通知表数据*/
                Db::name('users_notice')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除用户已读站内通知表数据*/
                Db::name('users_notice_read')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除站内信发送接收记录表数据*/
                Db::name('users_notice_tpl_content')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除积分详情表数据*/
                Db::name('users_score')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除用户签到表数据*/
                Db::name('users_signin')->where("users_id",'IN',$users_ids)->delete();
                /*同步删除微信小程序用户表数据*/
                Db::name('wx_users')->where("users_id",'IN',$users_ids)->delete();
                // 同步删除插件数据
                $Prefix = config('database.prefix');
                if (is_dir('./weapp/Ask/')) {
                    $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'weapp_ask\'');
                    if (!empty($isTable)) {
                        /*同步删除内置问答表数据*/
                        Db::name('weapp_ask')->where("users_id",'IN',$users_ids)->delete();
                        /*同步删除内置问答表数据*/
                        Db::name('weapp_ask_answer')->where("users_id",'IN',$users_ids)->delete();
                        /*同步删除问答点赞表数据*/
                        Db::name('weapp_ask_answer_like')->where("users_id",'IN',$users_ids)->delete();
                    }
                }
                if (is_dir('./weapp/Comment/')) {
                    $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'weapp_comment\'');
                    if (!empty($isTable)) {
                        /*同步删除插件评论表数据*/
                        Db::name('weapp_comment')->where(['users_id'=>['IN', $users_ids]])->delete();
                        /*同步删除插件评论点赞表数据*/
                        Db::name('weapp_comment_like')->where("users_id",'IN',$users_ids)->delete();
                    }
                }
            } catch (\Exception $e) {
                
            }
        }
    }

    /**
     * 批量更新会员过期等级
     * @return [type] [description]
     */
    public function batch_update_userslevel()
    {
        $redata = [
            'code' => 1,
            'msg'  => '没开启会员中心，不需要执行',
        ];
        $web_users_switch = tpCache('web.web_users_switch');
        if (!empty($web_users_switch)) {
            /*查询系统初始的默认级别*/
            $level_id = Db::name('users_level')->where([
                    'level_id'  => 1,
                    'is_system' => 1,
                ])->value('level_id');
            $level_id = empty($level_id) ? 1 : intval($level_id);
            /* END */
            $time = getTime();
            $where_str = "`level` <> {$level_id} AND (`level_maturity_days` < 36600 AND (`open_level_time` <> 0 OR `level_maturity_days` <> 0)) AND ((`open_level_time` + (`level_maturity_days` * 86400) - {$time}) <= 0)";
            $r = Db::name('users')->where($where_str)->update([
                    'level'           => $level_id,
                    'open_level_time' => 0,
                    'level_maturity_days' => 0,
                    'update_time'     => $time,
                ]);
            if ($r !== false) {
                $redata = [
                    'code' => 1,
                    'msg'  => '会员过期更新成功',
                ];
            } else {
                $redata = [
                    'code' => 1,
                    'msg'  => '会员过期更新失败',
                ];
            }
        }
        return $redata;
    }
}