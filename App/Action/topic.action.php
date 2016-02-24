<?
header("Content-type: text/html; charset=utf-8"); 
/**
 * 首页模块设置
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-01-8
 */
class topicAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 首页模块设置列表
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function index()
	{	
		//参数
		$params = array();
		$page 	= $this->input('page', 'int', '1');
		
		$res 	= $this->load('topic')->getList($params, $page, $this->rowNum);

		$total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];

		$pager 		= $this->pager($total, $this->rowNum);
        $pageBar 	= empty($list) ? '' : getPageBar($pager);
		$this->set('total', $total);
        $this->set("pageBar",$pageBar);
		$this->set('s', $params);
		$this->set('list', $res['rows']);
		$this->display();
	}
	
	
	/**
	 * 添加/编辑首页模块设置
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function edit()
	{	
	
		$id 	= $this->input('id', 'int', '0');
		$topic = $topicItems = array();
		if($id){
			$topic 	= $this->load('topic')->getTopicInfo($id);
			$topicItems = $this->load('topic')->getTopicClassList($id);
		}
		
		$referr = Session::get('edit_referr');
		$this->set('topic', $topic);
		$this->set('topicItems', $topicItems);
		$this->set('referr', $referr);
		$this->display();
	}
	
	
	/**
	 * 删除
	 * 
	 * @author	Jeany
	 * @since	2016-02-18
	 * @access	public
	 * @return	void
	 */
	public function delTopic()
	{	
		$id 	= $this->input('id', 'int', '0');
		
		$res  = $this->load('topic')->delTopic($moduleId);
		$topicClass = $this->load('topic')->getTopicClassList($id);
		if($topicClass['rows']){
			
			foreach($topicClass['rows'] as $k => $v){
				$this->load('topic')->delTopicClass($v['id'],$id);
			}
		}
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
		
	}
	
}
?>