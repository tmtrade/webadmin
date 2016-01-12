<?
$prefix     = 't_';
$dbId       = 'trade';
$configFile = array( ConfigDir.'/Db/trade.master.config.php' );

$tbl['tsale'] = array(
    'name'      => $prefix.'sale',
    'dbId'      => $dbId, 
    'configFile'=> $configFile,
);

$tbl['tsaleTrademark'] = array(
    'name'      => $prefix.'sale_trademark',
    'dbId'      => $dbId, 
    'configFile'=> $configFile,
);

?>