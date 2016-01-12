<?
//Memcache数据缓存
$configs[] = array(
'id'        => 'mem',
'enable'    => true,
'source'    => LibDir.'/Util/Cache/MmCache.php',
'className' => 'MmCache',
'import'    => array(LibDir.'/Util/Cache/ICache.php'),
'property'  => array(
    'expire'     => 1800,
	'configFile' => ConfigDir.'/memcache.config.php',
	'objRef'	 => array('encoding' => 'encoding'),
));

//Redis数据缓存
$configs[] = array(
'id'        => 'redis',
'enable'    => true,
'source'    => LibDir.'/Util/Cache/RedisCache.php',
'className' => 'RedisCache',
'import'    => array(LibDir.'/Util/Cache/ICache.php'),
'property'  => array(
    'expire'     => 1800,
	'configFile' => ConfigDir.'/redis.config.php',
	'objRef'	 => array('encoding' => 'encoding'),
));

//Redis队列操作
$configs[] = array(
'id'        => 'redisQ',
'enable'    => true,
'source'    => LibDir.'/Util/Queue/RedisQ.php',	
'className' => 'redisQ',
'import'    => array(LibDir.'/Util/Queue/IQueue.php'),
'property'  => array(
    'expire'     => 1800,
	'configFile' => ConfigDir.'/redis.config.php',
	'objRef'	 => array('encoding' => 'encoding'),
));

?>