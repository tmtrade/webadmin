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
class SecondStatusModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'secondstatus' => 'secondstatus',
		);

	/**
	 * 引用业务模型
	 */
	public function details($number, $class)
	{
		$r['eq']				= array('trademark_id' => $number, 'class_id' => $class);
		$r['limit']				= 1;
		$r['col']				= array("status1","status2","status3","status10","status11","status26");
		$data					= $this->import('secondstatus')->find($r);
		if(empty($data)) {
			return "";
		} elseif($data['status1'] == 1){
			return "申请中";
		} elseif($data['status26'] == 1){
			return "冻结中";
		} elseif($data['status2'] == 1){
			$output = "已注册";
			if($data['status10'] == 1){
				$output .= "(需续展)";
			}elseif($data['status11'] == 1){
				$output .= "(续展中)";
			}
			return $output;
		} elseif($data['status3'] == 1){
			return "已无效";
		}
	}
}
?>