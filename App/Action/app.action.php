<?
/**
 * 应用公用控制器
 *
 * 存放应用的公共方法
 *
 * @package	Action
 * @author	void
 * @since	2015-01-28
 */
abstract class AppAction extends Action
{
	/**
	 * 每页显示多少行
	 */
	public $rowNum  = 20;
	
	public $username = null;
	
	public $userId   = null;
	
	public $roleId   = null;
	/**
	 * 前置操作(框架自动调用)
	 * @author	void
	 * @since	2015-01-28
	 *
	 * @access	public
	 * @return	void
	 */
	public function before()
	{
		//自定义业务逻辑
		$this->username = Session::get('username');
		$this->userId   = Session::get('userId');

		//有用户账号就必须判断账号是否有效
		if ( !empty($this->username) ){
			$userinfo = $this->load('member')->get($this->username);
			if(empty($userinfo) || $userinfo['isUse'] == 2) {
				Session::clear();
				$this->redirect('', '/role/error');
			}
			$this->roleId 	= $userinfo['roleId'];
			$roleInfo 		= $this->load('role')->getRoleById($this->roleId);
			$_role 			= empty($roleInfo['role']) ? array() : explode(',', $roleInfo['role']);
			if ( $roleInfo['isUse'] == 2 ){
				Session::clear();
				$this->redirect('', '/role/error');
			}
			$this->hasRole = $_role;
			$this->set('roleId' , $this->roleId);
			$this->set('_role_' , $this->hasRole);
		}
		
		$roleList = C('ROLE_LIST');
		if ( isset($roleList[$this->mod.'/'.$this->action]) ) {//权限判断范围
			//判断权限时，必须要登录状态
			if ( empty($this->username) || empty($this->userId) ){
				$this->redirect('', '/index/');
			}
			$roleNo = $roleList[$this->mod.'/'.$this->action];
			if ( !in_array($roleNo, $this->hasRole) ){
				$this->redirect('', '/role/error');
			}
		}
		$this->set('username' , $this->username);
		$this->set('userId'   , $this->userId);
	}

	/**
	 * 后置操作(框架自动调用)
	 * @author	void
	 * @since	2015-01-28
	 *
	 * @access	public
	 * @return	void
	 */
	public function after()
	{
		//自定义业务逻辑
	}

	/**
	 * 输出json数据
	 *
	 * @author	Xuni
	 * @since	2015-11-06
	 *
	 * @access	public
	 * @return	void
	 */
	protected function returnAjax($data=array())
	{
		$jsonStr = json_encode($data);
		exit($jsonStr);
	}

}
?>