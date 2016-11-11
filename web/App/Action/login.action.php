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
		$info = $this->load('member')->getMemberById(1);
		if ( !empty($info) ){
			$info['userId'] = $info['id'];
			$_userinfo 			= serialize($info);

			Session::set(COOKIE_USER , $_userinfo, 3600*8);
		}else{
			exit('error');
		}

		if(isset($_GET['key']) && empty($_GET['key'])) exit('error');
		$userinfo = ucClient::userInfo($_GET['key']);
		
		if(!is_array($userinfo)) exit('error');
		$userinfo['staffId'] = $userinfo['staffid'];
		$userId = $this->load('member')->setMember($userinfo);

		if(!empty($userinfo))
		{
			$userinfo['userId'] = $userId;
			$_userinfo 			= serialize($userinfo);

			Session::set(COOKIE_USER , $_userinfo, 3600*8);
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
		Session::remove(COOKIE_USER);
		$this->redirect('', '/index/');
	}
}
?>