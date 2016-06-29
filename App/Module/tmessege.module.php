<?
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/8 0008
 * Time: 上午 10:03
 */
class TmessegeModule extends AppModule
{
    public $models = array(
        'messege_monitor'     => 'MessegeMonitor',
    );

    /**
     * 得到站内信数据
     * @return array
     */
    public function getList(){
        //得到当前的站内信模板
        $r = array();
        $r['limit'] = 1000;
        return $this->import('messege_monitor')->find($r);
    }

    /**
     * 创建新的站内信配置
     * @param $params
     * @return int
     */
    public function add($params){
        $r = array();
        $url = $params['url'];
        $tmp = explode('---',$url);
        $params['url'] = strtolower($tmp[0]);//转化为小写
        $params['desc'] = $tmp[1];
        $r['eq']['url'] = $params['url'];
        $rst = $this->import('messege_monitor')->find($r);
        if($rst){
            return -1;
        }
        $params['date'] = time();
        $rst = $this->import('messege_monitor')->create($params);
        if($rst){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 修改站内信配置
     * @param $params
     * @return int
     */
    public function edit($params){
        $r = array();
        $url = $params['url'];
        $tmp = explode('---',$url);
        $params['url'] = $tmp[0];//解析出url
        $params['desc'] = $tmp[1];//解析出描述
        $r['eq']['url'] = $params['url'];
        $r['raw'] = 'id<>'.$params['id'];
        $rst = $this->import('messege_monitor')->find($r);
        if($rst){
            return -1;
        }
        $params['date'] = time();
        $rst = $this->import('messege_monitor')->modify($params,array('eq'=>array('id'=>$params['id'])));
        if($rst){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 删除对应的配置文件
     * @param $id
     * @return bool
     */
    public function drop($id){
        return $this->import('messege_monitor')->remove(array('eq'=>array('id'=>$id)));
    }

    /**
     * 得到某个模板详情
     * @param $id
     * @return array
     */
    public function getDetail($id){
        //得到对应的模板信息
        $r = array();
        $r['eq']['id'] = $id;
        return $this->import('messege_monitor')->find($r);
    }

    /**
     * 得到配置文件信息
     * @return mixed
     */
    public function getConfig(){
        return require ConfigDir.'/messege.config.php';
    }
}
?>