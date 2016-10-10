<?
$prefix		= 't_';
$prefixs	= 's_'; //出售者平台
$dbId		= 'trade_new';
$configFile	= array( ConfigDir.'/Db/trade_new.master.config.php' );

$tbl['blacklist'] = array(
	'name'		=> $prefix.'blacklist',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['member'] = array(
	'name'		=> $prefix.'member',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['remind'] = array(
	'name'		=> $prefix.'remind',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['role'] = array(
	'name'		=> $prefix.'role',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['sale'] = array(
	'name'		=> $prefix.'sale',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['saleContact'] = array(
	'name'		=> $prefix.'sale_contact',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);


$tbl['saleHistory'] = array(
	'name'		=> $prefix.'sale_history',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['saleLog'] = array(
	'name'		=> $prefix.'sale_log',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['saleTminfo'] = array(
	'name'		=> $prefix.'sale_tminfo',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['seo'] = array(
	'name'		=> $prefix.'seo',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['tempBuy'] = array(
	'name'		=> $prefix.'temp_buy',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['tempUser'] = array(
	'name'		=> $prefix.'temp_user',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['phone'] = array(
	'name'		=> $prefix.'phone',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['module'] = array(
	'name'		=> $prefix.'module',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['moduleClass'] = array(
	'name'		=> $prefix.'module_class',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['moduleClassItems'] = array(
	'name'		=> $prefix.'module_class_items',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['modulePic'] = array(
	'name'		=> $prefix.'module_pic',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['moduleLink'] = array(
	'name'		=> $prefix.'module_link',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['indexBasic'] = array(
    'name'      => $prefix.'index_basic',
    'dbId'      => $dbId, 
    'configFile'=> $configFile,
);

$tbl['industry'] = array(
	'name'		=> $prefix.'industry',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['industryClass'] = array(
	'name'		=> $prefix.'industry_class',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['industryClassItems'] = array(
	'name'		=> $prefix.'industry_class_items',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['industryPic'] = array(
	'name'		=> $prefix.'industry_pic',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['channel'] = array(
	'name'		=> $prefix.'channel',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['channelItems'] = array(
	'name'		=> $prefix.'channel_items',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['classGroup'] = array(
	'name'		=> $prefix.'class_group',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);


$tbl['topic'] = array(
	'name'		=> $prefix.'topic',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['topicItems'] = array(
	'name'		=> $prefix.'topic_items',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['apiLog'] = array(
	'name'		=> $prefix.'api_log',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['systemLog'] = array(
	'name'		=> $prefix.'system_log',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['userSaleHistory'] = array(
	'name'		=> $prefix.'user_sale_history',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['case'] = array(
	'name'		=> $prefix.'case',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['income'] = array(
	'name'		=> $prefix.'income',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patent'] = array(
	'name'		=> $prefix.'patent',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentContact'] = array(
	'name'		=> $prefix.'patent_contact',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentHistory'] = array(
	'name'		=> $prefix.'patent_history',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentInfo'] = array(
	'name'		=> $prefix.'patent_info',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentLog'] = array(
	'name'		=> $prefix.'patent_log',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['userPatentHistory'] = array(
	'name'		=> $prefix.'user_patent_history',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentList'] = array(
	'name'		=> $prefix.'patent_list',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['test'] = array(
	'name'		=> $prefix.'test',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['keywordCount'] = array(
	'name'		=> $prefix.'keyword_count',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['stpage'] = array(
	'name'		=> $prefix.'stpage',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['tvisitlog'] = array(
	'name'		=> $prefix.'visitlog',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['tsessions'] = array(
	'name'		=> $prefix.'sessions',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['packageItems'] = array(
	'name'		=> $prefix.'package_items',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['package'] = array(
	'name'		=> $prefix.'package',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['systemSetting'] = array(
	'name'		=> $prefix.'system_setting',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

//出售者平台数据
$tbl['ad'] = array(
	'name'		=> $prefixs.'ad',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['exchange'] = array(
	'name'		=> $prefixs.'exchange',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messege'] = array(
	'name'		=> $prefixs.'messege',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messegeMonitor'] = array(
	'name'		=> $prefixs.'messege_monitor',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messegeUser'] = array(
	'name'		=> $prefixs.'messege_user',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['total'] = array(
	'name'		=> $prefixs.'total',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['totalLog'] = array(
	'name'		=> $prefixs.'total_log',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['passSale'] = array(
	'name'		=> $prefixs.'pass_sale',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['quotationItems'] = array(
	'name'		=> $prefixs.'quotation_items',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['quotation'] = array(
	'name'		=> $prefixs.'quotation',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);



?>