<?
$prefix		= 'st_';
$dbId		= 'trade_new';
$configFile	= array( ConfigDir.'/Db/trade_new.master.config.php' );

$tbl['semVisitlog'] = array(
	'name'		=> $prefix.'sem_visitlog',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['sessions'] = array(
	'name'		=> $prefix.'sessions',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['visitlog'] = array(
	'name'		=> $prefix.'visitlog',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);


?>