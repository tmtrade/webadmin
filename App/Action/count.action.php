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
		$yzc = $this->input('yzc','int',0);
		if($yzc !=1 && $yzc !=2){
			return;
		}
		//获得参数
		$params = array();
		foreach($_GET as $k=>$v){
			if($k && $v){
				$params[$k] = $v;
			}
		}
		//获取ip
		$params['ip'] = getClientIp();
		//获取用户cookie
		$sid = isset($_GET['cookie'])?$_GET['cookie']:'';
		if($sid){
			$flag = false;
			$params['sid'] = $sid;
		}else{
			$flag = true;
			$params['sid'] = uniqid();
		}
		$res = array('code'=>0);
		if($yzc==1){ //打开页面的记录
			//处理数据
			$rst = $this->load('count')->handleFirst($params,$flag);
			if($rst){
				$res = array('code'=>1,'msg'=>$rst);
			}
		}else{ //关闭页面的信息
			$this->load('count')->handleLast($params);//保存用户的操作信息
		}
		//返回结果
		exit($_GET['yzctj'].'('.json_encode($res).')');
	}
}
?>