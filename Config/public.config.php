<?
/**
 * 定义应用所需常量
 */
$define = array(

    'SYS_API_KEY' => 'JyZyZcXmChOfN2016ZxWlQkF',//api 加密 key
    'API_USERS' => array(
        'UserCenter' => 'api1010',//用户中心
    ),

    'OPENAPI_KEY' => array(
        'api1001' => 'JyZgJxMcHoFn2015ZxKfKeYsBxT',
    ),
    'OPENAPI_USERS' => array(
        'TmSystem' => 'api1001',//商标系统
    ),

    'ISTOP_LIST' => array(
    	'0' 	=> '不置顶',
    	'1' 	=> '普通置顶',
    	'2' 	=> '包装图置顶',
    	'3' 	=> '特价置顶',
    	'4' 	=> '4置顶',
    	'5' 	=> '5置顶',
    	'6' 	=> '6置顶',
    	'7' 	=> '7置顶',
    	'8' 	=> '8置顶',
    	'9' 	=> '9置顶',
    ),

	'INDEX_WORD_STRESS' => array(
		'0' 	=> '无',
		'1' 	=> '红色',
		'2' 	=> '蓝色',
		'3' 	=> '橘色',
	),
	//来源渠道
    'SOURCE' => array(
        '1'  => '管家',
        '2'  => '顾问',
		'4'  => '展示页',
		'5'  => '引导页',
		'6'  => '400',
		'7'  => '乐语',
		'8'  => '百度商桥',
		'3'  => '其他',
		'9'  => '同行',
		'10'  => '一只蝉',
		'11'  => '用户中心',
    ),
	//出售商标状态
    'SALE_STATUS' => array(
        '1'  => '销售中',
		'2'  => '已下架',
        '3'  => '待审核',
    ),
	
	//商标类型
    'TYPES' => array(
		'1'  => '中文',
        '2'  => '英文',
        '3'  => '图形',
		'4'  => '中+英',
		'5'  => '中+图',
		'6'  => '英+图',
		'7'  => '中+英+图',
		'8'  => '数字',
    ),

    'OP_TYPE' => array(
    	'1' 	=> '商品上架',
    	'2' 	=> '商品下架',
    	'3' 	=> '创建商品',
    	'4' 	=> '编辑商品',
    	'5' 	=> '审核联系人',
    	'6' 	=> '加入黑名单',
    	'7' 	=> '删除黑名单',
    	'8' 	=> '添加联系人',
    	'9' 	=> '编辑联系人',
    	'10' 	=> '修改价格信息',
    	'11' 	=> '修改备注信息',
    	'12' 	=> '修改包装信息',
    	'13' 	=> '删除联系人',
    	'14' 	=> '联系人审核通过',
    	'15' 	=> '驳回并删除联系人',
    	'16' 	=> '删除商品',
    ),
	
	//指导价格浮动比例
    'FLAOT_RATIO' => array(
		'1'  => 1,
        '2'  => 0.8,
        '3'  => 0.6,
		'4'  => 0.5,
		'5'  => 0.4,
		'6'  => 0.3,
		'7'  => 0.1,
		'8'  => 0,
    ),
	
	//出售搜索底价
    'SEARCH_PRICE' => array(
		'1'  => array(1,20000,'2万以下'),
                '2'  => array(20000,50000,'2-5万'),
		'3'  => array(50000,100000,'5-10万'),
		'4'  => array(100000,200000,'10-20万'),
		'5'  => array(200000,500000,'20-50万'),
		'6'  => array(500000,1000000,'50-100万'),
		'7'  => array(1000000,10000000000000,'100万以上'),
		'8'  => array(0,0,'面议'),
    ),
	
	//商标字数
    'TM_NUMBER' => array(
        '1'  => '一个字母或中文',
        '2'  => '两个字母或中文',
		'3'  => '三个字母或中文',
		'4'  => '四个字母或中文',
		'5'  => '五个字母或中文',
		'6'  => '五个以上字母或中文',
    ),
	//商标标签
    'TM_LABEL' => array(
        '1'  => '精品',
		'2'  => '潜力',
		'3'  => '特色',
		'4'  => '紧急',
    ),

	//商标标签
    'SALE_TYPE' => array(
        '1'  => '出售',
        '2'  => '许可',
        '3'  => '出售/许可',
    ),


	'AUTH_LIST' => array(
		'1' => array(
			'label' => '商品列表',
			'child' => array(
				'1' => array(
					'label' => '国内商标列表',
				),
                                '27' => array(
					'label' => '专利列表',
				),
				'2' => array(
					'label' => '下架包装商品',
				),
				'3' => array(
					'label' => '删除商品',
				),
				'15' => array(
					'label' => '创建商品',
				),
				'16' => array(
					'label' => '导入商品',
				),
				'17' => array(
					'label' => '修改特价价格',
				),
				'18' => array(
					'label' => '修改包装信息',
				),
				'19' => array(
					'label' => '删除联系人',
				),
				'20' => array(
					'label' => '添加/编辑联系人',
				),
				'21' => array(
					'label' => '审核联系人',
				),
				'22' => array(
					'label' => '修改备注信息',
				),
                '25' => array(
                    'label' => '修改普通价格',
                ),
                '26' => array(
                    'label' => '下架普通商品',
                ),
			),
		),
            
		'2' => array(
			'label' => '站点管理',
			'child' => array(
				'4' => array(
					'label' => '联系电话管理',
				),
				'5' => array(
					'label' => '黑名单列表',
				),
				'6' => array(
					'label' => '首页基本配置',
				),
				'7' => array(
					'label' => '首页模块设置',
				),
				'8' => array(
					'label' => '通栏行业菜单',
				),
				'9' => array(
					'label' => '频道页设置',
				),
				'10' => array(
					'label' => '专题页设置',
				),
				'11' => array(
					'label' => '商标分类设置',
				),
				'23' => array(
					'label' => '成功案例设置',
				),
				'24' => array(
					'label' => 'SEO设置',
				),
			),
		),
		'3' => array(
			'label' => '系统配置',
			'child' => array(
				'12' => array(
					'label' => '账号管理',
				),
				'13' => array(
					'label' => '角色管理',
				),
				'14' => array(
					'label' => '缓存管理',
				),
			),
		),
                '4' => array(
			'label' => '数据跟踪',
			'child' => array(
				'28' => array(
					'label' => '访问者数据跟踪',
				),
				'29' => array(
					'label' => '模块使用频率',
				),
				'30' => array(
					'label' => '模块走势图',
				),
                                '31' => array(
					'label' => '热门搜索 筛选页',
				),
			),
		),
	),
	'ALL_AUTH' => array(
		'1' => array(
			'id'	=> '1',
			'up'	=> '1',
			'label' => '国内商标列表',
		),
		'2' => array(
			'id'	=> '2',
			'up'	=> '1',
			'label' => '下架包装商品',
		),
		'3' => array(
			'id'	=> '3',
			'up'	=> '1',
			'label' => '删除商品',
		),
		'4' => array(
			'id'	=> '4',
			'up'	=> '2',
			'label' => '联系电话管理',
		),
		'5' => array(
			'id'	=> '5',
			'up'	=> '2',
			'label' => '黑名单列表',
		),
		'6' => array(
			'id'	=> '6',
			'up'	=> '2',
			'label' => '首页基本配置',
		),
		'7' => array(
			'id'	=> '7',
			'up'	=> '2',
			'label' => '首页模块设置',
		),
		'8' => array(
			'id'	=> '8',
			'up'	=> '2',
			'label' => '通栏行业菜单',
		),
		'9' => array(
			'id'	=> '9',
			'up'	=> '2',
			'label' => '频道页设置',
		),
		'10' => array(
			'id'	=> '10',
			'up'	=> '2',
			'label' => '专题页设置',
		),
		'11' => array(
			'id'	=> '11',
			'up'	=> '2',
			'label' => '商标分类设置',
		),
		'12' => array(
			'id'	=> '12',
			'up'	=> '3',
			'label' => '账号管理',
		),
		'13' => array(
			'id'	=> '13',
			'up'	=> '3',
			'label' => '角色管理',
		),
		'14' => array(
			'id'	=> '14',
			'up'	=> '3',
			'label' => '缓存管理',
		),
		'15' => array(
			'id'	=> '15',
			'up'	=> '1',
			'label' => '创建商品',
		),
		'16' => array(
			'id'	=> '16',
			'up'	=> '1',
			'label' => '导入商品',
		),
		'17' => array(
			'id'	=> '17',
			'up'	=> '1',
			'label' => '修改商品价格',
		),
		'18' => array(
			'id'	=> '18',
			'up'	=> '1',
			'label' => '修改包装信息',
		),
		'19' => array(
			'id'	=> '19',
			'up'	=> '1',
			'label' => '删除联系人',
		),
		'20' => array(
			'id'	=> '20',
			'up'	=> '1',
			'label' => '添加/编辑联系人',
		),
		'21' => array(
			'id'	=> '21',
			'up'	=> '1',
			'label' => '审核联系人',
		),
		'22' => array(
			'id'	=> '22',
			'up'	=> '1',
			'label' => '修改备注信息',
		),
		'23' => array(
			'id'	=> '23',
			'up'	=> '2',
			'label' => '成功案例设置',
		),
		'24' => array(
			'id'	=> '24',
			'up'	=> '2',
			'label' => 'SEO设置',
		),
        '25' => array(
            'id'    => '25',
            'up'    => '1',
            'label' => '修改普通价格',
        ),
        '26' => array(
            'id'    => '26',
            'up'    => '1',
            'label' => '下架普通商品',
        ),
        '27' => array(
            'id'    => '27',
            'up'    => '1',
            'label' => '专利列表',
        ),
        '28' => array(
            'id'    => '28',
            'up'    => '4',
            'label' => '访问者数据跟踪',
        ),
        '29' => array(
            'id'    => '29',
            'up'    => '4',
            'label' => '模块使用频率',
        ),
        '30' => array(
            'id'    => '30',
            'up'    => '4',
            'label' => '模块走势图',
        ),
        '31' => array(
            'id'    => '31',
            'up'    => '4',
            'label' => '热门搜索 筛选页',
        ),
    ),
    
        //SEO列表配置项
        'SEO_LIST' => array(
		'1' => array(
			'id'            => '1',
			'name'          => '首页',
		),
		'2' => array(
			'id'            => '2',
			'name'          => '特价商标页',
		),
        '3' => array(
                    'id'	=> '3',
                    'name'      => '商标筛选页',
            ),
        '4' => array(
                    'id'	=> '4',
                    'name'      => '专利购买页',
            ),
        '5' => array(
                    'id'	=> '5',
                    'name'      => '专利出售页',
            ),
        '6' => array(
                    'id'	=> '6',
                    'name'      => '商标购买页',
            ),
        '7' => array(
                    'id'	=> '7',
                    'name'      => '商标出售页',
            ),
        '8' => array(
                    'id'	=> '8',
                    'name'      => '商标详情页',
            ),
        '9' => array(
                    'id'	=> '9',
                    'name'      => '专题列表页',
            ),
        '10' => array(
                    'id'	=> '10',
                    'name'      => '专题详情页',
            ),
        '11' => array(
                    'id'	=> '11',
                    'name'      => '案列列表页',
            ),
        '12' => array(
                    'id'	=> '12',
                    'name'      => '案列详情页',
            ),
        '13' => array(
                        'id'	=> '13',
                        'name'      => '标签筛选页',
            ),
        '14' => array(
                    'id'	=> '14',
                    'name'      => '专利出售页',
            ),
        '15' => array(
                    'id'	=> '15',
                    'name'      => '专利详情页',
            ),
        ),
	'ROLE_LIST' => array(
		'role/index' 			=> '13',
		'role/add' 				=> '13',
		'role/edit' 			=> '13',
		
		'user/index' 			=> '12',
		'user/add' 				=> '12',
		'user/role' 			=> '12',

		'internal/index'		=> '1',
		'internal/edit'			=> '1',
		'internal/doUp' 		=> '1',
		'internal/doDown' 		=> '2',
		'internal/delete' 		=> '3',
		'internal/deleteSale' 	=> '3',
		'internal/create' 		=> '15',
		'internal/checkNumber'	=> '15',
		'internal/import'		=> '16',
		'internal/importForm'	=> '16',
		'internal/setPrice' 	=> '17',
		'internal/setEmbellish' => '18',
		'internal/delContact' 	=> '19',
		'internal/contact' 		=> '20',
		'internal/setContact' 	=> '20',
		'internal/setVerify' 	=> '21',
		'internal/delVerify' 	=> '21',
        'internal/setMemo'      => '22',
        'internal/setPrice'     => '25',//修改普通价格
        'internal/doDown'       => '26',//下架普通商品
		
		'cache/index' 			=> '14',
		'phone/index' 			=> '4',
		'blacklist/index' 		=> '5',
		'basic/index'			=> '6',
		'module/index' 			=> '7',
		'industry/index' 		=> '8',
		'channel/index' 		=> '9',
		'topic/index' 			=> '10',
		'tmclass/index' 		=> '11',
		'case/index' 			=> '23',
		'seo/index' 			=> '24',
	),


	/*平台入驻*/
	'SALE_PLATFORM' => array(
		'1' 		=> array(
			'name'	=> '京东',
			'value'	=> '1,2,3,5,6,9,11,12,14,15,16,18,20,21,24,25,26,27,28,29,30,31,32,33,35,36,37,39,41,42,43,44,45',
						
		),
		'2' 		=> array(
			'name'	=> '天猫',
			'value'	=> '1,2,3,5,6,8,9,11,12,14,15,16,17,18,19,20,21,24,25,26,27,28,29,30,31,32,33,35,36,37,39,41,42,43,44,45',
			
		),
		'3' 		=> array(
			'name'	=> '亚马逊',
			'value'	=> '3,5,9,10,16,28,29,30,31,32,33,34,41,44',
		),
		'4' 		=> array(
			'name'	=> '1号店',
			'value'	=> '2,3,5,6,7,8,9,10,11,12,14,15,16,17,18,19,20,21,23,24,25,26,27,28,29,30,31,32,33,35,36,37,38,39,41,42,43,44',
		),
		'5' 		=> array(
			'name'	=> '美丽说',
			'value'	=> '18,24,25,26,29,30,31,32,33',
		),
		'6' 		=> array(
			'name'	=> '聚美优品',
			'value'	=> '3,5,8,9,10,11,14,15,16,18,21,24,25,26,28,29,30,31,32,33',
		),
		'7' 		=> array(
			'name'	=> '大型超市',
			'value'	=> '1,2,3,5,6,7,8,9,11,12,13,14,15,16,17,18,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34',
		),
	),

	'SecondStatus' => array (
		1     => '申请中',
		2     => '已注册',
		3     => '商标已无效',
		4     => '驳回中',
		5     => '驳回复审中',
		6     => '部分驳回',
		7     => '公告中',
		8     => '异议中',
		9     => '异议复审中',
		10    => '需续展',
		11    => '续展中',
		12    => '开具优先权证明中',
		13    => '开具注册证明中',
		14    => '撤销中',
		15    => '撤销复审中',
		16    => '撤回撤销中',
		17    => '变更中',
		18    => '变更代理人中',
		19    => '补证中',
		20    => '补变转续证中',
		21    => '转让中',
		22    => '更正中',
		23    => '许可备案中',
		24    => '许可备案变更中',
		25    => '删减商品中',
		26    => '冻结中',
		27    => '注销中',
		28    => '无效宣告中',
        ),
	

    'MSG_TEMPLATE' => array(
        'register'  => "%s（登录密码），系统已为您开通手机账户，登陆可查看求购进展，工作人员不会向你索要，请勿向任何人泄露。",
    ),
    
    //专利大类别
    'PATENT_TYPE' => array (
		1     => '发明',
		2     => '实用新型',
		3     => '外观设计',
        ),
    
    //专利分类
    'PATENT_ClASS_ONE' => array (
		'a'     => '人类生活必需',
		'b'     => '作业；运输',
		'c'     => '化学；冶金',
                'd'     => '纺织；造纸',
                'e'     => '固定建筑物',
                'f'     => '机械工程；照明；加热；武器；爆破',
                'g'     => '物理',
                'h'     => '电学',
        ),
    
    //专利分类
    'PATENT_ClASS_TWO' => array (
		'01'     => '食品',
		'02'     => '服装和服饰用品',
		'03'     => '其他类未列入的旅行用品、箱子、阳伞和个人用品',
                '04'     => '刷子类',
                '05'     => '纺织品、人造或天然材料片材',
                '06'     => '家具',
                '07'     => '其他类未列入的家用物品',
                '08'     => '工具和金属器具',
                '09'     => '用于商品运输或装卸的包装和容器',
		'10'     => '钟、表和其他计量仪器、检测和信号仪器',
                '11'     => '装饰品',
                '12'     => '运输或提升工具',
                '13'     => '发电、配电和输电的设备',
                '14'     => '录音、通讯或信息再现设备',
                '15'     => '其他类未列入的机械',
                '16'     => '照相、电影摄影和光学仪器',
                '17'     => '乐器',
                '18'     => '印刷和办公机械',
                '19'     => '文具用品、办公设备、美术用品及教学材料',
                '20'     => '销售和广告设备、标志',
                '21'     => '游戏器具、玩具、帐篷和体育用品',
		'22'     => '武器、烟火用具、用于狩猎、捕鱼及捕杀有害动物的器具',
		'23'     => '液体分配设备，卫生、供暖、通风和空调设备，固体燃料',
                '24'     => '医疗和实验室设备',
                '25'     => '建筑构件和施工元件',
                '26'     => '照明设备',
                '27'     => '烟草和吸烟用具',
                '28'     => '药品、化妆品、梳妆用品和器具',
                '29'     => '防火灾、防事故救援装置和设备',
                '30'     => '动物的管理与驯养设备',
                '31'     => '其他类未列入的食品或饮料制作机械和设备',
		'99'     => '其他杂项',
        ),
    
);


return $define;

?>