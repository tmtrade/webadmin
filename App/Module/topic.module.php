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
class TopicModule extends AppModule
{
    public $models = array(
        'topic'		=> 'topic',
		'topicitems'	=> 'topicitems',
		'member'	=> 'member',
    );
    
	//专题
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['order'] = array('sort'=>'desc');
        $res = $this->import('topic')->findAll($r);
		if($res){
			foreach($res['rows'] as $k => $v){
				$res['rows'][$k]['classNum'] = $this->getTopicClassNum($v['id']);
				$res['rows'][$k]['member'] = $this->getMember($v['memberId']);
			}
		}
        return $res;
    }
	
	//模块基础内容
    public function getMember($memberId)
    {
        $r = array();
        $r['eq']['id']  = $memberId;
        $r['limit'] = 1;
		$r['col'] = array('name');
        $res = $this->import('member')->find($r);
        return $res['name'];
    }
	
	//专题商标数量
    public function getTopicClassNum($moduleId)
    {
        $r = array();
		$r['eq']['topicId'] = $moduleId;
        $num = $this->import('topicitems')->count($r);
        return $num;
    }
	
	
	//模块基础内容
    public function getTopicInfo($moduleId)
    {
        $r = array();
        $r['eq']['id']  = $moduleId;
        $r['limit'] = 1;
        $res = $this->import('topic')->find($r);
        return $res;
    }
	
	//首页模块子分类列表信息
    public function getTopicClassList($topicId)
    {
        $r = array();
		$r['eq']['topicId'] = $topicId;
		$r['limit'] = 100;
        $r['order'] = array('sort'=>'desc');
        $data = $this->import('topicitems')->findAll($r);
        return $data;
    }
	
	//首页模块子分类列表信息
    public function getTopicClassInfo($id)
    {
        $r = array();
        $r['eq']['id']  = $id;
        $r['limit'] = 1;
        $res = $this->import('topicitems')->find($r);
        return $res;
    }
	
	//获取某种类型的最后一个排序值
    public function getLastSort($type,$where = '')
    {
		switch($type){
			case 1: 
				$import = 'topic';
				break;
			case 2: 
				$import = 'topicitems';
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
    //$updown 1：向下，2：向上
    public function sortUpDown($id, $updown, $type, $where='')
    {
        if ( empty($id) ) return false;
		
		switch($type){
			case 1: 
				$import = 'topic';
				break;
			case 2: 
				$import = 'topicitems';
				break;
		}
		
        $rl['eq']   = array('id'=>$id);
        $rl['col']  = array('sort');
        $res = $this->import($import)->find($rl);
        if ( empty($res) ) return false;

        $order = $res['sort'];
        $r['raw']   = $updown == 2 ? " `sort` > $order " : " `sort` < $order ";
        $ord        = $updown == 2 ? 'asc' : 'desc';
        $r['order'] = array('sort'=>$ord);
        if($where){
            $r['eq'] = $where;
        }
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
            return 1;
        }
        return false;
    }
	
	
	//添加首页模块推广链接
    public function addTopic($data)
    {    
		$sort = $this->getLastSort(1);
		$data['sort'] =  $sort + rand(2,5);
        return $this->import('topic')->create($data);
    }
	
	//编辑首页模块推广链接
    public function updateTopic($data, $id)
    {
        $r['eq'] = array('id'=>$id);
        return $this->import('topic')->modify($data, $r);
    }
	
	//删除首页模块推广链接
    public function delTopic($id)
    {
		$r = array();
        if ( empty($id) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('topic')->remove($r);
    }
	
	
	
	//添加首页模块推广链接
    public function addTopicClass($data,$topicId)
    {    
		$sort = $this->getLastSort(2,array('topicId'=>$topicId));
		$data['sort'] = $sort + rand(2,5);
        return $this->import('topicitems')->create($data);
		
    }
	
	
	//编辑首页模块推广链接
    public function updateTopicClass($data, $id)
    {
        $r['eq'] = array('id'=>$id);
        return $this->import('topicitems')->modify($data, $r);
    }
	
	//删除首页模块推广链接
    public function delTopicClass($id, $topicId)
    {
		$r = array();
        if ( empty($id) && empty($topicId) ) return false;
		if($id > 0){
			$r['eq'] = array('id'=>$id);
		}else{
			$r['eq'] = array('topicId'=>$topicId);
		}
        return $this->import('topicitems')->remove($r);
    }
}
?>