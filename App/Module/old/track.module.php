<?
/**
 * 跟客记录
 *
 * 
 * @package	Module
 * @author	martin
 * @since	2015-07-23
 */
class TrackModule extends AppModule
{
	/**
	 * 消息模版
	 */
	public $models = array(
		'track' => 'track',
	);


	/**
	 * 获取最新的跟可记录
	 * @author	martin
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	int		$id	    交易信息id
	 * @return	array
	 */
	public function buyTrack( $id )
	{
        $r['eq']    = array("buyId"=>$id);
        $r['limit'] = 1;
        $r['order'] = array("id" => "desc");
        $data = $this->import("track")->find($r);
		return empty($data) ? '' : $data['info'];
	}

	/**
	 * 获取跟踪列表
	 * @author	martin
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	int		$id	    交易信息id
	 * @return	array
	 */
	public function buyTracklist( $id )
	{
        $deal   = C("BUY_STATUS");
        $r['eq']    = array("buyId"=>$id);
        $r['limit'] = 10000;
        $r['order'] = array("id" => "desc");
        $data = $this->import("track")->find($r);
		foreach($data as &$item){
			$item['statusValue'] = $deal[$item['status']];
		}
		return $data;
	}

	/**
	 * 获取最后一条跟踪信息
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @param	int		$id	    交易信息id
	 * @return	array
	 */
	public function getLastTrack( $id )
	{
        $r['eq']    = array("buyId"=>$id);
        $r['limit'] = 1;
        $r['order'] = array("id" => "desc");
        $data = $this->import("track")->find($r);
		return $data;
	}
}
?>