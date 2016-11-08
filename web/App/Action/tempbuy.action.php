<?
/**
 * 求购信息
 *
 * @package	Action
 * @author	Jeany
 * @since	2015-07-22
 */
class TempBuyAction extends AppAction
{
	//	public $debug = true;
	
	/**
	 * 出售列表
	 * 
	 * @author	Jeany
	 * @since	2015-07-22
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{	
	
		$param				= $this->getFormData();
		$data				= $this->load("tempbuy")->getList($param, $this->rowNum);
        $pager				= $this->pager($data['total'], $this->rowNum);
        $pageBar			= empty($data['rows']) ? '' : getPageBar($pager);
		$this->set("search",$param);
        $this->set("pageBar",$pageBar);
        $this->set("data",$data['rows']);
		$this->set("page", $param['page']);
		
		$this->set("where", http_build_query($param));
		$this->display();
	}
	
	/**
	 * 添加出售信息
	 * 
	 * @author	Jeany
	 * @since	2015-07-22
	 *
	 * @access	public
	 * @return	void
	 */
	public function add()
	{	
		if ( $this->isPost() ) {
			
			$number = $this->input("number","string");
			$name 	= $this->input("name","string");
			$class 	= $this->input("class","string");
			$price 	= $this->input("price","string");

			if (empty($number) || empty($name) || empty($class) || empty($price)) exit('2');
			
			
			
			$data = array(
				'number'  => $number,
				'name' 	  => $name,
				'class'   => $class,
				'price'   => $price,
				);
			$case = $this->load('tempbuy')->get($data);
			if ( $case ) exit('3');
			
			$res = $this->load('tempbuy')->addCase($data);
			if ( $res ) exit('1');
			exit('4');
		} else {
			$this->display();
		}	
	}
	

	/**
	 * 编辑案例信息
	 * 
	 * @author	Jeany
	 * @since	2015-10-10
	 *
	 * @access	public
	 * @return	void
	 */
	public function edit()
	{	
		
		$id			= $this->input("id","int");
		$status		= $this->input("status","int");
		
		$param		= array('id'=>$id);
		$rows       = $this->load("tempbuy")->get($param);
		$data		= array('status' => $status);
		$bool       = $this->load("tempbuy")->editCase($id, $data);
		if($bool){
			$this->redirect('','/tempbuy/index/');
		}
		
	}
	
	
	/**
	 * 删除信息
	 * 
	 * @author	Jeany
	 * @since	2015-10-10
	 *
	 * @access	public
	 * @return	void
	 */
	public function del()
	{	
		
		$id = $this->input("id","int");
		if (empty($id)) exit('2');
		
		$res = $this->load('tempbuy')->delCase($id);
		if ( $res ) exit('1');
		exit('4');
		
	}
	
	/**
	 * 删除信息
	 * 
	 * @author	Jeany
	 * @since	2015-10-10
	 *
	 * @access	public
	 * @return	void
	 */
	public function mark()
	{
		$id			= $this->input("id","int");
		$status		= $this->input("status","int");
		$bool		= $this->load("tempbuy")->dealinfo($id , $status);
		if($bool){
			$this->redirect('操作成功！','/tempbuy/index/');
		}else{
			$this->redirect('该信息已处理！','/tempbuy/index/');
		}
	}
}
?>