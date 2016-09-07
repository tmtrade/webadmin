<?
return $menu = array(
	'1' => array(
		'title' => '商标专利列表',
		'ico'   => 'images/menu1.png',
		//'url'   => '/sale/index/',
		'child'	=> array(
					'1' => array(
						'title' => '国内商标列表',
						'url'   => '/internal/index/',
						'auth'	=> '1',
					),
                    '2' => array(
						'title' => '商标交易历史',
						'url'   => '/internal/history/',
						'auth'	=> '36',
					),
                    '3' => array(
						'title' => '出售数据分析',
						'url'   => '/dataanalyze/index/',
						'auth'	=> '38',
					),
                    '4' => array(
						'title' => '专利列表',
						'url'   => '/patent/index/',
						'auth'	=> '27',
					),
		),
	),
    '2' => array(
		'title' => '数据分析',
		'ico'   => 'images/menu1.png',
		'url'   => '/visitlog/userlist/',
		'child'	=> array(
					'1' => array(
						'title' => '访问者数据跟踪',
						'url'   => '/visitlog/userlist/',
						'auth'	=> '28',
					),
                                        '2' => array(
						'title' => '模块使用频率',
						'url'   => '/visitlog/frequency/',
						'auth'	=> '29',
					),
                                        '3' => array(
						'title' => '模块走势图',
						'url'   => '/visitlog/trendChart/',
						'auth'	=> '30',
					),
                                        '4' => array(
						'title' => '热门搜索 筛选页',
						'url'   => '/visitlog/search/',
						'auth'	=> '31',
					),
		),
	),
    '3' => array(
		'title' => '出售者平台',
		'ico'   => 'images/menu1.png',
		'url'   => '/exchange/index/',
		'child'	=> array(
					'1' => array(
						'title' => '兑换信息列表',
						'url'   => '/exchange/index/',
						'auth'	=> '33',
					),
                                        '2' => array(
						'title' => '站内信配置',
						'url'   => '/messege/index/',
						'auth'	=> '34',
					),
                                        '3' => array(
						'title' => '广告管理',
						'url'   => '/ad/index/',
						'auth'	=> '35',
					),
		),
	),
	'4' => array(
		'title' => '站点管理',
		'url'   => '/phone/index/',
		'ico'   => 'images/menu2.png',
		'child'	=> array(
			'1' => array(
				'title' => '联系电话管理',
				'url'   => '/phone/index/',
				'auth'	=> '4',
			),
			'2' => array(
				'title' => '黑名单列表',
				'url'   => '/blacklist/index/',
				'auth'	=> '5',
			),
			'3' => array(
				'title' => '首页基本配置',
				'url'   => '/basic/index/',
				'auth'	=> '6',
			),
			'4' => array(
				'title' => '首页模块设置',
				'url'   => '/module/index/',
				'auth'	=> '7',
			),
			'5' => array(
				'title' => '通栏行业菜单',
				'url'   => '/industry/index/',
				'auth'	=> '8',
			),
			'6' => array(
				'title' => '频道页设置',
				'url'   => '/channel/index/',
				'auth'	=> '9',
			),
			'7' => array(
				'title' => '专题页设置',
				'url'   => '/topic/index/',
				'auth'	=> '10',
			),
			'8' => array(
				'title' => '商标分类设置',
				'url'   => '/tmclass/index/',
				'auth'	=> '11',
			),
			'9' => array(
				'title' => '成功案例设置',
				'url'   => '/case/index/',
				'auth'	=> '23',
			),
			'10' => array(
				'title' => 'SEO设置',
				'url'   => '/seo/index/',
				'auth'	=> '24',
			),
		),
	),
	'5' => array(
		'title' => '系统配置',
		'ico'   => 'images/menu9.png',
		'url'   => '/user/index/',
		'child'	=> array(
					'1' => array(
						'title' => '账号管理',
						'url'   => '/user/index/',
						'auth'	=> '12',
					),
					'2' => array(
						'title' => '角色管理',
						'url'   => '/role/index/',
						'auth'	=> '13',
					),
					'3' => array(
						'title' => '缓存管理',
						'url'   => '/cache/index/',
						'auth'	=> '14',
					),
		),
	),
);
?>