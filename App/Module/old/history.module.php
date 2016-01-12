<?php 


class HistoryModule extends AppModule
{

    public $source = array();
    public $deal   = array();

    /**
	* 引用业务模型
	*/
	public $models = array(
		'buy'			=> 'buy',
		'sale'			=> 'sale',
        'member'		=> 'member',
		'track'			=> 'track',
		'buydeal'		=> 'buydeal',
		'history'		=> 'history',
		'saletrademark' => 'saletrademark',
	);

    /**
     * 初始化变量值
     * @author	martin
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source = C("SOURCE");
        $this->deal   = C("BUY_STATUS");
    }

	/**
	 * 购买列表
	 * @author	martin
	 * @since	2015-07-22
	 *
	 * @access	public
     * @param	int 	$p	    页码
     * @param	int 	$num	条数
     * @param	array	$param	参数
	 * @return	array
	 */
	public function getList( $p, $num, $param)
	{
        $r['limit']         = $num;
        $r['page']          = $p;
        $r['order']         = array('confirmDate' => 'desc', 'id'=>'desc');
        $this->paramJudge($r, $param);
        $data               = $this->import('history')->findAll($r);
        foreach($data['rows'] as & $item){
            $item['sourceValue']    = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
        }
		return $data;
	}

    /**
     * 组合搜索条件
     * @author	martin
     * @since	2015-07-24
     *
     * @access	public
     * @param	int 	$r	    输出组合条件
     * @param	array	$param	参数
     * @return	array
     */
    public  function paramJudge(&$r, $param)
    {
        if(!empty( $param['name']) ){
            $r['like']['name'] = $param['name'];
        }
        if(!empty( $param['number']) ){
            $r['eq']['number'] = $param['number'];
        }
        if(!empty( $param['class']) ){
            $r['eq']['class'] = $param['class'];
        }
		$raw	   = array();
		if(!empty($param['sale_start'])){
			$raw[] = strtotime($param['sale_start']." 00:00:00")." <= saleDate";
		}
		if(!empty($param['sale_end'])){
			$raw[] = strtotime($param['sale_end']." 23:59:59")." > saleDate";
		}
		if(!empty($param['deal_start'])){
			$raw[] = strtotime($param['deal_start']." 00:00:00")." <= dealDate";
		}
		if(!empty($param['deal_end'])){
			$raw[] = strtotime($param['deal_end']." 23:59:59")." > dealDate";
		}
		if(!empty($param['confirm_start'])){
			$raw[] = strtotime($param['confirm_start']." 00:00:00")." <= confirmDate";
		}
		if(!empty($param['confirm_end'])){
			$raw[] = strtotime($param['confirm_end']." 23:59:59")." > confirmDate";
		}
		$r['raw']  = implode(" and " , $raw);
		moduleParam("history",'index',$param);
    }


    /**
     * 导出求购信息
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	array	$data	数据
     * @return	void
     */
    public function excel($param)
    {
        $r['limit'] = 1000000;
        $r['p']     = 1;
        $this->paramJudge($r,$param);
        $r['order'] = array('confirmDate' => 'desc', 'id'=>'desc');
        $data       = $this->import("history")->find( $r );
        $output     = array();
        foreach($data as $k=> $item){
            $output[ $k ]['source']         = $this->source[ $item['source'] ];
            $output[ $k ]['saleDate']       = empty($item['saleDate']) ? 0 : date('Y-m-d H:i:s', $item['saleDate']);
            $output[ $k ]['name']           = $item['name'];
            $output[ $k ]['number']         = $item['number'];
            $output[ $k ]['class']          = "第" . $item['class'] ."类";
            $output[ $k ]['price']          = number_format($item['price'],2);
            $output[ $k ]['salePrice']      = $item['salePrice'];
            $output[ $k ]['agencyfee']      = $item['agencyfee'];
            $output[ $k ]['dealDate']       = empty($item['dealDate']) ? 0 : date('Y-m-d H:i:s', $item['dealDate']);
            $output[ $k ]['confirmDate']    = empty($item['confirmDate']) ? 0 : date('Y-m-d H:i:s', $item['confirmDate']);
            $output[ $k ]['attacheId']      = $this->load("member")->getName($item['attacheId']);
        }

        $header   = array('来源渠道' , '出售时间', '商标名称','商标号',  '商标类别', '底价', '成交价', '代理费', '成交时间', '立案时间', '交易专员');

        $fileName = '已出售商标'.date('Y-m-d', time()).'.xls';
        Excel::expcsv_html($header, $output , $fileName);
    }

    /**
     * 已出售详情
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array	$id	数据
     * @return	array
     */
	public function getDetail($id)
	{
		$r['eq'] = array("id" => $id);
		$r['limit'] = 1;
		return $this->import("history")->find($r);

	}


}
?>