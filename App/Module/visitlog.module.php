<?

/**
 * 数据跟踪
 *
 * 访问者数据，模块跟踪，模块走势图
 *
 * @package    Module
 * @author     Far
 * @since      2016年5月17日11:00:07
 */
class VisitlogModule extends AppModule
{
	public $models = array(
		'visitlog'              => 'visitlog',
		'sessions'              => 'visitlogSessions',
		'sem'                   => 'visitlogSem',
	);
    
    //计算每个页面点击数
    public function page_count($url,$dateStart="",$dateEnd="",$like="")
    {
        if(!empty($like)){
            $r['like']['s_url'] = $url;
        }else{
            $r['eq']['s_url'] = $url;
        }
        
        $r['eq']['host'] = "www.yizhchan.com";
        if(!empty($dateStart)){
            $r['raw'] = " dateline>".strtotime($dateStart);
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and dateline<".strtotime($dateEnd);
        }
        return $this->import('visitlog')->count($r);
    }
    
    //计算每个页面访问者
    public function pageUser_count($url,$dateStart="",$dateEnd="",$like="")
    {
        if(!empty($like)){
            $r['like']['s_url'] = $url;
        }else{
            $r['eq']['s_url'] = $url;
        }
        $r['eq']['host'] = "www.yizhchan.com";
        if(!empty($dateStart)){
            $r['raw'] = " dateline>".strtotime($dateStart);
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and dateline<".strtotime($dateEnd);
        }
        $r['group'] = array('sid' => 'asc');
        return $this->import('visitlog')->count($r);
    }
    
    //计算每个链接访问次数
    public function pageUrl_count($url,$s_url,$dateStart="",$dateEnd="",$slike="",$like="")
    {
        
        if(!empty($slike)){
            $r['like']['s_url'] = $s_url;
        }else{
            $r['eq']['s_url'] = $s_url;
        }
        
        if(!empty($like)){
            $r['like']['url'] = $url;
        }else{
            $r['eq']['url'] = $url;
        }
        
        $r['eq']['host'] = "www.yizhchan.com";
        if(!empty($dateStart)){
            $r['raw'] = " dateline>=".strtotime($dateStart);
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and dateline<".strtotime($dateEnd);
        }
        return $this->import('visitlog')->count($r);
    }
    
	/**
	 * 得到访客数据
	 * @param $page
	 * @param int $limit
	 * @return array
	 */
	public function getUserList($page, $limit=10){
		$r = array();
		$r['page']  = $page;
		$r['limit'] = $limit;
		$r['order'] = array('lastdateline'=>'desc');
		$data = $this->import('sessions')->findAll($r);
		//处理列表数据
		$data['rows'] = $this->handList($data['rows']);
		return $data;
	}

	/**
	 * 处理访问列表数据
	 * @param $data
	 * @return array
	 */
	private function handList($data){
		foreach($data as $k=>$item){
			$sid = $item['sid'];
			//设备数据
			if($item['issem']){
				$data[$k]['device'] = $this->getDevice($sid);//去sem表记录查询
			}else{
				$data[$k]['device'] = 0;
			}
			//停留时间
			$data[$k]['stay'] = '未实现';
		}
		return $data;
	}

	/**
	 * 根据唯一用户cookie获得最近的访问设备
	 * @param $sid
	 * @return int
	 */
	private function getDevice($sid){
		$r['limit'] = 1;
		$r['eq']['sid'] = $sid;
		$r['order'] = array('auto'=>'desc');
		$r['col'] = array('device');
		$res = $this->import('sem')->find($r);
		if($res){
			return $res['device'];
		}
		return 0;
	}

	/**
	 * 得到访问者的所有信息
	 * @param $sid
	 * @param $page
	 * @param int $size
	 * @param bool $start
	 * @param bool $end
	 * @return array
	 */
	public function getUserAll($sid,$page, $size = 10,$start = false,$end = false){
		//得到基本信息
		$basic = $this->getBasicInfo($sid);
		//得到浏览足迹信息
		$footprint = $this->getFootprint($sid,$page,$size,$start,$end);
		return array($basic,$footprint);
	}

