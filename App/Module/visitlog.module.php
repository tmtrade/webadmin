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
            $r['raw'] = "and dateline>".strtotime($dateStart);
        }
        if(!empty($dateEnd)){
            $r['raw'] = "and dateline<".strtotime($dateEnd);
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
            $r['raw'] = "and dateline>".strtotime($dateStart);
        }
        if(!empty($dateEnd)){
            $r['raw'] = "and dateline<".strtotime($dateEnd);
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
            $r['raw'] = "and dateline>".strtotime($dateStart);
        }
        if(!empty($dateEnd)){
            $r['raw'] = "and dateline<".strtotime($dateEnd);
        }
        return $this->import('visitlog')->count($r);
    }

	
}
?>