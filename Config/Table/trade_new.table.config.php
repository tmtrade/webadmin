<?
$prefix		= 't_';
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


?>