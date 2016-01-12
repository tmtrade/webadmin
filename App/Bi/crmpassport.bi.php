<?php

error_reporting(E_ALL);

/**
 * 定义接口地址
 * 本地测试环境：http://demo.chofn.com/
 * 线上生产环境：http://system.chofn.net/
 * @var string
 */
class CrmPassportBi extends Bi
{
	/**
	 * $host 定义接口地址
	 * 本地测试环境：http://demo.chofn.com/
	 * 线上生产环境：http://system.chofn.net/
	 * @var string
	*/

	//public $host = 'http://system.chofn.net/';//线上
	public $host = 'http://demo.chofn.com/';//本地测试
	
	/**
	 * 定义接口用户名
	 * @var string
	 */
	public $user = '';
	
	/**
 	* 定义接口密钥
 	* @var string
	*/
	public $key  = '';
	
	/**
	 * 接口标识
	 */
	public $apiId = 2;
	
	/**
	 * 用超凡id获取申请人
	 * @since  2015-06-23
	 * @param  int   $uid   超凡id
	 * @return json 
	 */
	public function checkCrmName($name)
	{
		$param = array(
				'api_type' 	=> 'checkUser',
				'api_key' 	=> 'J6tUFlJnoWi781qGfxZN8n',
				'api_user' 	=> 'user',
				'username' 	=> $name,
		);
		$res = $this->requests($param);
		return $res;
	}
	

	/**
	 *请求接口
	 * @author	garrett
	 * @since	2015-06-16
	 * 
	 * @access	public
	 * @param	$param 相关参数
	 * @return	array   返回申请人所有信息
	 */
	public function requests( $params )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, $this->host.'Api/user.php');
		curl_setopt(
			$ch, CURLOPT_POSTFIELDS, $params
		);
		$result = curl_exec($ch);
		
		if($result === false) {
			$result = curl_error($ch);
		}
		curl_close($ch);
		return json_decode(trim($result,chr(239).chr(187).chr(191)),true);
	}

	/**
	 * 用超凡id获取申请人
	 * @since  2015-06-23
	 * @param  int   $uid   超凡id
	 * @return json 
	 */
	public function insertCrmMember($data)
	{
		$param = array(
				'CRM_API_HOST' 	=> 'http://demo.chofn.com:88/',
				'CRM_API_USER' 	=> 'tmsystem',
				'CRM_API_KEY' 	=> 'KU6IjH2tSzgg7FDa',
				'api_type'		=> 'networkJoin',
				'id'			=> 100001,
		);
		$param = array_merge($param,$data);

		return $this->requestTrack($param);
	}

	/**
	 *转移到第三顾问
	 * @author	martin
	 * @since	2015/11/6
	 * 
	 * @access	public
	 * @param	$param 相关参数
	 * @return	bool  
	 */
	public function requestTrack($param, $url = '')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, $param['CRM_API_HOST'].'Api/');
		curl_setopt(
			$ch, CURLOPT_POSTFIELDS,
			array_merge(
				array(
					'api_user' => $param['CRM_API_USER'],
					'api_key' => md5($param['CRM_API_KEY'].$param['id'])
				),
				$param
			)
		);
		$result = curl_exec($ch);
		if($result === false) {
			$result = curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
}