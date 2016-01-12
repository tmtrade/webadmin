<?php 
/**
 * 登录
 *
 * 网站首页
 *
 * @package	Action
 * @author	void
 * @since	2014-12-17
 */
class LoginAction extends AppAction
{

	/**
	 * 登录
	 * 
	 * @author	garrett
	 * @since	2015-07-16
	 *
	 * @access	public
	 * @return	void
	 */
	public function synlogin()
	{
		if(isset($_GET['key']) && empty($_GET['key'])) exit('error');
		$userinfo = ucClient::userInfo($_GET['key']);

		if(!is_array($userinfo)) exit('error');

		$userinfo['staffId'] = $userinfo['staffid'];
		$userId = $this->load('member')->setMember($userinfo);

		if(!empty($userinfo))
		{
			Session::set('username' , $userinfo['username'], 3600*8);
			Session::set('token'    , $userinfo['token'], 3600*8);
			Session::set('userId'   , $userId, 3600*8);
		}
	} 
	
	/**
	 * 退出
	 * 
	 * @author	garrett
	 * @since	2015-07-16
	 *
	 * @access	public
	 * @return	void
	 */
	public function synloginout()
	{
		Session::clear();
		$this->redirect('', '/index/');
	}
}
?>