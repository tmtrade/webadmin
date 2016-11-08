<?
define('WebDir', '.');						//定义项目路径
require('../Spring/Spring.php');		    //载入框架入口文件
require(LibDir.'/Util/Tool/MakeCode.php');	//载入代码生成工具

//指定数据库名、表前缀
$configs = array(
	array(
		'name'	  => 'trade_new',
		'prefix'  => 't_',
		'contain' => '*',
		),
	);

//指定数据库配置文件存放路径
MakeCode::$configFileDir = WebDir.'/Config/Db';
MakeCode::create($configs);
?>