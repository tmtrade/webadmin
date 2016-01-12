<?
/**
 * 商标模块
 *
 * 商标详情
 * 
 * @package	Module
 * @author	martin
 * @since	2015-07-22
 */
class TrademarkModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'trademark'		=> 'Trademark',
		'proposer'		=> 'proposer',
		'agent'			=> 'agent',
		'secondstatus' => 'secondstatus',
		);

	/**
	 * 引用业务模型
	 */
	public function details($number, $class)
	{
		$r['eq']				= array('id' => $number, 'class' => $class);
		$r['limit']				= 1;
		$data					= $this->import('trademark')->find($r);
		if(empty($data)) return array();
		$data['imgUrl']			= $this->load('imgurl')->getUrl($data['id']);
		$w['eq']				= array('id' => $data['proposer_id']);
		if($data['valid_start'] == '0000-00-00' && $data['valid_end'] != '0000-00-00'){
			$data['valid_start'] = date('Y-m-d',strtotime("-10 year +1 day", strtotime( $data['valid_end'] )));
		}
		$w['limit']				= 1;
		$proposer				= $this->import('proposer')->find($w);
		$data['newId']			= $proposer['newId'];
		$data['proposerName']	= $proposer['name'];
		$status					= $this->load("secondstatus")->details($number, $class);
		$data['newstatus']		= $status;

		return $data;
	}
	
	
	/**
	 * 获取AUTO
	 * @author	Jeany
	 * @since	2015-11-04
	 *
	 * @access	public
	 * @param	array	
	 * @return	array
	 */
	public function getAuto($number,$class)
	{
		$r['eq']	 = array('id' => $number, 'class' => $class);
		$r['limit']	 = 1;
		$r['col']    = array('auto');
		$data		 = $this->import('trademark')->find($r);

		return $data['auto'];
	}
}
?>