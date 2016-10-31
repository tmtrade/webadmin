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
		$this->set('module_type', C('MODULE_CLASS'));
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
		$this->set('class_type', C('CLASS_TYPE'));
		$this->set('moduleId', $moduleId);
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
		$type  = $this->input('type','int',1);
		$name = $this->input('name','text','');
		
		if ( $name == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入名称'));
		}
		
		if ( $id <= 0 ){
			$res = $this->load('module')->addModule($name,$isUse,$type);
			$id = $res;
		}else{
			$res = $this->load('module')->updateModule($name, $isUse, $id,$type);
			if($res===-1) $this->returnAjax(array('code'=>3,'msg'=>'操作失败, 分类中含有不同的商品类型'));
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
		$module_type = $this->input('type', 'int', 1);//模块的类型
		$id = $this->input('id', 'int', 0);
		$classes = $classesItem = array();
		if ( $id > 0 ){
			$classes = $this->load('module')->getClassInfo($moduleId, $id);
			$classesItem = $this->load('module')->getModuleClassItemsList($id);
		}
		//得到可选的分类类型
		$types = array(1=>'商标',2=>'专利');
		if($module_type==1){
			$types = array(1=>'商标');
		}elseif($module_type==2){
			$types = array(2=>'专利');
		}
		$this->set('id', $id);
		$this->set('module_type', $types);
		$this->set('moduleId', $moduleId);
		$this->set('classes', $classes);
		$this->set('classesItem', $classesItem);
		$this->display();
	}

	/**
	 * 添加/编辑模块分类
	 *
	 * @author	Dower
	 * @since	2016-02-29
	 * @access	public
	 * @return	void
	 */
	public function addClass(){
		$moduleId = $this->input('moduleId','int',0);
		$id = $this->input('id','int',0);
		$name = $this->input('name','string','');
		$type = $this->input('type','int',1);
        $link = $this->input('link','string','');

		if(!$moduleId){
			$result['code'] = 1;
			$this->returnAjax($result);
		}
		if(!$name){
			$result['code'] = 2;
			$this->returnAjax($result);
		}
		if($id==0){
			$rst = $classId = $this->load('module')->addClass($name, $moduleId,$type,$link);
			$id = $rst;
		}else{
			$classInfo = $this->load('module')->getClassInfo($moduleId, $id);
			$num = $this->load('module')->getModuleClassItemNum($id);
			if ( $classInfo['type'] != $type && $num > 0){
				$result['code'] = 4;
				$result['classId'] = '分类有数据无法修改类型';
				$this->returnAjax($result);
			}
			$rst = $this->load('module')->updateClass($name, $id,$type,$link);
			//$this->load('module')->delClassItems($id);//清空之前的商品项
		}
		if($rst){
			$result['code'] = 0;
			$result['classId'] = $id;
		}else{
			$result['code'] = 3;
			$result['classId'] = '保存信息失败';
		}
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
		$number = $this->input('number', 'string', '');
		$classId = $this->input('classId', 'int', 0);
		$opt = $this->input('opt', 'string', '');
		$type = $this->input('type', 'int', 1);
		$id = $this->input('id', 'int', '0');
		//参数合法性
		if($number && $classId){
			//检测商品是否存在
			if($type==1){
				$data = $this->load('internal')->getSaleByNumber($number);
				//检测商标状态
				if($data && $data['status']!=1){
					$res['code'] = 5;
					$this->returnAjax($res);
				}
			}else{
				//专利数据
				$data = $this->load('patent')->getPatentByNumber($number);
				if($data) $data['name'] = $data['title'];
			}
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
		// code: 0,10 正常;1 非法参数;2 无商品信息;3 添加数据出错;4 编辑数据出错; 5 商标已下架或审核中
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
		$classId = $params['id'];
		unset($params['id']);
		//删除之前的信息
		$this->load('module')->delClassItems($classId);
		//保存当前的信息
		if($params['numbers']){
			foreach($params['numbers'] as $k => $v){
				$arr = explode("===",$v);
				$this->load('module')->addClassItems($arr[0],$arr[1], $classId);
			}
		}
		//返回结果
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
		//设置条件
		$where = '';
		if(isset($_POST['classId'])){
			$classId = $this->input('classId', 'int', 0);
			$where = array('classId'=>$classId);
		}elseif(isset($_POST['moduleId'])){
			$moduleId = $this->input('moduleId', 'int', 0);
			$where = array('moduleId'=>$moduleId);
		}
		$updown = $this->input('updown', 'int', 0); //1上，2下
		
		if ( $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		
		$res = $this->load('module')->sortUpDown($id, $updown, $type, $where);
		
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'排序失败'));
	}
}
?>