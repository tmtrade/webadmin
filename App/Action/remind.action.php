<?
/**
 * 网站首页
 *
 * 网站首页
 *
 * @package	Action
 * @author	martin
 * @since	2017-07-22
 */
class RemindAction extends AppAction
{
    /**
	 * 设置提醒
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{


	}

    /**
	 * 设置提醒
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function setRemind()
	{
		$search = $this->getFormData();
		$bool	= $this->load("remind")->saveRemind($search);
		
		echo $bool == true ? "1" : "添加失败！"; die;
	}

    /**
	 * 设置提醒
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function closeRemind()
	{
        $buyid	= $this->input("remindid","int");
		$bool	= $this->load("remind")->closeRemind($buyid);
		echo $bool == true ? "1" : "关闭失败！"; die;
	}


    /**
	 * 是否标红
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function isRed($buyId)
	{
		$return = $this->load('remind')->getRemindRed($buyId);
		return $return;
	}

	/**
	 * 提醒弹出框
	 * 
	 * @author	martin
	 * @since	2015/11/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function countRemind()
	{
		//$return = $this->load('remind')->getRemindDiv();
		$res = array(
			'count' => 10,
			);
		$this->returnAjax($res);
	}

}
?>