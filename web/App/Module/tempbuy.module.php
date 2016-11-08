<?
/**
 * 成功案例
 *
 * 获取案例信息，添加案例信息
 * 
 * @package	Module
 * @author	void
 * @since	2015-10-10
 */
class TempBuyModule extends AppModule
{
	
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'tempbuy'		=> 'tempbuy',
		'member'		=> 'member',
		'buy'			=> 'buy',
		'tempuser'		=> 'tempuser',
    );
	
	/**
	 * 获取工作日志分页列表
	 * @author	Jeany
	 * @since	2015-10-10
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getList($param, $num )
	{
		if(!empty($param['mobile'])){
			$r['like']['mobile'] = $param['mobile'];
		}
		if(!empty($param['need'])){
			$r['like']['need'] = $param['need'];
		}
        $r['limit']         = $num;
        $r['page']          = $param['page'];
        $r['order']         = array('status' => 'asc','id' => 'desc');
        $r['raw']			= "date < " . ( time()-300 );
		
        $data = $this->import('tempbuy')->findAll($r);
        $data['rows'] = $this->getRecentBuy($data['rows']);
		return $data;
	}

	public function getRecentBuy($data)
	{
		foreach ($data as $k => $v) {
			$r['eq'] 	= array('phone'=>trim($v['mobile']),'source' => 10);
			$r['order'] = array('date'=>'desc');
			//$r['score']	= array('date'=>array(time()-3600, time()));
			$res		= $this->import('buy')->count($r);
			if ( $res && $v['status'] == 0){
				//$data[$k]['recentDate'] = $res['date'];
				//$data[$k]['recentDay'] 	= number_format(($v['date']-$res['date'] )/(3600),2);
				$data[$k]['green'] 	= 1;
			}else{
				$data[$k]['green'] 	= 0;
			}

		}
		return $data;
	}
	
	
	/**
	 * 添加出售信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function addCase($param)
	{
		return $this->import('tempbuy')->add($param);
	}
	
	
	
	/**
	 * 获取单个案例信息
	 * @author	void
	 * @since	2015-10-10
	 *
	 * @access	public
	 * @param	int		$username  用户名称
	 * @return	array
	 */
	public function get($param)
	{
		foreach($param as $k => $v){
			if(!empty($v)){
				$r['eq'][$k] = $v;
			}
		}
		$case = $this->import('tempbuy')->find($r);
		return !empty($case) ? $case : '' ;
	}
	
	
	/**
	 * 编辑出售信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function editCase($id,$param)
	{
		$r['eq']	= array( "id" => $id );
		$temp		= $this->import('tempbuy')->get($id);
		$this->pushCrm($temp);
		return $this->import('tempbuy')->remove($r);
	}
	
	/**
	 * 删除案例信息
	 * @author	Jeany
	 * @since	2015-10-10
	 *
	 * @access	public
	 * @id	int
	 * @return	array
	 */
	public function delCase($id)
	{
		$r['eq'] = array( "id" => $id );
		return $this->import('tempbuy')->remove($r);
	}
	
	/**
	 * 提取信息
	 * @author	martin
	 * @since	2015/12/8
	 *
	 * @access	public
	 * @id	int
	 * @return	array
	 */
	public function dealinfo($id , $status)
	{
		$temp			= $this->import('tempbuy')->get($id);
		if(!empty($temp)){
			$temp['mobile']	= trim($temp['mobile']);
			$q['raw']	= "mobile='".$temp['mobile']."' and status = 1 and id!=".$temp['id'];

			$cmrinfo	= $this->load('tempbuy')->pushCrm($temp);
			$count		= $this->import('tempbuy')->count($q);

			//添加新用户发送短信
			$user		= $this->addUser($temp);
			//插入求购信息
			$buyinfo	= array(
						'phone'		=> $temp['mobile'],
						'contact'	=> $temp['name'],
						'need'		=> $temp['need'],
						'source'	=> 10,
						);
			$isexist	= $this->load("buy")->isExist( $buyinfo );
			if($isexist == false){
				$buyinfo['loginUserId'] = $user;
				$buyinfo['date']		= time();
				$buyinfo['crmInfoId']	= $cmrinfo;

				$this->import('buy')->create($buyinfo);
			}else{
				$logininfo		= array('loginUserId' => $user);
				$modify['eq']	= array(
								'phone'			=> $temp['mobile'],
								'contact'		=> $temp['name'],
								'source'		=> 10,
								'status'		=> 6,
								'loginUserId'	=> 0,
								);

				$this->import('buy')->modify($logininfo, $modify);
			}

			$r['eq']	= array( "id" => $id );
			//$param		= array('status' => 1, 'userId'=>$user);
			//$this->import('tempbuy')->modify($param, $r);
			$this->import('tempbuy')->remove($r);
			return true;
		}
		return false;

	}

	public function addUser($temp)
	{
		$bool = $this->importBi('passport')->get($temp['mobile'],2);
		if( empty($bool['data']) ){
			$p['eq']	= array('username'=>$temp['mobile']);
			$pa			= $this->import('tempuser')->find($p);
			$this->import('tempuser')->remove($p);
			if($pa){
				$pass   = $pa['password'];
			}else{
				$pass   = randCode(8);//生成8位随机密码
			}
			$userId = $this->importBi('passport')->register($temp['mobile'], $pass, 2, $temp['ip']);
			if (!isset($userId['data']) || empty($userId['data']) ) return 0;
				
			if(!$pa){
				$msgTemp = C('MSG_TEMPLATE');
				$msg = sprintf($msgTemp['register'], $pass);
				$res = $this->importBi('Message')->sendMsg($temp['mobile'], $msg);
			}
			if (isset($userId['code']) && $userId['code'] == 1){
				return $userId['data']['id'];
			}else{
				return 0;
			}
		}
		return $bool['data']['id'];
	}


	public function pushCrm($temp)
	{
		//来源站点（0：后台查标[.COM] 2：在线咨询[.COM]  4：400电话咨询）
		$post['source'] 	= 0;
		$post['username'] 	= 'Yally';//顾问id
		$post['company'] 	= '';//公司名称
		$post['pttype'] 	= 1; //类型（1：求购 2：出售）
		$post['subject'] 	= '求购商标';//注册名称
		$post['remarks']	= $temp['need'];//备注
		$post['name'] 		= $temp['name'];//联系人
		$post['address'] 	= '';//客户联系地址
		$post['postcode'] 	= '';//客户邮编
		$post['tel'] 		= $temp['mobile'];//电话
		$post['email'] 		= '';//邮件
		$post['area'] 		= $temp['area'];///地区
		$post['sid'] 		= $temp['sid'];//
		$json = $this->importBi('CrmPassport')->insertCrmMember($post);
		$output				=  json_decode($json,1);
		if(isset($output['data']['id']) && $output['data']['id'] > 0){
			return $output['data']['id'];
		}else{
			return 0;
		}

	}
}
?>