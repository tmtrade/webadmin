<?php 
/**
 * 登录
 *
 * 网站首页
 *
 * @package	Action
 * @author	void
 * @since	2014-12-17
 */
class VisitlogAction extends AppAction
{

	/**
	 * 访问者数据跟踪
	 * @author	Far
	 * @since	2016-05-17
	 * @access	public
	 * @return	void
	 */
	public function userlist()
	{
		$this->display();
	} 
        
        /**
	 * 模块使用频率
	 * @author	Far
	 * @since	2016-05-17
	 * @access	public
	 * @return	void
	 */
	public function frequency()
	{
		$this->display();
	} 
        
        /**
	 * 模块走势图
	 * @author	Far
	 * @since	2016-05-17
	 * @access	public
	 * @return	void
	 */
	public function trendChart()
	{
		$this->display();
	} 
        
        /**
	 * 热门搜索，筛选页
	 * @author	Far
	 * @since	2016-05-17
	 * @access	public
	 * @return	void
	 */
	public function search()
	{
		$this->display();
	} 

}
?>