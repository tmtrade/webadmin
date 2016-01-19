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
		),
	),
	'3' => array(
		'title' => '任务管理',
		'ico'   => 'images/menu7.png',
		'url'   => '/remind/index/',
		'child'	=> array(
					'1' => array(
						'title' => '提醒列表',
						'url'   => '/remind/index/',
						'auth'	=> '14',
					),
					// '2' => array(
					// 	'title' => '临时求购',
					// 	'url'   => '/tempbuy/index/',
					// 	'auth'	=> '15',
					// ),
		),
	),
	'4' => array(
		'title' => '账号管理',
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
		),
	),
);
?>