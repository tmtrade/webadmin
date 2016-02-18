<?
header("Content-type: text/html; charset=utf-8"); 
/**
 * 首页模块设置
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-01-8
 */
class moduleAction extends AppAction
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
		
		$res 	= $this->load('module')->getList($params, $page, $this->rowNum);

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
	 * 添加首页模块设置
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function add()
	{	
		$this->display();
	}
	
	
	/**
	 * 添加推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function pic()
	{	
		$moduleId = $this->input('moduleId', 'int', 0);
		$id = $this->input('id', 'int', 0);
		if ( $id > 0 ){
			$pic = $this->load('module')->getPicInfo($moduleId, $id);
		}else{
			$pic = array();
		}
		$this->set('id', $id);
		$this->set('moduleId', $moduleId);
		$this->set('pic', $pic);
		$this->display();
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
        $obj = $this->load('upload')->uploadAdPic('fileName', 51200, 'img');
        if ( $obj->_imgUrl_ ){
            $msg['code']    = 1;
            $msg['img']     = $obj->_imgUrl_;
        }else{
            $msg['msg']     = $obj->msg;
        }
        $this->returnAjax($msg);
    }
	
	/**
	 * 添加推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function setPic()
	{	
		//参数
		$params = $this->getFormData();
		if ( $params['moduleId'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( $params['pic'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		$id = $params['id'];
		
		unset($params['id']);
		if ( $id <= 0 ){
			$params['date'] = time();
			$res = $this->load('module')->addPic($params, $params['moduleId']);
			$id = $res;
		}else{
			//$pic = $this->load('module')->getPicInfo($moduleId, $id);
			//if($pic['pic']){
			//	unlink($pic['pic']);
			//}
			$res = $this->load('module')->updatePic($params, $id);
		}
		if ( $res ){
			$this->returnAjax(array('code'=>1,'msg'=>'成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}
	
	/**
	 * 删除推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function delPic()
	{	
		$moduleId = $this->input('moduleId', 'int', 0);
		$id 	= $this->input('id', 'int', 0);
		if ( $moduleId <= 0 || $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		
		$pic = $this->load('module')->getPicInfo($moduleId, $id);
		if($pic['pic']){
			unlink(".".$pic['pic']);
		}
		
		$res = $this->load('module')->delPic($id, $moduleId);
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
	}
	
	
	
	/**
	 * 添加推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function link()
	{	
		$moduleId = $this->input('moduleId', 'int', 0);
		$lId = $this->input('lId', 'int', 0);
		if ( $lId > 0 ){
			$link = $this->load('module')->getLinkInfo($moduleId, $lId);
		}else{
			$link = array();
		}
		$this->set('lId', $lId);
		$this->set('moduleId', $moduleId);
		$this->set('link', $link);
		$this->display();
	}
	
	
	/**
	 * 添加推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function setLink()
	{	
		//参数
		$params = $this->getFormData();
		if ( $params['moduleId'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( $params['title'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入名称'));
		}
		$lId = $params['lId'];
		
		unset($params['lId']);
		if ( $lId <= 0 ){
			$params['date'] = time();
			$res = $this->load('module')->addLink($params, $params['moduleId']);
			$lId = $res;
		}else{
			$res = $this->load('module')->updateLink($params, $lId);
		}
		if ( $res ){
			$this->returnAjax(array('code'=>1,'msg'=>'成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}
	
	/**
	 * 删除推广链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function delLink()
	{	
		$moduleId = $this->input('moduleId', 'int', 0);
		$id 	= $this->input('id', 'int', 0);
		if ( $moduleId <= 0 || $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		$res = $this->load('module')->delLink($id, $moduleId);
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
	}
	
	
	/**
	 * 添加首页模块设置
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function edit()
	{	
		$moduleId 	= $this->input('id', 'int', '0');
		$module 	= $this->load('module')->getModuleInfo($moduleId);
		$moduleClass = $this->load('module')->getModuleClassList($moduleId);
		$moduleAds  = $this->load('module')->getModuleAdsList($moduleId);
		$moduleLink  = $this->load('module')->getModuleLinkList($moduleId);
		
		$referr = Session::get('edit_referr');
		$this->set('module', $module);
		$this->set('moduleClass', $moduleClass);
		$this->set('moduleAds', $moduleAds);
		$this->set('moduleLink', $moduleLink);
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
	public function delete()
	{	
		$moduleId 	= $this->input('id', 'int', '0');
		$this->import('module')->remove($role);
		$module 	= $this->load('module')->getModuleInfo($moduleId);
		$moduleClass = $this->load('module')->getModuleClassList($moduleId);
		$moduleAds  = $this->load('module')->getModuleAdsList($moduleId);
		$moduleLink  = $this->load('module')->getModuleLinkList($moduleId);
	}
}
?>