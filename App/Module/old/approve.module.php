<?
/**
 * 出售接口
 *
 * 获取出售信息，添加出售信息
 * 
 * @package	Module
 * @author	void
 * @since	2015-06-11
 */
class ApproveModule extends AppModule
{
	public $source = array();
    public $status   = array();
	public $type   = array();
	
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'approve' => 'Approve',
		'member'    => 'member',
	);
	
	
	/**
     * 初始化变量值
     * @author	Jeany
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source = array( 1=>'管家' ,2=>'顾问' , 3=> '其他');
        $this->status = array( 1=>'未认证' ,2=>'认证通过' , 3=> '认证未通过');
		$this->type = array( 1=>'邮箱',2=>'资质' );
    }

	
	/**
	 * 获取工作日志分页列表
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getList($page, $num ,$status , $type = true , $order = true)
	{
        $r['limit']         = $num;
        $r['page']          = $page;
		if($type) {
			$r['eq']        = array('type'=>2 , 'status' => 1);//显示资质未认证数据
		}else {
			$r['raw']       = "status != 1";
		}
		if($order == true) {
			$r['order']         = array('createTime' => 'desc');
		} else {
			$r['order']         = array('updateTime' => 'desc');	
		}
        $data               = $this->import('approve')->getRows($r);
        foreach($data['rows'] as $k => $item){
        	$data['rows'][$k]['_source_'] 	= $item['source'];
            $data['rows'][$k]['source'] 	= isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $data['rows'][$k]['status'] 	= isset( $this->status[$item['status']] ) ? $this->status[$item['status']] : $item['status'];
			$data['rows'][$k]['type'] 		= isset( $this->type[$item['type']] ) ? $this->type[$item['type']] : $item['type'];
			$data['rows'][$k]['updateTime'] =  date('Y-m-d H:i:s',$item['updateTime']);
        }
		return $data;
	}
	
	/**
	 * 返回分类数据
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function returnClassData()
	{
		
		$data = array();
		$data['source'] = $this->source;
		$data['status'] = $this->status;
		return $data;
	}
	
	/**
	 * 编辑
	 * @author	Jeany
	 * @since	2015-07-24
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function edit($param)
	{
		$r['eq'] = array('id'=>$param['id']);
		if(isset($param['id']))
		{
			unset($param['id']);	
		}
		if($param['status'] == 2 && empty($param['reason']))
		{
			$param['reason'] = '通过资质认证';
		}
		$param['updateTime'] = time();
		return $this->import('approve')->edit($param , $r);
	}
	
	/**
	 * 查询
	 * @author	garrett
	 * @since	2015-07-24
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function getApprove($id)
	{
		return $this->import('approve')->getApprove($id);
	}
	
	/**
	 * 查询检索
	 * @author	garrett
	 * @since	2015-07-24
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function search($data ,$num , $status = '' , $order = true)
	{
		$where = null;
		//组装模糊查询条件
		if(!empty($data['proposerName']))
		{
			$where = "`proposerName` like '%".trim($data['proposerName'])."%'";
		}
	
		if(!empty($data['contact']))
		{
			if(!empty($where)) {
				$where .= " and `contact` like '%".trim($data['contact'])."%'";
			} else {
				$where = "`contact` like '%".trim($data['contact'])."%'";
			}
		}
		
		//组装精确查找
		if(!empty($data['status']))
		{
			//已认证显示
			if(!empty($where)) {
				if($data['status'] == 4) {
					$where  .= "and `status` != 1 "; 
				}  else {
					$where  .= "and `status` = '".trim($data['status'])."' "; 
				}
			} else {
				if($data['status'] == 4) {
					$where  .= "`status` != 1 "; 
				}  else {
					$where  = "`status` = '".trim($data['status'])."' "; 
				}	
			}
		} else {
			//认证管理，这个是值的
			if(!empty($where))
			{
				$where .= " and  `status` = 1 and `type` = 2 ";
			} else {
				$where  = " `status` = 1 and `type` = 2 "; 
			}
		}
		if(!empty($data['phone']))
		{
			if(!empty($where))
			{
				$where .= " and  `phone` = '".trim($data['phone'])."' ";
			} else {
				$where  = "`phone` = '".trim($data['phone'])."' "; 
			}
		}
		
		if(!empty($data['loginUserName']))
		{
			if(!empty($where))
			{
				$where .= " and  `loginUserName` = '".trim($data['loginUserName'])."' ";
			} else {
				$where  = "`loginUserName` = '".trim($data['loginUserName'])."' "; 
			}
		}
		$r['raw']           = $where;
		$r['limit']         = $num;
        $r['page']          = $data['page'];
		if($order == true) {
			$r['order']         = array('createTime' => 'desc');
		} else {
			$r['order']         = array('updateTime' => 'desc');	
		}
		$data = $this->import('approve')->getRows($r);
		foreach($data['rows'] as $k => $item){
			$data['rows'][$k]['_source_'] = $item['source'];
            $data['rows'][$k]['source'] = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $data['rows'][$k]['status'] = isset( $this->status[$item['status']] ) ? $this->status[$item['status']] : $item['status'];
			$data['rows'][$k]['type'] = isset( $this->type[$item['type']] ) ? $this->type[$item['type']] : $item['type'];
			$data['rows'][$k]['updateTime'] = date('Y-m-d H:i:s',$item['updateTime']);
        }
		return $data;
	}
}
?>