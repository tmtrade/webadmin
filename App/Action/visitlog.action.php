<?php 
/**
 * 网站统计
 *
 * @package	Action
 * @since	2016-05-17
 *
 */
class VisitlogAction extends AppAction
{
	public $rowNum  = 10;

	/**
	 * 访问者数据跟踪列表
	 * @author	dower
	 * @since	2016-05-17
	 * @access	public
	 * @return	void
	 */
	public function userlist()
	{
		//参数
		$page 	= $this->input('page', 'int', '1');
		$res 	= $this->load('visitlog')->getUserList( $page, $this->rowNum);
		//得到列表数据
		$total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];
		$total = ceil($total/$this->rowNum);
		//渲染页面
		$this->set('total', $total);
		$this->set('page', $page);
		$this->set('list', $res['rows']);
		$this->display();
	}

	/**
	 * 访问者数据跟踪详情
	 * @author	dower
	 * @since	2016-05-17
	 * @access	public
	 * @return	void
	 */
	public function viewuser(){
		//获取参数
		$sid = $this->input('sid','string');
		$page = $this->input('page','int',1);
		$start1 = $this->input('start','string','');
		$end1 = $this->input('end','string','');
		$start = strtotime($start1);
		$end = strtotime($end1);
		$size = 6;
		list($basic,$footprint) = $this->load('visitlog')->getUserAll($sid,$page,$size,$start,$end);
		//处理数据
		$total 	= empty($footprint['total']) ? 0 : $footprint['total'];
		$list 	= empty($footprint['rows']) ? array() : $footprint['rows'];
		$total = ceil($total/$size);
		//渲染页面
		$this->set('start',$start1);
		$this->set('end',$end1);
		$this->set('total', $total);
		$this->set('page', $page);
		$this->set("basic",$basic);
		$this->set('list', $footprint['rows']);
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
            
            $arr = $this->com('redisHtml')->get('frequency_list');
            if(empty($arr) || !empty($dateStart) || !empty($dateEnd)){
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
                $this->com('redisHtml')->set('frequency_list', $arr, 300);
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