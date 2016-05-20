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
		//来源页
		$referr = $this->getReferrUrl('viewuser');
		//渲染页面
		$this->set('start',$start1);
		$this->set('end',$end1);
		$this->set('referr',$referr);
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
            $count = 0;
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
                       foreach ($val['url'] as $u_val){
                           $count+=$this->load('visitlog')->pageUrl_count($u_val['url'],$v['url'], $dateStart, $dateEnd, $v['like'], $u_val['like']); 
                       }
                       $arr1[$key]['count'] = $count;
                       $arr1[$key]['zhanbi'] = round($count/$arr[$k]['page_count']*100,2);
                       $count = 0;
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
                $url_array      = require ConfigDir.'/visitlog.config.php';
                $pages          = $this->input('pages','int','');
                $page_module    = $this->input('page_module','int','');
                $dateStart 	= $this->input('dateStart', 'string');
                $dateEnd 	= $this->input('dateEnd', 'string');
                $dates          = $this->input('dates', 'int',1);
                if(empty($pages))  $pages = 1;
                $page_array = $url_array[$pages];
                $count = 0;
                $month = 0;
                $year  = 0;
                if(empty($dateStart) || empty($dateEnd)){
                    $dateStart = strtotime(date("Y-m-d"))-518400;
                    $dateEnd = strtotime(date("Y-m-d"));
                }else{
                    $dateStart = strtotime($dateStart);
                    $dateEnd = strtotime($dateEnd);
                    if(date("m",$dateStart)!=date("m",$dateEnd)){//不在同一个月
                        $month = 1;
                    }
                    if(date("Y",$dateStart)!=date("Y",$dateEnd)){//不在同一年
                        $year = 1;
                    }
                }
                if($dates==1){//选择日
                    $day = ($dateEnd-$dateStart)/86400;//取得选择时间段的天数
                    if(empty($page_module)){//当只选择页面时
                        for($i=0;$i<=$day;$i++){
                            $date_start = $dateStart+$i*86400;
                            $date_end = $date_start+86400;
                            $page_count[$i] = $this->load('visitlog')->page_count($page_array['url'], date("Y-m-d",$date_start), date("Y-m-d",$date_end), $page_array['like']); 
                            $date[$i] = date("m-d",$date_start);
                        }
                    }else{//当选择页面的模块时
                         $module_array = $page_array['view'][$page_module]['url'];
                         for($i=0;$i<=$day;$i++){
                            $date_start = $dateStart+$i*86400;
                            $date_end = $date_start+86400;
                            foreach ($module_array as $val){
                                $count+=$this->load('visitlog')->pageUrl_count($val['url'],$page_array['url'], date("Y-m-d",$date_start), date("Y-m-d",$date_end), $page_array['like'], $val['like']); 
                            }
                            $page_count[] = $count; 
                            $date[] = date("m-d",$date_start);
                            $count = 0;
                        }
                    }
                    
                }else if($dates==2){//选择月
                    $month_start = date("m",$dateStart);//开始月份
                    $month_end = date("m",$dateEnd);//结束月份
                    if(empty($page_module)){//当只选择页面时
                        for($i=$month_start;$i<=$month_end;$i++){
                            if($i==$month_start){
                                $date_start = date("Y-m-d",$dateStart);
                                $oneDay = date('Y-'.$i.'-01'); 
                                $date_end = strtotime("$oneDay +1 month");
                            }else if($i==$month_end){
                                $date_start = date('Y-'.$i.'-01');
                                $date_end = $dateEnd;
                            }else{
                                $date_start=date('Y-'.$i.'-01'); //获取当前月份第一天
                                $date_end = strtotime("$date_start +1 month");    //加一个月
                            }
                            $page_count[] = $this->load('visitlog')->page_count($page_array['url'], $date_start, date("Y-m-d",$date_end), $page_array['like']); 
                            $date[] = $i<10?"0".intval($i):$i;
                        }
                    }else{//当选择页面的模块时
                        $module_array = $page_array['view'][$page_module]['url'];
                        for($i=$month_start;$i<=$month_end;$i++){
                            if($i==$month_start){
                                $date_start = date("Y-m-d",$dateStart);
                                $oneDay = date('Y-'.$i.'-01'); 
                                $date_end = strtotime("$oneDay +1 month");
                            }else if($i==$month_end){
                                $date_start = date('Y-'.$i.'-01');
                                $date_end = $dateEnd;
                            }else{
                                $date_start=date('Y-'.$i.'-01'); //获取当前月份第一天
                                $date_end = strtotime("$date_start +1 month");    //加一个月
                            }
                            foreach ($module_array as $val){
                                $count+=$this->load('visitlog')->pageUrl_count($val['url'],$page_array['url'], date("Y-m-d",$date_start), date("Y-m-d",$date_end), $page_array['like'], $val['like']); 
                            }
                            $page_count[] = $count; 
                            $date[] = $i<10?"0".intval($i):$i;
                            $count = 0;
                        }
                    }
                }else{//选择年
                    
                }
                
                $this->set("url_array",$url_array);
                $this->set("url_json",json_encode($url_array));
                $this->set("data",array("page_count"=>implode(",", $page_count),"date"=>json_encode($date)));
                $this->set("s",array("dateStart"=>date("Y-m-d",$dateStart),"dateEnd"=>date("Y-m-d",$dateEnd),"pages"=>$pages,"page_module"=>$page_module,"dates"=>$dates,"month"=>$month,"year"=>$year));
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