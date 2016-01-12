<?php 
class RemindModule extends AppModule
{
	
	/**
	* 引用业务模型
	*/
	public $models = array(
		'remind'	=> 'buyRemind',
		'buy'		=> 'buy',
		'track'		=> 'track',
	);
	
    /**
	 * 新增提醒设置
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function saveRemind($search){
		$r['eq'] = array('buyId'	=>$search['buyid']);
		$data	 = array('isUser'	=>2);
		$this->import("remind")->modify($data, $r);

		$search['attacheId']		= Session::get('userId');
		if($search['open'] == 2){
			$search['number']		= 0;
			$search['interval']		= 0;
		}else{
			if($search['number'] <= 0){
				echo "请设置间隔时间"; die;
			}
		}
		$search['addTime']			= $search['setTime'];
		$bool = $this->import("remind")->create($search);
		return $bool;
	}
    /**
	 * 关闭提醒设置
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function closeRemind($buyid){
		$r['eq'] = array('buyId' =>$buyid, 'isUser'	=>1,'attacheId' => Session::get('userId'));
		$data	 = array('isUser'=>2);

		$bool	 = $this->import("remind")->modify($data, $r);
		if($bool){
			$save		= array('remindTime'	=> 0);
			$b['eq']	= array('id'			=> $buyid);
			$this->import("buy")->modify($save, $b);
		}
		return $bool;
	}


    /**
	 * 列表查看，是否提醒
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function getRemindRed( $buyId ){
		$attacheId		= Session::get('userId');

		$r['eq']		= array(
						"buyId"		=>$buyId,
						"attacheId" =>$attacheId,
						"isUser"	=> 1,
		);
		$r['raw']		= "addTime <= ".time();
		/*
		$nowdate		= date("Y-m-d",time());
		$maxdate		= strtotime($nowdate . " 23:59:59");
		$r['raw']		= "setTime <".$maxdate;
		*/
		$r['limit']		= 1;
		$remind			= $this->import("remind")->find($r);
		if($remind == false){
			return 0;
		}
		/*
		$track			= $this->load("track")->getLastTrack($buyId);
		if($track == true && date('Y-m-d', $track['date']) >= date('Y-m-d',$remind['addTime'])){
			return 0;
		}*/
		return 1;
	}

	/**
	 * 提醒弹框
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function getRemindDiv( ){
		$attacheId		= Session::get('userId');
		$timeType		= array(1 => "Minute", 2 => "hour", 3 => "day");
		$r['eq']		= array(
						"attacheId" =>$attacheId,
						"isUser"	=> 1,
		);
		
		$r['raw']		= "setTime <".time()." and (isRemind=0 and open=2 or open=1)";
		$r['limit']		= 1000;
		$remind			= $this->import("remind")->findAll($r);
		foreach($remind['rows'] as &$item){
			$now					= time();

			if($item['open'] == 1){
				$interval			= $timeType[$item['interval']];
				$setTime			= strtotime("+".$item['number']." ".$interval,$item['setTime']);
				$data['setTime']	= $setTime > $now ? $setTime : $now;
			}
			$data['isRemind']		= 1;
			$q['eq']				= array("id" => $item['id']);
			$bool					= $this->import("remind")->modify($data, $q);
			if($bool){
				$save				= array('remindTime'	=> $now);
				$b['eq']			= array('id'			=> $item['buyId']);
				$this->import("buy")->modify($save, $b);
			}

			$buyInfo				= $this->import("buy")->get($item['buyId']);
			$item['phone']			= $buyInfo['phone'];
			$item['time']			= date("m-d H:i", $now);
		}
		return $remind['rows'];
	}
	

	/**
	 * 获取出售信息的提醒内容
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function getLastRemind($id){
		$r['eq']	= array("isUser"=>'1', 'buyId'=>$id);
		$r['order']	= array("id"=>"desc");
		$r['limit']	= 1;
		$data		= $this->import("remind")->find($r);
		if($data==false){
			return "";
		}else{
			return $data['memo'];
		}
	}
	
	/**
	 * 获取出售信息的提醒内容
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function getLastRemindInfo($id){
		$r['eq']	= array("isUser"=>'1', 'buyId'=>$id);
		$r['order']	= array("id"=>"desc");
		$r['limit']	= 1;
		$data		= $this->import("remind")->find($r);
		if($data==false){
			return array();
		}else{
			return $data;
		}
	}

}
?>