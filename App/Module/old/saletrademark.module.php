<?
/**
 * 出售详细信息
 *
 * 获取出售信息，添加出售信息
 * 
 * @package	Module
 * @author	Jeany
 * @since	2015-07-24
 */
class SaletrademarkModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'saletrademark' => 'Saletrademark',
	);

	/**
	 * 添加出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function Saletrademark($param)
	{
		return $this->import('saletrademark')->create($param);
	}
	
	
	/**
	 * 通过出售ID获取出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function getDetail($id)
	{	
		$r['limit']             = 1;
        $r['eq']['saleId']      = $id;
        $data                   = $this->import("saletrademark")->find($r);
        return $data;
	}
	
	
	/**
	 * 通过注册号获取出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function getSaleTradeByWhere($params)
	{	
		if($params){
			foreach($params as $key => $value){
				$r['eq'][$key]      = $value;
			}
		}
		$r['limit']             = 1;
        $data                   = $this->import("saletrademark")->find($r);
        return $data;
	}
	
	
	/**
	 * 出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function getGroup($id)
	{	
		$r['limit']           = 1;
        $r['eq']['saleId']    = $id;
		$r['col']             = array('group');
        $data                 = $this->import("saletrademark")->find($r);
        return $data['group'];
	}
	
	/**
	 * 出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function getBz($id)
	{	
		$r['limit']           = 1;
        $r['eq']['saleId']    = $id;
		$r['col']             = array('name');
		$r['raw']             = " ( bzpic != '' or valueAnalysis != '' or intro != '' or introTwo != '' ) ";
        $data                 = $this->import("saletrademark")->find($r);
        return $data['name'];
	}
	
	/**
	 * 编辑出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function editSaletrademark($id,$param)
	{
		$r['eq'] = array( "saleId" => $id );
		return $this->import('saletrademark')->modify($param,$r);
	}
	
	
	/**
	 * 编辑出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function editSaletrademarkByN($number,$param)
	{
		$r['eq'] = array( "number" => $number );
		return $this->import('saletrademark')->modify($param,$r);
	}
}
?>