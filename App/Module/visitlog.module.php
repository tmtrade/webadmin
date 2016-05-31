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
    );
	private $configData;
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
    
    //得到数据配置的菜单信息
    public function menu(){
        $config = $this->getConfigData();//得到所有的配置信息
        $array = array(1,2,3,4,5,6,10,11,100);
        $m = array();
        foreach ($config as $k=>$v){
            if(in_array($k, $array)){
                $m[$k] = $v;
                if(!empty($v['like'])){
                    $m[$k]['view'] = "";
                    $m[$k]['view'][1] = array(
                                    'title' => $v['like_name'],
                                    'web_id' => $v['like'],
                                    'in' => 1,
                                    );
                    $m[$k]['view'][2] = array(
                                    'title' => '广告模块',
                                    'web_id' => $v['ad'],
                                    );
                }
            }
        }
        return $m;
    }

    //计算每个页面点击数
    public function page_count($type,$dateStart="",$dateEnd="",$class="")
    {
        $r['raw'] = "1";
        if($type==100){
            $r['scope'] = array('web_id' => array(100, 110));
        }else{
            $r['eq']['type'] = $type;
            if(!empty($class)){
                $r['in'] = array('web_id' => $class);
            }else{
                if($type==4 || $type==9 || $type==11){
                    $r['scope'] = array('web_id' => array(1, 32));
                }else{
                    $r['scope'] = array('web_id' => array(1, 29));
                }
            }
        }
        if(!empty($dateStart)){
            $r['raw'] .= " and date>=".$dateStart;
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and date<".$dateEnd;
        }
        return $this->import('page')->count($r);
    }
    
    //计算每个页面访问者
    public function pageUser_count($type,$dateStart="",$dateEnd="",$class)
    {
        $r['raw'] = "1";
        if($type==100){
            $r['scope'] = array('web_id' => array(100, 110));
        }else{
            $r['eq']['type'] = $type;
            if(!empty($class)){
                $r['in'] = array('web_id' => $class);
            }else{
                if($type==4 || $type==9 || $type==11){
                    $r['scope'] = array('web_id' => array(1, 32));
                }else{
                    $r['scope'] = array('web_id' => array(1, 29));
                }
            }
        }
        if(!empty($dateStart)){
            $r['raw'] .= " and date>=".$dateStart;
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and date<".$dateEnd;
        }
        $r['group'] = array('sid' => 'asc');
        return $this->import('page')->count($r);
    }
    
    //计算每个链接访问次数
    public function pageUrl_count($web_id,$type,$dateStart="",$dateEnd="",$in="")
    {
        $r['raw'] = "1";
        if($type==100){
            $r['scope'] = array('web_id' => array(100, 110));
            $r['eq']['web_id'] = $web_id;
        }else{
            $r['eq']['type'] = $type;
            if(!empty($in)){
                $r['in'] = array('web_id' => $web_id);
            }else{
                $r['eq']['web_id'] = $web_id;  
            }
        }
        if(!empty($dateStart)){
            $r['raw'] .= " and date>=".$dateStart;
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and date<".$dateEnd;
        }
        return $this->import('page')->count($r);
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
			//得到用户画像
			$data[$k]['huaxiang'] = implode(',',$this->getHuaxiang($sid));
		}
		return $data;
	}

	/**
	 * 得到指定cookie的最近一次访问信息
	 * @param $logids
	 * @param $type int 默认返回原生结果
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
		//得到用户画像信息
		$huaxiang = $this->getHuaxiang($sid);
		//得到浏览足迹信息
		$footprint = $this->getFootprint($sid,$page,$size,$start,$end);
		return array($basic,$huaxiang,$footprint);
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
		}else{
			$data['device'] = 0;
		}
		return $data;
	}

	/**
	 * 得到用户画像信息
	 * @param $sid
	 * @return array
	 */
	private function getHuaxiang($sid){
		$data = array();
		//是否提交过商标销售信息
		$r['eq'] = array('sid'=>$sid,'type'=>13);
		$r['col'] = array('sid');
		$rst = $this->import('page')->find($r);
		if($rst){
			$data[] = '卖商标';
		}
		//是否提交过商标购买信息
		$r = array();
		$r['eq'] = array('sid'=>$sid);
		$r['in']['web_id'] = array('30','31');
		$r['col'] = array('sid');
		$rst = $this->import('page')->find($r);
		if($rst){
			$data[] = '买商标';
		}
		//其他信息
		$rst = $this->import('sessions')->find(array('eq'=>array('sid'=>$sid),'col'=>array('visits','issem')));
		if($rst){
			//是否老用户
			if($rst['visits']>5){
				$data[] = '老用户';
			}
			//是否推广来源
			if($rst['issem']){
				$data[] = '推广来源';
			}
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
			$start += 28800;//加8小时 时区问题
			$end += 115200;//加32小时 定位到当天24点
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
		$i = 0;
		$len = count($data);
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
			$tmp['s_url'] = $item['s_url'];//来源地址
			//分访问次数保存
			$temp[$i]['data'][] = $tmp;
			if($item['isnew']){ //新访问，次数加一
				$temp[$i]['riqi'] = $riqi;
				$temp[$i]['device'] = $item['device'];
				$temp[$i]['location'] = $this->getLocByIp($item['ip']);
				$temp[$i]['ip'] = $item['ip'];
				++$i;
			}
			if($k==$len-1 && $item['isnew']!=1){ //最后一个不是新访问时添加相关信息
				$temp[$i]['riqi'] = $riqi;
				$temp[$i]['device'] = $item['device'];
				$temp[$i]['location'] = $this->getLocByIp($item['ip']);
				$temp[$i]['ip'] = $item['ip'];
			}
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
		if(is_object($arr)){
			$str = $arr->province.$arr->city;
			if($str){
				return $str;
			}
		}
		return '未知';
	}

	/**
	 * 得到页面信息
	 * @param $oid string 操作的id
	 * @param $type int 页面类型
	 * @return string
	 */
	private function analyseUrl($type,$oid){
		if($type==0){
			return array('page'=>'未知','opt'=>'未知');
		}
		$oid = explode(',',ltrim($oid));
		$config = $this->getConfigData();//得到所有的配置信息
		$tmp = array();
		$tmp['page'] = $config[$type]['title'];
		if($oid){
			$tmp['opt'] = '';
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
								if($type==13){ //解码我要卖提交的数据
									$v0['addition'] = urldecode($v0['addition']);
								}
								$temp .= ' | 附加信息: '.$v0['addition'];
							}
							$tmp['opt'][] = $temp;
						}
					}
				}
				if($tmp['opt']){
					$tmp['opt'] = implode('<br/>',$tmp['opt']);
					return $tmp;
				}
			}
		}
		$tmp['opt'] = '无操作信息';
		return $tmp;
	}
        
}
?>