	/**
	 * 得到浏览者的基本信息
	 * @param $sid
	 * @return array
	 */
	private function getBasicInfo($sid){
		$r = array();
		$r['eq']['sid']  = $sid;
		$r['limit'] = 1;
		$r['col'] = array('issem','visits');
		$data = $this->import('sessions')->find($r);
		if($data && $data['issem']==1){
			$data['device'] = $this->getDevice($sid);
		}else{
			$data['device'] = 0;
		}
		return $data;
	}

	/**
	 * 得到用户足迹的分页数据
	 * @param $sid
	 * @param $page
	 * @param $limit
	 * @param $start
	 * @param $end
	 * @return array
	 */
	private function getFootprint($sid,$page,$limit,$start,$end){
		//得到所有的浏览信息
		$r = array();
		$r['eq'] = array('sid'=>$sid);
		$r['page']  = $page;
		$r['limit'] = $limit;
		//单方向筛选
		if($start && !$end){
			$end = time();
		}
		if($end && !$start){
			$start = 1000;
		}
		if($start && $end){
			//保证end大于start
			if($start > $end){
				$start = $start ^ $end;
				$end = $start ^ $end;
				$start = $start ^ $end;
			}
			$r['scope'] = array('dateline'=>array($start,$end));
		}
		$r['order'] = array('dateline'=>'desc');
		$data = $this->import('visitlog')->findAll($r);
		//对数据进行处理
		$data['rows'] = $this->handFootprint($data['rows']);
		return $data;
	}

	/**
	 * 处理浏览足迹数据
	 * @param $data
	 * @return mixed
	 */
	private function handFootprint($data){
		$temp = array();
		foreach($data as $k=>$item){
			$riqi = date('Y-m-d',$item['dateline']);
			//处理停留时间
			$tmp = array();
			$tmp['time'] = $item['dateline'];//进入时间
			//停留时间
			if(isset($data[($k-1)])){
				$tmp['stay'] = $data[($k-1)]['dateline']-$item['dateline'];//上一个减当前
			}else{
				$tmp['stay'] = 0;
			}
			$tmp['type'] = $this->analyseUrl($item['url']);//页面类型
			$tmp['opr'] = '未实现';//操作类型
			//分日期保存(精确到天)
			$temp[$riqi]['data'][] = $tmp;
			$temp[$riqi]['location'] = $this->getLocByIp($item['ip']);//地址>>TODO 此处有问题(ip对应一个记录)
			$temp[$riqi]['ip'] = $item['ip'];//ip
		}
		return $temp;
	}

	/**
	 * 根据ip得到地址
	 * @param $ip
	 * @return string
	 */
	private function getLocByIp($ip){
		$str = file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip);//新浪接口
		$arr = json_decode($str);
		$str = $arr->province.$arr->city;
		if($str){
			return $str;
		}else{
			return '未知';
		}
	}

	/**
	 * 根据url得到访问的页面
	 * @param $url
	 * @return string
	 */
	private function analyseUrl($url){
//		$site_url = SITE_URL;
		$site_url = 'http://www.yizhchan.com/';//todo
		//匹配页面
		if($url==$site_url || $url==substr($url,0,-1)){
			return '网站首页';
		}elseif(strpos($url,$site_url.'s')!==false || strpos($url,$site_url.'offprice')!==false){
			return '商标列表页/特价商标列表页';
		}elseif(strpos($url,$site_url.'d-')!==false){
			return '商标详情页';
		}elseif(strpos($url,$site_url.'n-45-')!==false){
			return 'faq详情页';
		}elseif(strpos($url,$site_url.'g')!==false){
			return '商品聚合页';
		}elseif(strpos($url,$site_url.'n-45')!==false){
			return 'faq列表面';
		}elseif(strpos($url,$site_url.'n-50-')!==false){
			return '新闻详情页';
		}elseif(strpos($url,$site_url.'n-50')!==false){
			return '新闻列表面';
		}elseif(strpos($url,$site_url.'zhuanti/view')!==false || strpos($url,$site_url.'topic/')!==false){
			return '专题详情面';
		}elseif(strpos($url,$site_url.'zhuanti')!==false){
			return '专题列表面';
		}elseif(strpos($url,$site_url.'buy')!==false){
			return '我要买';
		}elseif(strpos($url,$site_url.'sell')!==false){
			return '我要卖';
		}else{
			return '未知';
		}
	}
}
?>