<?php 
class BuyDealModule extends AppModule
{

    public $source = array();

    /**
	* 引用业务模型
	*/
	public $models = array(
		'buydeal'       => 'buydeal',
	);


    /**
     * 初始化变量值
     * @author	martin
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source = C("SOURCE");
        $this->deal   = C("DEAL_STATUS");
    }

    /**
     * 获取成交信息详情
     * @author	martin
     * @since	2015-07-22
     *
     * @access	public
     * @param	array 	$r	    查询条件
     * @return	array
     */
    public function findIdByBuy( $r )
    {
        $r['limit']             = 1;
        $r['eq']['status']      = 1;
        $data                   = $this->import("buydeal")->find($r);
        return $data;
    }

	
    /**
     * 保存数据
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	int	    $id	    求购编号
     * @param	array	$data	数据
     * @return	void
     */
    public function edit($id,$data)
    {
        $r['eq'] = array( "buyId" => $id );
        return  $this->import("buydeal")->modify($data,$r);
    }
}
?>