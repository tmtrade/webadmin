<?
/**
 * 数据统计
 *
 * @package	Action
 * @author	dower
 * @since	2016-02-17
 */
class CountAction extends Action
{
	/**
	 * 统计
	 */
	public function index(){
		//域名验证
		$url = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST);
		if(strpos(SITE_URL,$url) === false){
			return;
		}
		$yzc = $this->input('yzc','int',0);
		if(!in_array($yzc,array(1,2,3))){
			return;
		}
		//获得参数
		$params = array();
		foreach($_GET as $k=>$v){
			if($k && $v){
				$params[$k] = $v;
			}
		}
		//分类型处理
		$res = array('code'=>0);
		if($yzc==3){ //离开站点
			$this->load('count')->handleLast($params);//保存用户的操作信息-------
		}else{
			//获取ip
			$params['ip'] = get_client_ip();
			//获取用户cookie
			$sid = isset($_GET['cookie'])?$_GET['cookie']:'';
			if($sid){
				$flag = false;
				$params['sid'] = $sid;
			}else{
				$flag = true;
				$params['sid'] = uniqid();
			}
			if($yzc==1){ //打开页面的记录
				//处理数据
				$rst = $this->load('count')->handleFirst($params,$flag);
				if($rst){
					$res = array('code'=>1,'msg'=>$rst[0],'id'=>$rst[1]);
				}
			}else{ //页面操作的信息
				$this->load('count')->handleOpt($params);//保存用户的操作信息
			}
		}
		//返回结果
		exit($_GET['yzctj'].'('.json_encode($res).')');
	}
}
?>