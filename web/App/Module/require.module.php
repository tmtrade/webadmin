<?
/**
 * 需求管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/22 0022
 * Time: 上午 10:31
 */
class RequireModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'require' => 'require',
        'requirebid' => 'requirebid',
        'buy' => 'buybrand',
        'memberOptions' => 'memberOptions',
    );

    /**
     * 得到原始信息列表
     * @param $param array 查询参数
     * @param $limit int 分页大小
     * @return array
     */
    public function rawList($param,$limit){
        $r = array();
        $r['neq'] = array('mobile'=>'0');//有电话号码的需求
        $r['page']  = $param['page'];
        $r['limit'] = $limit;
        $r['col'] = array('id','mobile','name','remarks','recorddate','isuse');
        $r['order'] = array('recorddate'=>'desc');
        $res = $this->import('buy')->findAll($r);
        //得到当前的最后访问时间
        $last = $this->lastTime('get',3);
        $last = intval($last);
        foreach($res['rows'] as &$item){
            $item['isNew'] = false;
            if($item['recorddate']>$last){
                $item['isNew'] = true;
            }
        }
        unset($item);
        //设置最后访问时间
        $this->lastTime('post',3);
        return $res;
    }

    /**
     * 得到需求信息列表
     * @param $param array 查询参数
     * @param $limit int 分页大小
     * @return array
     */
    public function requireList($param,$limit){
        $r = array();
        $r['neq'] = array('mobile'=>'0');//有电话号码的需求
        $r['page']  = $param['page'];
        $r['limit'] = $limit;
        $r['col'] = array('id','name','mobile','desc','status','date');
        $r['order'] = array('date'=>'desc','status'=>'asc');
        $res = $this->import('require')->findAll($r);
        if($res['total']){
            //得到竞标数量
            foreach($res['rows'] as &$item){
                //得到其他的竞标信息
                $temp = $this->getTips($item['id']);
                $item['hasNew'] = $temp['isNew'];
                $item['count'] = $temp['count'];
            }
            unset($item);
        }
        //得到是否有新原始需求
        $last = intval($this->lastTime('get',3));
        $r = array(
            'raw'=>" recorddate>{$last}"
        );
        $rst = $this->import('buy')->count($r);
        if($rst){
            $res['isNew'] = true;
        }else{
            $res['isNew'] = false;
        }
        return $res;
    }

    /**
     * 得到原始需求信息
     * @param $id
     * @return array
     */
    public function getBasicRequire($id){
        $r = array();
        $r['eq'] = array('id'=>$id);
        $r['col'] = array('mobile','name','remarks');
        $res = $this->import('buy')->find($r);
        //标记为提取过的信息
        $s = array('eq'=>array('id'=>$id));
        $d = array('isuse'=>1);
        $this->import('buy')->modify($d,$r);
        return $res;
    }

    /**
     * 得到需求详情
     * @param $id
     * @return array
     */
    public function getRequire($id){
        $r = array(
            'eq'=>array('id'=>$id)
        );
        return $this->import('require')->find($r);
    }

    /**
     * 详情页,竞标的提示数据
     * @param $id int 需求id
     * @return array
     */
    public function getTips($id){
        //得到总数
        $r = array(
            'eq'=>array('require_id'=>$id)
        );
        $count = $this->import('requirebid')->count($r);
        //是否new
        $last = intval($this->lastTime('get',2,$id));
        $isNew = false;
        if($count){
            $r = array(
                'eq'=>array('require_id'=>$id),
                'raw'=>" `date`>'$last'",
            );
            $res = $this->import('requirebid')->count($r);
            if($res) $isNew = true;
        }
        //返回结果
        return array('count'=>$count,'isNew'=>$isNew);
    }

    /**
     * 得到竞标信息
     * @param $id
     * @param $limit
     * @param $page
     * @return array
     */
    public function getBids($id,$limit,$page){
        //得到竞价信息
        $r = array(
            'eq'=>array('require_id'=>$id),
            'limit'=>$limit,
            'page'=>$page,
            'order'=>array('status'=>'asc','date'=>'desc'),
        );
        $res = $this->import('requirebid')->findAll($r);
        //得到上次查看时间
        $last = intval($this->lastTime('get',2,$id));
        //更新查看时间
        $this->lastTime('post',2,$id);
        //得到其他信息
        if(!$res['total']) return $res;
        $temp = array();
        foreach($res['rows'] as $item){
            //设置更新状态
            if($item['date']>$last){
                $item['isNew'] = true;
            }else{
                $item['isNew'] = false;
            }
            //得到商标信息
            $tminfo = $this->load('trademark')->getInfo($item['number'],array('trademark','class','auto'));
            $tminfo['imgUrl'] = $this->load('trademark')->getImg($item['number']);
            $class = explode(',',$tminfo['class']);
            $tminfo['yzc'] = SITE_URL.'d-'.$tminfo['auto'].'-'.$class[0].'.html';
            $item['tm'] = $tminfo;
            //保存到结果中
            $temp[] = $item;
        }
        $res['rows'] = $temp;
        return $res;
    }

    /**
     * 删除需求信息
     * @param $ids
     * @return bool
     */
    public function delete($ids){
        $r = array('in'=>array('id'=>$ids));
        return $this->import('require')->remove($r);
    }

    /**
     * 添加需求信息
     * @param $data
     * @return mixed
     */
    public function add($data){
        $data['date'] = time();
        return $this->import('require')->create($data);
    }

    /**
     * 修改需求信息
     * @param $data
     * @param $id
     * @return bool
     */
    public function edit($data,$id){
        $data['date'] = strtotime($data['date']);
        $r = array('eq'=>array('id'=>$id));
        return $this->import('require')->modify($data,$r);
    }

    /**
     * 修改竞标的状态
     * @param $id
     * @param $status
     * @return bool
     */
    public function changeBid($id,$status){
        $r = array('eq'=>array('id'=>$id));
        $d = array('status'=>$status);
        return $this->import('requirebid')->modify($d,$r);
    }

    /**
     * 得到或设置用户的上次访问时间
     * @param string $method 默认get-获得,post-设置
     * @param int $type 默认1-需求,2-需求竞标,3-原始需求信息
     * @param int $id 0 需求竞标的需求id
     * @return string|bool
     */
    private function lastTime($method='get',$type=1,$id=0){
        //得到用户id
        $userinfo = Session::get(COOKIE_USER);
        if(empty($userinfo)){
            return false;
        }
        $userinfo = unserialize($userinfo);
        $user_id = $userinfo['userId'];
        //判断操作类型
        $option_name = $type==1?'last_require':($type==2?'last_require_bid_'.$id:'last_require_raw');
        //判断method区别处理
        $method = strtolower($method);
        if($method=='post'){//设置
            //查看是否存在设置
            $r = array(
                'eq'=>array('memberId'=>$user_id,'name'=>$option_name),
                'col'=>array('id'),
            );
            $res = $this->import('memberOptions')->find($r);
            if($res){//修改数据
                $id = $res['id'];

                $r = array('eq'=>array('id'=>$id));
                $d = array('value'=>time());
                return $this->import('memberOptions')->modify($d,$r);
            }else{//添加数据
                $d = array(
                    'memberId'=>$user_id,
                    'name'=>$option_name,
                    'value'=>time(),
                );
                return $this->import('memberOptions')->create($d);
            }
        }else{//获取
            $r = array(
                'eq'=>array('memberId'=>$user_id,'name'=>$option_name),
                'col'=>array('value'),
            );
            $res = $this->import('memberOptions')->find($r);
            if($res){
                return $res['value'];
            }else{
                return false;
            }
        }
    }

    /**
     * 得到竞标信息的number信息
     * @param $id
     * @return string
     */
    public function getNumbers($id){
        $r = array(
            'eq'=>array('require_id'=>$id,'status'=>1),
            'limit'=>10000,
            'col'=>array('number')
        );
        $res = $this->import('requirebid')->find($r);
        if($res){
            $numbers = arrayColumn($res,'number');
            $numbers = array_filter(array_unique($numbers));
            return implode(' ',$numbers);
        }else{
            return '';
        }
    }
}
?>