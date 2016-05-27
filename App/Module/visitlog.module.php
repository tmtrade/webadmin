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
		'page'     => 'stpage',
		'visitlog'     => 'tvisitlog',
		'sessions'     => 'tsessions',
                'module'                => 'Module',
                'moduleClass'           => 'ModuleClass',
                'moduleLink'            => 'ModuleLink',
                'modulePic'             => 'ModulePic',
                'moduleClassItems'      => 'ModuleClassItems',
                'sale'                  => 'Sale',
	);

	/**
	 * 得到配置文件的数组信息
	 * @return mixed
	 */
	private function getConfigData(){
		if($this->configData){
			return $this->configData;
		}else{
			return require ConfigDir.'/visitlog.config.php';
		}
	}

    //计算每个页面点击数
    public function page_count($url,$dateStart="",$dateEnd="",$like="")
    {
        $r['eq']['host'] = "www.yizhchan.com";
        if(!empty($like)){
            $r['lLike']['s_url'] = $url;
        }else{
            $r['eq']['s_url'] = $url;
        }
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
        $r['eq']['host'] = "www.yizhchan.com";
        if(!empty($like)){
            $r['lLike']['s_url'] = $url;
        }else{
            $r['eq']['s_url'] = $url;
        }
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
        $r['eq']['host'] = "www.yizhchan.com";
        if(!empty($slike)){
            $r['lLike']['s_url'] = $s_url;
        }else{
            $r['eq']['s_url'] = $s_url;
        }
        
        if(!empty($like)){
            $r['lLike']['url'] = $url;
        }else{
            $r['eq']['url'] = $url;
        }
        if(!empty($dateStart)){
            $r['raw'] = " dateline>=".$dateStart;
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and dateline<".$dateEnd;
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
			$rst = $this->getVisitLog($item['logids'],1);
			if($rst){
				$data[$k]['device'] = $rst['device'];
				date_default_timezone_set ( 'GMT' );
				$data[$k]['stay'] = date('H.i.s',$rst['stay']);
				date_default_timezone_set ( 'PRC' );
			}else{
				$data[$k]['device'] = 0;
				$data[$k]['stay'] = 0;
			}
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
		$r['order'] = array('id'=>'desc');
		$r['col'] = array('device');
		$res = $this->import('visitlog')->find($r);
		if($res){
			return $res['device'];
		}
		return 0;
	}

	/**
	 * 得到指定cookie的最近一次访问信息
	 * @param $logids
	 * @return bool
	 */
	public function getVisitLog($logids,$type=0){
		$ids = explode(',',ltrim($logids,','));
		if($ids){
			//得到所有记录信息
			$r['in']['id'] = $ids;
			$r['limit'] = 1000;
			$r['col'] = array('device','tel','dateline','leavetime');
			$rst = $this->import('visitlog')->find($r);
			if($rst){
				if(!$type){
					return $rst;//返回原生结果
				}
				$len = count($rst);
				//得到电话号码信息
				$tel = array_filter(array_unique(arrayColumn($rst,'tel')));
				$data['tel'] = $tel[0];
				//得到设备信息
				$data['device'] = $rst[0]['device'];
				//得到停留时间信息
				if($rst[$len-1]['leavetime']){
					$end = $rst[$len-1]['leavetime'];
				}else{
					$end = $rst[$len-1]['dateline'];
				}
				$data['stay'] = $end-$rst[0]['dateline'];
				return $data;
			}
		}else{
			return false;
		}
	}

	/**
	 * 得到访客的所有信息
	 * @param $sid
	 * @return array
	 */
	private function getAllLog($sid){
		//得到所有记录信息
		$r['eq']['sid'] = $sid;
		$r['limit'] = 1000;
		$r['col'] = array('device','tel');
		return $this->import('visitlog')->find($r);
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
		//得到session信息
		$r = array();
		$r['eq']['sid']  = $sid;
		$r['limit'] = 1;
		$r['col'] = array('issem','visits','tel','logids');
		$data = $this->import('sessions')->find($r);
		//得到其他信息
		if($data){
			$rst = $this->getAllLog($sid);//处理手机
			if($rst){
				$tel = arrayColumn($rst,'device');
				$data['device'] = array(0,0);
				foreach($tel as $v){
					if($v){
						$data['device'][0] += 1;//手机
					}else{
						$data['device'][1] += 1;//电脑
					}
				}
			}else{
				$data['device'] = 0;
			}
			$data['utype'] = $data['tel']?1:0;//用户类型
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
			if($item['leavetime']){
				date_default_timezone_set ( 'GMT' );
				$tmp['stay'] = date('i:s',$item['leavetime']-$item['dateline']);
				date_default_timezone_set ( 'PRC' );
			}else{
				$tmp['stay'] = 0;
			}
			//处理页面信息
			$rst = $this->analyseUrl($item['type'],$item['oid']);
			$tmp['type'] = $rst['page'];//页面类型
			$tmp['opr'] = $rst['opt'];//操作类型
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
	 * 得到页面信息
	 * @param $oid string 操作的id
	 * @param $type int 页面类型
	 * @return string
	 */
	private function analyseUrl($type,$oid){
		$oid = explode(',',ltrim($oid));
		$config = $this->getConfigData();//得到所有的配置信息
		$tmp = array();
		$tmp['page'] = $config[$type]['title'];
		if($oid){
			//得到操作信息
			$r['in']['id'] = $oid;
			$r['limit'] = 1000;
			$r['col'] = array('web_id','addition');
			$rst = $this->import('page')->find($r);
			if($rst){
				foreach($config[$type]['view'] as $k=>$v){
					foreach($rst as $v0){
						if($v0['web_id']==$k){
							//组装操作信息
							$temp = '操作: '.$v['title'];
							if($v0['addition']){
								$temp .= ' | 附加信息: '.$v0['addition'];
							}
							$tmp['opt'][] = $temp;
						}
					}
				}
				$tmp['opt'] = implode('<br/>',$tmp['opt']);
				return $tmp;
			}
		}
		$tmp['opt'] = '无操作信息';
		return $tmp;
	}
        
    /**
     * 得到首页个楼层模块信息
     * @return array
     */
    public function getModule(){
        //得到所有的模块信息
        $r['order'] = array('sort'=>'asc');
        $r['eq'] = array('isUse'=>1);
        $r['col'] = array('id','name');
        $r['limit'] = 1000;
        $data = $this->com('redisHtml')->get('admin_index_Module');
        if ( empty($data) ){
            $modules = $this->import('module')->find($r);
            foreach($modules as $k=>$module){
                $class = $this->getModuleClass($module['id']);
                $pic = $this->getModuleAds($module['id']);
                $link = $this->getModuleLink($module['id']);
                $data[$k]['title'] = ($k+1)."F-".$module['name'];
                $data[$k]['url'] =  array_merge($class,$pic,$link);
            }
            $this->com('redisHtml')->set('admin_index_Module', $data, 3600);
        }
        return $data;
    }
    
    //获取新闻问答的链接
    public function getFaq(){
        $_news = $this->com('redisHtml')->get('_news_tmp');
        $arr1 = array();
        $arr2 = array();
        $arr3 = array();
        $arr4 = array();
        foreach ($_news['news'] as $k=>$v){
            $arr1[$k]['url'] = 'http://www.yizhchan.com'.$v['url'];
        }
        foreach ($_news['baike'] as $k=>$v){
            $arr2[$k]['url'] = 'http://www.yizhchan.com'.$v['url'];
        }
        foreach ($_news['faq'] as $k=>$v){
            $arr3[$k]['url'] = 'http://www.yizhchan.com'.$v['url'];
        }
        foreach ($_news['law'] as $k=>$v){
            $arr4[$k]['url'] = 'http://www.yizhchan.com'.$v['url'];
        }
        return array_merge($arr1,$arr2,$arr3,$arr4);
    }

    /**
     * 首页模块子分类列表信息
     * @param $moduleId
     * @return array
     */
    private function getModuleClass($moduleId)
    {
        $r = array();
        $r['eq']['moduleId'] = $moduleId;
        $r['limit'] = 100;
        $r['col'] = array('id','name');
        $r['order'] = array('sort'=>'asc');
        $data = $this->import('moduleClass')->find($r);
        //得到分类的子分类列表
        $arr = array();
        $a = array();
        foreach($data as $k=>$item){
            $arr[$k] = $this->getModuleClassItems($item['id']);
            $a = array_merge($a,$arr[$k]);
        }
        return $a;
    }

    /**
     * 首页模块子分类列表信息
     * @param $classId
     * @return array
     * @throws array
     */
    private function getModuleClassItems($classId)
    {
        $r = array();
        $r['eq']['classId'] = $classId;
        $r['limit'] = 100;
        $r['order'] = array('sort'=>'asc');
        $r['col'] = array('id','name','number');
        $data = $this->import('moduleClassItems')->find($r);
        //添加额外信息
        $arr = array();
        foreach($data as $k=>$item){
            //判断商品是否销售中
            $rst = $this->isSale($item['number']);
            if(!$rst){
                unset($data[$k]);
                continue;
            }
            //得到所属分类名
            $result = $this->getSaleInfoByNumber($item['number']);
            $arr[$k]['title'] = $result['name'];
            $arr[$k]['url'] = 'http://www.yizhchan.com/d-'.$result['tid'].'-'.$result['class'].'.html';
        }
        return $arr;
    }

    /**
     * 首页模块包含广告列表信息
     * @param $moduleId
     * @return array
     */
    private function getModuleAds($moduleId)
    {
        $r = array();
        $r['eq']['moduleId'] = $moduleId;
        $r['limit'] = 100;
        $r['col'] = array('pic','link','alt');
        $r['order'] = array('sort'=>'asc');
        $data = $this->import('modulePic')->find($r);
        $arr = array();
        foreach ($data as $k=>$v){
            $arr[$k]['title']   = $v['alt'];
            $arr[$k]['url']     = "http://www.yizhchan.com".$v['link'];
        }
        return $arr;
    }

    /**
     * 首页模块推广链接列表信息
     * @param $moduleId
     * @return array
     */
    private function getModuleLink($moduleId)
    {
        $r = array();
        $r['eq']['moduleId'] = $moduleId;
        $r['limit'] = 100;
        $r['col'] = array('title','link','show');
        $r['order'] = array('sort'=>'asc');
        $data = $this->import('moduleLink')->find($r);
        $arr = array();
        foreach ($data as $k=>$v){
            $arr[$k]['title']   = $v['title'];
            $arr[$k]['url']     = "http://www.yizhchan.com".$v['link'];
        }
        return $arr;
    }

        
    /**
     * 根据商标得到sale信息(分类第一个)
     * @author dower
     * @param $number
     * @return mixed
     */
    public function getSaleInfoByNumber($number,$col = array('tid','class','name')){
        //得到商标的第一个分类
        $r['eq'] = array('number'=>$number);
        $r['col'] = $col;
        $rst = $this->import('sale')->find($r);
        //返回商标的分类名
        return $rst;
    }
    
        /**
     * 判断商标是否销售中
     * @author dower
     * @param $number
     * @return bool
     */
    public function isSale($number){
        $r['eq'] = array('number'=>$number);
        $r['col'] = array('status');
        $result = $this->import('sale')->find($r);
        if($result && $result['status']==1){
            return true;
        }
        return false;
    }
}
?>