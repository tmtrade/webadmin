<?
/**
 * API组件
 *
 * 处理API接口方法
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-06-18
 */
class ApiModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'member'  	=> 'member',
		'track'  	=> 'track',
		'buy'  		=> 'buy',
		);


    /**
     * 获取出售信息
     *
     * @return    	array
     * @author    	martin
     * @copyright 	CHOFN
     * @since    	2015/11/4
     */
	public function checkBuyId($crmid)
	{
		$crmid		= (int) $crmid;
		$r['eq']	= array("crmInfoId" => $crmid);
		$r['col']	= array("id", "attacheId", 'status');
		$data		= $this->import("buy")->find($r);
		return  $data;
	}


    /**
     * 插入跟踪记录
     *
     * @return    	array
     * @author    	martin
     * @copyright 	CHOFN
     * @since    	2015/11/4
     */
	public function insertTrack($buyinfo, $params)
	{
		$data	= array(
				'attacheId'	=> $buyinfo['attacheId'],
				'buyId'		=> $buyinfo['id'],
				'saleId'	=> 0,
				'status'	=> $buyinfo['status'],
				'info'		=> "[".$params['username']."]".$params['remark'],
				'memo'		=> '',
		);
		$r['eq'] = $data;
		$num	 = $this->import("track")->count($r);
		if($num){
			return false;
		}
		$data['date']	= time();
		$bool = $this->import("track")->create($data);
		return $bool;
	}
}
?>