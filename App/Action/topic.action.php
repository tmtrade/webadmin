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
	public $rowNum  = 10;
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
        
        //获取SEO设置
		$seoInfo = $this->load('seo')->getInfoByType(10,$id);
        $this->set('seoInfo', $seoInfo);
		
		$this->set('topic', $topic);
		$this->set('topicItems', $topicItems);
		$this->set('referr', $referr);
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
	public function setTopic()
	{	
		//参数
		$params = $this->getFormData();
		if ( $params['id'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( $params['title'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写专题标题'));
		}
		$id = $params['id'];
		unset($params['id']);
		$res = $this->load('topic')->updateTopic($params, $id);
		if ( $res ){
            //设置SEO的信息
            $sid = $this->input('sid', 'int', '0');
            $data['vid']            = $id;
            $data['seotitle']       = $this->input('seo_title', 'string', '');
            $data['keyword']        = $this->input('seo_keyword', 'string', '');
            $data['description']    = $this->input('seo_description', 'string', '');
            $data['isUse']          = $this->input('seo_isUse', 'int', '1');
            $reArr = $this->load('seo')->viewSetSeo($sid,$data,10);
            $this->returnAjax($reArr);
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}
	
	public function add()
	{
		if ( !$this->isPost() ){
			$this->display();
			exit;
		}
		$title = $this->input('title', 'string', '');
        if ( empty($title) ){
            $this->returnAjax(array('code'=>2,'msg'=>'请填写标题'));
        }
        $data = array(
            'title'     => $title,
            'date'      => time(),
            'memberId'  => $this->userId,
        );
        $res = $this->load('topic')->addTopic($data);
        if ( $res ){
            $this->returnAjax(array('code'=>1,'id'=>$res));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}
	
	/**
	 * 弹出添加商标的界面
	 * 
	 * @author	Jeany
	 * @since	2016-02-24
	 * @access	public
	 * @return	void
	 */
	public function items()
	{
		
		$topicId =  $this->input('topicId', 'int', '0');
		$id =  $this->input('id', 'int', '0');
		$items = array();
		if($id > 0){
			$items = $this->load('topic')->getTopicClassInfo($id);
		}
		$this->set('items', $items);
		$this->set('topicId', $topicId);
		$this->display();
		exit;
		
	}
	
	/**
	 * 弹出添加商标的界面
	 * 
	 * @author	Jeany
	 * @since	2016-02-24
	 * @access	public
	 * @return	void
	 */
	public function setitems()
	{
		
		$number   = $this->input('number', 'string', '');
		$topicId = $this->input('topicId', 'int', '');
		$id = $this->input('id', 'int', '');
        if ( empty($number) ){
            $this->returnAjax(array('code'=>2,'msg'=>'请填写商标号'));
        }

		$dataN = $this->load('internal')->getSaleByNumber($number);
		if(!$dataN){		
			$this->returnAjax(array('code'=>2,'msg'=>'不存在该商标'));
		}
		if($dataN['status']!=1){
			$this->returnAjax(array('code'=>2,'msg'=>'商标已下架或审核中'));
		}
        $data = array(
            'name'     => $dataN['name'],
            'number'    => $number,
            'topicId'  	=> $topicId,
        );
		if($id > 0){
			$res = $this->load('topic')->updateTopicClass($data,$id);
		}else{
			$data['date'] = time();
			$res = $this->load('topic')->addTopicClass($data,$topicId);
		}
        
        if ( $res ){
            $this->returnAjax(array('code'=>1,'id'=>$res));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}
	
	
	/**
	 * 获取商标
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function gettrade()
	{	
		$number = $this->input('number', 'int', 0);
		$trade  = array();
		if ( $number > 0 ){
			$data = $this->load('internal')->getSaleByNumber($number);
			if($data){		
				$res['name'] = $data['name'];
				$res['code'] = 1;
			}else{
				$res['code'] = 0;
			}
		}
		$this->returnAjax($res);
	}
	
	
	//图片上传
	public function ajaxUploadPic()
    {
        $msg = array(
            'code'  => 0,
            'msg'   => '',
            'img'   => '',
            );
        if ( empty($_FILES) || empty($_FILES['fileName']) ) {
            $msg['msg'] = '请上传图片';
            $this->returnAjax($msg);
        }
        $obj = $this->load('upload')->uploadAdPic('fileName', 204800, 'img');
        if ( $obj->_imgUrl_ ){
            $msg['code']    = 1;
            $msg['img']     = $obj->_imgUrl_;
        }else{
            $msg['msg']     = $obj->msg;
        }
        $this->returnAjax($msg);
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
		$id   = $this->input('id', 'int', '0');
		
		$res  = $this->load('topic')->delTopic($id);
		$topicClass = $this->load('topic')->getTopicClassList($id);
		if($topicClass['rows']){
			$res  = $this->load('topic')->delTopicClass(0,$id);
		}
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
		
	}
	

	/**
	 * 删除
	 * 
	 * @author	Jeany
	 * @since	2016-02-18
	 * @access	public
	 * @return	void
	 */
	public function delItems()
	{	
		$id   	  = $this->input('id', 'int', '0');
		$topicId  = $this->input('topicId', 'int', '0');
		
		$res  = $this->load('topic')->delTopicClass($id,$topicId);
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
	}
	
	
	/**
	 * 删除推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function sortChaneg()
	{	
		$id = $this->input('id', 'int', 0);
		$type = $this->input('type', 'int', 0);
		$updown = $this->input('updown', 'int', 0); //1上，2下
		$topicId = $this->input('topicId', 'int', 0);
		$where = '';
		if($topicId){
			$where = array('topicId'=>$topicId);
		}
		if ( $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		
		$res = $this->load('topic')->sortUpDown($id, $updown, $type,$where);
		
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'排序失败'));
	}
	
}
?>