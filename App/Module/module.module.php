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
	
	
	//模块基础内容
    public function getModuleInfo($moduleId)
    {
        $r = array();
        $r['eq']['id']  = $moduleId;
        $r['limit'] = 1;
        $res = $this->import('module')->find($r);
        return $res;
    }
	
	
	
	//首页模块子分类列表信息
    public function getModuleClassList($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['limit'] = 100;
        $r['order'] = array('date'=>'desc');
        $data = $this->import('moduleClass')->findAll($r);
        return $data;
    }
	
	//首页模块包含广告列表信息
    public function getModuleAdsList($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['limit'] = 100;
        $r['order'] = array('date'=>'desc');
        $data = $this->import('modulePic')->findAll($r);
        return $data;
    }
	
	//首页模块推广链接列表信息
    public function getModuleLinkList($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['limit'] = 100;
        $r['order'] = array('date'=>'desc');
        $data = $this->import('modulelink')->findAll($r);
        return $data;
    }
	
	
	//首页模块推广链接列表信息
    public function getLinkInfo($moduleId,$lId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['eq']['id'] = $lId;
        $data = $this->import('modulelink')->find($r);
        return $data;
    }
	
	
	//添加首页模块推广链接
    public function addLink($data,$moduleId)
    {    
        $data['moduleId'] = $moduleId;
        return $this->import('modulelink')->create($data);
		
    }
	
	
	//编辑首页模块推广链接
    public function updateLink($data, $lId)
    {
        $r['eq'] = array('id'=>$lId);
        return $this->import('modulelink')->modify($data, $r);
    }
	
	//删除首页模块推广链接
    public function delLink($id, $moduleId)
    {
		$r = array();
        if ( empty($id) && empty($moduleId) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('modulelink')->remove($r);
    }
	
	
	//添加首页模块广告图
    public function addPic($data,$moduleId)
    {    
        $data['moduleId'] = $moduleId;
        return $this->import('modulePic')->create($data);
		
    }
	
	
	//编辑首页模块广告图
    public function updatePic($data, $id)
    {
        $r['eq'] = array('id'=>$id);
        return $this->import('modulePic')->modify($data, $r);
    }
	
	//删除首页模块广告图
    public function delPic($id, $moduleId)
    {
		$r = array();
        if ( empty($id) && empty($moduleId) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('modulePic')->remove($r);
    }
	
	//首页模块推广链接列表信息
    public function getPicInfo($moduleId,$id)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['eq']['id'] = $id;
        $data = $this->import('modulePic')->find($r);
        return $data;
    }
	
	
	
	
	//首页模块推广链接列表信息
	/**
    public function remove($moduleId,$type)
    {
		if($type == 1){//删除全部
			
			$r['eq']['moduleId'] = $moduleId;
			$this->import('moduleClass')->remove($r);
			$this->import('modulePic')->remove($r);
			$this->import('modulelink')->remove($r);
		}
		$module['eq']['id'] = $moduleId;
		$this->import('module')->remove($module);
		
    
    }
	**/
	
	
}
?>