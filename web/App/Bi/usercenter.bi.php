<?
/**
 * 用户中心接口调用
 *
 * 推送商品价格变化数据、
 *
 * @package	Bi
 * @author	Xuni
 * @since	2015-11-05
 */
class UserCenterBi extends Bi
{
	/**
	 * 接口标识
	 */
	public $apiId = 3;

	/**
	 * 推送价格变动数据到用户中心
	 * 
	 * @author	Xuni
	 * @since	2016-03-02
	 *
	 * @access	public
	 * @param	string	$number		商标号
	 * @param	array	$oldInfo	价格修改前的数据
	 * @param	array	$newInfo	价格修改后的数据
	 * 
	 * @return	bool
	 */
	public function pushTmPrice($number, array $oldInfo, array $newInfo)
	{
		$param = array(
			'number'	=> $number,
			'oldInfo'	=> $oldInfo,
			'newInfo'	=> $newInfo,
			'webname'	=> 'tradmin',//不变
			'key'		=> "trademark1104martinewodd",//不变
		);
		
		return $this->request("systemapi/pushTrademarkPrice/", $param);
	}

}
?>