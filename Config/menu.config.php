<?
return $menu = array(
	'1' => array(
		'title' => '商标列表',
		'ico'   => 'images/menu1.png',
		//'url'   => '/sale/index/',
		'child'	=> array(
					'1' => array(
						'title' => '国内商标列表',
						'url'   => '/internal/index/',
						'auth'	=> '1',
					),
					// '2' => array(
					// 	'title' => '专利列表',
					// 	'url'   => '/patent/index/',
					// 	'auth'	=> '2',
					// ),
					// '3' => array(
					// 	'title' => '国际商标列表',
					// 	'url'   => '/international/index/',
					// 	'auth'	=> '3',
					// ),
		),
	),
	'2' => array(
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
				'auth'	=> '12',
			),
                        '10' => array(
				'title' => 'SEO设置',
				'url'   => '/seo/index/',
				'auth'	=> '13',
			),
		),
	),
	'3' => array(
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