<?
/**
* 国内商标
*
* 国内商标商品创建，修改，删除等
*
* @package	Module
* @author	Jeany
* @since	2015-12-30
*/
class ModuleModule extends AppModule
{
    public $models = array(
        'module'		=> 'module',
		'modulelink'	=> 'moduleLink',
		'modulePic'		=> 'modulePic',
		'moduleClass'	=> 'moduleClass',
		'moduleClassItems'	=> 'moduleClassItems',
    );
    
	//模块
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['order'] = array('date'=>'desc');
        $res = $this->import('module')->findAll($r);
		if($res){
			foreach($res['rows'] as $k => $v){
				$res['rows'][$k]['classNum'] = $this->getModuleClassNum($v['id']);
				$res['rows'][$k]['adsNum'] = $this->getModuleAdsNum($v['id']);
				$res['rows'][$k]['linkNum'] = $this->getModuleLinkNum($v['id']);
			}
		}
        return $res;
    }
	
	
	//首页模块子分类数量
    public function getModuleClassNum($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
        $num = $this->import('moduleClass')->count($r);
        return $num;
    }
	
	//首页模块包含广告条数数量
    public function getModuleAdsNum($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
        $num = $this->import('modulePic')->count($r);
        return $num;
    }
	
	//首页模块推广链接数量
    public function getModuleLinkNum($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
        $num = $this->import('modulelink')->count($r);
        return $num;
    }
}
?>