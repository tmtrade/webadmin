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
        $r['order'] = array('sort'=>'asc');
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
	
	//首页模块子分类数量
    public function getModuleClassItemNum($classId)
    {
        $r = array();
		$r['eq']['classId'] = $classId;
        $num = $this->import('moduleClassItems')->count($r);
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
        $r['order'] = array('sort'=>'asc');
        $data = $this->import('moduleClass')->findAll($r);
        return $data;
    }
	
	//首页模块子分类列表信息
    public function getModuleClassItemsList($classId)
    {
        $r = array();
		$r['eq']['classId'] = $classId;
		$r['limit'] = 100;
        $r['order'] = array('sort'=>'asc');
        $data = $this->import('moduleClassItems')->findAll($r);
        return $data;
    }
	
	
	//首页模块包含广告列表信息
    public function getModuleAdsList($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['limit'] = 100;
		$r['order'] = array('sort'=>'asc');
        $data = $this->import('modulePic')->findAll($r);
        return $data;
    }
	
	//首页模块推广链接列表信息
    public function getModuleLinkList($moduleId)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['limit'] = 100;
		$r['order'] = array('sort'=>'asc');
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
	
	
	//获取某种类型的最后一个排序值
    public function getLastSort($type,$where = '')
    {
		switch($type){
			case 1: 
				$import = 'module';
				break;
			case 2: 
				$import = 'modulelink';
				break;
			case 3: 
				$import = 'modulePic';
				break;
			case 4: 
				$import = 'moduleClass';
				break;
			case 5: 
				$import = 'moduleClassItems';
				break;
		}
		if($where){
				 $r['eq']    = $where;
		}
        $r['order'] = array('sort'=>'desc');
        $r['col']   = array('sort');
        
        $res    = $this->import($import)->find($r);
        $sort  = empty($res) ? 0 : $res['sort']; 
        return $sort;
    }
	
	//对某类别中某项进行上下排序
    //$updown 1：向上，2：向下
    public function sortUpDown($id, $updown, $type)
    {
        if ( empty($id) ) return false;
		
		switch($type){
			case 1: 
				$import = 'module';
				break;
			case 2: 
				$import = 'modulelink';
				break;
			case 3: 
				$import = 'modulePic';
				break;
			case 4: 
				$import = 'moduleClass';
				break;
			case 5: 
				$import = 'moduleClassItems';
				break;
		}
		if($where){
				 $r['eq']    = $where;
		}
		
        $rl['eq']   = array('id'=>$id);
        $rl['col']  = array('sort');
        $res = $this->import($import)->find($rl);
        if ( empty($res) ) return false;

        $order = $res['sort'];

        $r['raw']   = $updown == 1 ? " `sort` > $order " : " `sort` < $order ";
        $ord        = $updown == 1 ? 'asc' : 'desc';
        $r['order'] = array('sort'=>$ord);
        $res = $this->import($import)->find($r);
        if ( empty($res) ) return false;

        $changeOrder    = $res['sort'];
        $changeId       = $res['id'];

        $update1    = array('sort'=>$changeOrder);//需要交换的
        $update2    = array('sort'=>$order);//被交换的


		$rO['eq'] = array('id'=>$id);
		$rOC['eq'] = array('id'=>$changeId);
		$flag1 = $this->import($import)->modify($update1, $rO);
		$flag2 = $this->import($import)->modify($update2, $rOC);

        if ( $flag1 && $flag2 ) {
            return true;
        }
        return false;
    }
	
	
	//添加首页模块推广链接
    public function addModule($name,$isUse)
    {    
        $data['name'] = $name;
		$data['isUse'] = $isUse;
		$sort = $this->getLastSort(1);
		$data['sort'] =  $sort + rand(2,5);
        return $this->import('module')->create($data);
    }
	
	//编辑首页模块推广链接
    public function updateModule($name,$isUse, $id)
    {
        $r['eq'] = array('id'=>$id);
		$data['name'] = $name;
		$data['isUse'] = $isUse;
        return $this->import('module')->modify($data, $r);
    }
	
	//删除首页模块推广链接
    public function delModule($id)
    {
		$r = array();
        if ( empty($id) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('module')->remove($r);
    }
	
	
	
	//添加首页模块推广链接
    public function addLink($data,$moduleId)
    {    
        $data['moduleId'] = $moduleId;
		$sort = $this->getLastSort(2,array('moduleId'=>$moduleId));
		$data['sort'] = $sort + rand(2,5);
		
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
		if($id > 0){
			$r['eq'] = array('id'=>$id);
		}else{
			$r['eq'] = array('moduleId'=>$moduleId);
		}
        
        return $this->import('modulelink')->remove($r);
    }
	
	
	//添加首页模块广告图
    public function addPic($data,$moduleId)
    {    
        $data['moduleId'] = $moduleId;
		$sort = $this->getLastSort(3,array('moduleId'=>$moduleId));
		$data['sort'] = $sort + rand(2,5);
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
		
		if($id > 0){
			$r['eq'] = array('id'=>$id);
		}else{
			$r['eq'] = array('moduleId'=>$moduleId);
		}
		
        return $this->import('modulePic')->remove($r);
    }
	
	
	//添加首页模块分类
    public function addClass($name,$moduleId)
    {    
		
		$sort = $this->getLastSort(4,array('moduleId'=>$moduleId));
		$data['sort'] = $sort + rand(2,5);
		
		$data['name'] = $name;
        $data['moduleId'] = $moduleId;
        return $this->import('moduleClass')->create($data);
		
    }
	
	
	//编辑首页模块分类
    public function updateClass($name, $id)
    {
        $r['eq'] = array('id'=>$id);
		 $data['name'] = $name;
        return $this->import('moduleClass')->modify($data, $r);
    }
	
	//删除首页模块分类
    public function delClass($id, $moduleId)
    {
		$r = array();
        if ( empty($id) && empty($moduleId) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('moduleClass')->remove($r);
    }
	
	//添加首页模块子分类
    public function addClassItems($number,$name,$classId)
    {    
		$sort = $this->getLastSort(5,array('classId'=>$classId));
		$data['sort'] = $sort + rand(2,5);
		$data['name'] = $name;
		$data['number'] = $number;
        $data['classId'] = $classId;
        return $this->import('moduleClassItems')->create($data);
    }
	
	//编辑首页模块子分类
    public function updateClassItems($data, $id)
    {
        $r['eq'] = array('id'=>$id);
        return $this->import('moduleClassItems')->modify($data, $r);
    }
	
	//删除首页模块子分类
    public function delClassItems($classId)
    {
		$r = array();
        if ( empty($classId) ) return false;
        $r['eq'] = array('classId'=>$classId);
        return $this->import('moduleClassItems')->remove($r);
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
    public function getClassInfo($moduleId,$id)
    {
        $r = array();
		$r['eq']['moduleId'] = $moduleId;
		$r['eq']['id'] = $id;
        $data = $this->import('moduleClass')->find($r);
        return $data;
    }
	
	
	
	
}
?>