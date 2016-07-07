<?php
/**
 * 站内信触发配置信息----------------- url可以为多个,以','间隔 -----------------------------
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/7 0007
 * Time: 下午 17:32
 */
return array(
    0=>array(
        'title'=>'账号相关',
        'data'=>array(
            0=>array(
                'title'=>'首次登陆',
                'url'=>SELLER_URL.'index/index'
            ),
        )
    ),
    1=>array(
        'title'=>'积分相关',
        'data'=>array(
            0=>array(
                'title'=>'积分变化',
                'url'=>TRADE_URL.'user/pointChange',
            ),
        )
    ),
    2=>array(
        'title'=>'订单相关',
        'data'=>array(
            0=>array(
                'title'=>'交易完成',
                'url'=>TRADE_URL.'internal/complateSale/,'.TRADE_URL.'patent/complateSale/',
            ),
	    1=>array(
                'title'=>'通过联系人',
                'url'=>TRADE_URL.'internal/setVerify/',
            ),
	    2=>array(
                'title'=>'驳回联系人',
                'url'=>TRADE_URL.'internal/delVerify/',
            ),
        )
    ),
    3=>array(
        'title'=>'申请相关',
        'data'=>array(
            0=>array(
                'title'=>'申请通过',
                'url'=>TRADE_URL.'exchange/through',
            ),
            1=>array(
                'title'=>'申请拒绝',
                'url'=>TRADE_URL.'exchange/cancel',
            ),
        )
    ),
);