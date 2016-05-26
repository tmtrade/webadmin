<?
/**
* 数据统计
*
* @package	Module
* @author	dower
* @since	2016-05-25
*/
class CountModule extends AppModule
{
    public $models = array(
        'page'     => 'stpage',
        'visitlog'     => 'tvisitlog',
        'sessions'     => 'tsessions',
    );

    /**
     * 处理第一次来源信息
     * @param $params
     * @param $flag boolean 是否新用户
     * @return string
     */
    public function handleFirst($params,$flag){
        $time = time();
        //得到sid和上次访问时间
        $temp = explode('-',$params['sid']);
        $sid = $temp[0];
        $start = isset($temp[1])?$temp[1]:0;
        if(!$flag || ($time-$start)>3600){ //访问间隔大于1个小时认为第二次访问
            $isnew = 1;
        }else{
            $isnew = 0;
        }
        if($flag){
            //添加到访问信息表中
            $host = parse_url($params['referrer'],PHP_URL_HOST);//解析来源的host
            if(!$host){
                $host = '';
            }
            $data  = array(
                'sid' => $sid,
                's_url' => isset($params['referrer'])?$params['referrer']:'',
                's_ip' => $params['ip'],
                's_dateline' => $time,
                'host' => $host,
                'intourl' => $params['url'],
                'lasturl' => $params['url'],
                'lastip' => $params['ip'],
                'lastdateline' => $time,
                'visits' => 1,
                'issem' => isset($params['issem'])?$params['issem']:0,
            );
            $this->import('sessions')->create($data);
        }else{
            //更新访问信息表
            $data  = array(
                'lasturl' => $params['url'],
                'lastip' => $params['ip'],
                'lastdateline' => $time,
            );
            if($isnew){ //二次访问更新次数
                $data['visits'] = array('visits',1);
            }
            $r['eq']['sid'] = $sid;
            $this->import('sessions')->modify($data,$r);
        }
        //添加到访问日志表
        $data = array();
        $data  = array(
            'sid' => $sid,
            's_url' => isset($params['referrer'])?$params['referrer']:'',
            'url' => $params['url'],
            'host' => $params['host'],
            'device' => isset($params['device'])?$params['device']:0,
            'dateline' => $time,
            'ip' => $params['ip'],
            'tel' => isset($params['tel'])?$params['tel']:'',
            'isnew' => $isnew,
            'issem' => isset($params['issem'])?$params['issem']:0,
        );
        $id = $this->import('visitlog')->create($data);
        //返回新的cookie值 和 日志的id
        return array(implode('-',array($sid,$time)),$id);
    }

    /**
     * 处理访问离开信息
     * @param $params
     */
    public function handleLast($params){
        $temp = explode('-',$params['sid']);
        $sid = $temp[0];
        $data  = array(
            'sid' => $sid,
            'w' => isset($params['w'])?$params['w']:0,
            'h' => isset($params['h'])?$params['h']:0,
            'x' => isset($params['x'])?$params['x']:0,
            'y' => isset($params['y'])?$params['y']:0,
            'type' => isset($params['type'])?$params['type']:0,
            'web_id' => isset($params['web_id'])?$params['web_id']:'',
            'addition' => isset($params['addition'])?$params['addition']:'',
            'date' => time(),
        );
        $visitid = isset($params['visitid'])?$params['visitid']:0;
        $id = $this->import('page')->create($data);
        //保存操作id到日志表oid中
        if($id && $visitid !=0){
            //得到日志表的信息
            $r['eq']['id'] = $visitid;
            $r['col'] = array('oid');
            $rst = $this->import('visitlog')->find($r);
            if($rst && $rst['oid']){
                $data = array('oid'=>$rst['oid'].','.$id);
            }else{
                $data = array('oid'=>$id);
            }
            //保存操作到浏览日志表中
            $this->import('visitlog')->modify($data,array('eq'=>array('id'=>$visitid)));
        }
    }
}
?>