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
	 * 添加/编辑首页模块设置
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function edit()
	{	
		$moduleId 	= $this->input('id', 'int', '0');
		$module = $moduleClass = $moduleAds = $moduleLink = array();
		if($moduleId){
			$module 	= $this->load('module')->getModuleInfo($moduleId);
			$moduleClass = $this->load('module')->getModuleClassList($moduleId);
			
			if($moduleClass['rows']){
				foreach($moduleClass['rows'] as $k => $v){
					$moduleClass['rows'][$k]['itemNum'] = $this->load('module')->getModuleClassItemNum($v['id']);
				}
			}
			$moduleAds  = $this->load('module')->getModuleAdsList($moduleId);
			$moduleLink  = $this->load('module')->getModuleLinkList($moduleId);
		}

		//设置返回的地址,判断请求地址是否包含module/edit,包含则不设置
		$ori_uri = $_SERVER['HTTP_REFERER'];
		if(strpos($ori_uri,'module/edit')===false){
			Session::set('edit_referr',$ori_uri);
		}
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
	public function delModule()
	{	
		$moduleId 	= $this->input('id', 'int', '0');
		
		$res  = $this->load('module')->delModule($moduleId);
		$moduleClass = $this->load('module')->getModuleClassList($moduleId);
		if($moduleClass['rows']){
			
			foreach($moduleClass['rows'] as $k => $v){
				$this->load('module')->delClass($v['id'],$moduleId);
				$this->load('module')->delClassItems($v['id']);
			}
		}
		$this->load('module')->delLink(0,$moduleId);
		$this->load('module')->delPic(0,$moduleId);
		
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
		
	}
	
	
	/**
	 * 添加/编辑模块链接
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function setModule()
	{	
		//参数
		$id 	= $this->input('id','int','0');
		$isUse  = $this->input('isUse','int','0');
		$name = $this->input('name','text','');
		
		if ( $name == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入名称'));
		}
		
		if ( $id <= 0 ){
			$res = $this->load('module')->addModule($name,$isUse);
			$id = $res;
		}else{
			$res = $this->load('module')->updateModule($name, $isUse, $id);
		}
		if ( $res ){
			$this->returnAjax(array('code'=>1,'msg'=>'成功','moduleId'=>$id));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}
	
	/**
	 * 木块分类
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function classes()
	{	
		$moduleId = $this->input('moduleId', 'int', 0);
		$id = $this->input('id', 'int', 0);
		$classes = $classesItem = array();
		if ( $id > 0 ){
			$classes = $this->load('module')->getClassInfo($moduleId, $id);
			$classesItem = $this->load('module')->getModuleClassItemsList($id);
		}
		$this->set('id', $id);
		$this->set('moduleId', $moduleId);
		$this->set('classes', $classes);
		$this->set('classesItem', $classesItem);
		$this->display();
	}

	/**
	 * 添加模块分类
	 *
	 * @author	Dower
	 * @since	2016-02-29
	 * @access	public
	 * @return	void
	 */
	public function addClass(){
		$moduleId = $this->input('moduleId','int',0);
		$name = $this->input('name','string','');
		if(!$moduleId){
			$result['code'] = 1;
			$this->returnAjax($result);
		}
		if(!$name){
			$result['code'] = 2;
			$this->returnAjax($result);
		}
		$classId = $this->load('module')->addClass($name, $moduleId);
		$result['code'] = 0;
		$result['classId'] = $classId;
		//code: 0 成功; 1 非法参数,无module id ;2 分类名不能为空
		$this->returnAjax($result);
	}
	/**
	 * 添加/修改模块分类的商标
	 *
	 * @author	Dower
	 * @since	2016-02-29
	 * @access	public
	 * @return	void
	 */
	public function addClassItem(){
		//获取参数
		$number = $this->input('number', 'int', 0);
		$classId = $this->input('classId', 'int', 0);
		$opt = $this->input('opt', 'string', '');
		$id = $this->input('id', 'int', '0');
		//参数合法性
		if($number && $classId){
			//检测商标是否存在
			$data = $this->load('internal')->getSaleByNumber($number);
			if($data){
				$name = $data['name'];
				//判断是添加还是编辑操作
				if($opt=='edit'){
					//编辑
					$rst['number'] = $number;
					$rst['name'] = $name;
					$data = $this->load('module')->updateClassItems($rst, $id);
					if($data){
						$res['code'] = 10;
						$res['name'] = $name;
						$res['number'] = $number;
						$res['id'] = $id;
					}else{
						$res['code'] = 4;
					}
				}else {
					//添加
					$data = $this->load('module')->addClassItems($number,$name,$classId);
					if($data){
						$res['code'] = 0;
						$res['name'] = $name;
						$res['number'] = $number;
						$res['id'] = $data;
					}else{
						$res['code'] = 3;
					}
				}
			}else{
				$res['code'] = 2;
			}
		}else{
			$res['code'] = 1;

		}
		// code: 0,10 正常;1 非法参数;2 无商标信息;3 添加数据出错;4 编辑数据出错
		$this->returnAjax($res);
	}
	/**
	 * 添加推广链接
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

	/**
	 * 添加、编辑分类
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function setClass()
	{	
		//参数
		$params = $this->getFormData();
		if ( $params['moduleId'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( $params['name'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写分类名称'));
		}
		$classId = $params['id'];
		unset($params['id']);
		
		if ( $classId <= 0 ){
			$params['date'] = time();
			$classId = $this->load('module')->addClass($params['name'], $params['moduleId']);
		}else{
			$this->load('module')->updateClass($params['name'], $classId);
			$this->load('module')->delClassItems($classId);
		}
		
		if($params['numbers']){
			foreach($params['numbers'] as $k => $v){
				$arr = explode("===",$v);
				$this->load('module')->addClassItems($arr[0],$arr[1], $classId);
			}
		}
			
		if ( $classId ){
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
	public function delClass()
	{	
		$moduleId = $this->input('moduleId', 'int', 0);
		$id 	= $this->input('id', 'int', 0);
		if ( $moduleId <= 0 || $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		
		$class = $this->load('module')->getClassInfo($moduleId, $id);
		$res = $this->load('module')->delClass($id, $moduleId);
		$this->load('module')->delClassItems($id);
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
		$classId = $this->input('classId', 'int', 0);
		$updown = $this->input('updown', 'int', 0); //1上，2下
		
		if ( $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		
		$res = $this->load('module')->sortUpDown($id, $updown, $type, array('classId'=>$classId));
		
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'排序失败'));
	}
	
	
	
	
	
}
?>