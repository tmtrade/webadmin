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
            $frequency = require ConfigDir.'/visitlog.config.php';
            $arr = array();
            $arr1 = array();
            $dateStart 	= $this->input('dateStart', 'string');
            $dateEnd 	= $this->input('dateEnd', 'string');
            foreach ($frequency as $k=>$v){
               $arr[$k]['title'] = $v['title']; 
               $arr[$k]['page_count'] = $this->load('visitlog')->page_count($v['url'], $dateStart, $dateEnd, $v['like']); 
               $arr[$k]['pageUser_count'] = $this->load('visitlog')->pageUser_count($v['url'], $dateStart, $dateEnd, $v['like']);
               foreach ($v['view'] as $key=>$val){  
                   $arr1[$key]['title'] = $val['title']; 
                   $arr1[$key]['count'] = $this->load('visitlog')->pageUrl_count($val['url'],$v['url'], $dateStart, $dateEnd, $v['like'], $val['like']); 
                   $arr1[$key]['zhanbi'] = round($arr1[$key]['count']/$arr[$k]['page_count']*100,2); 
               }
               $arr[$k]['view'] = $arr1; 
               $arr1 = array();
            }
            
            $this->set("list",$arr);
            $this->set("s",array("dateStart"=>$dateStart,"dateEnd"=>$dateEnd));
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