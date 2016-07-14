<?php
/**
 * 站内信基础业务模型
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/7 0007
 * Time: 上午 11:53
 */
class MessegeModule extends AppModule
{
    private $limit = 10000;//查询的最大数据量

    /**
     * 引用业务模型
     */
    public $models = array(
        'messege' => 'Messege',
        'messege_user' => 'MessegeUser',
        'messege_monitor' => 'MessegeMonitor',
        'user' => 'User',
    );

    /**
     * 添加站内信
     * @param $params
     * @return bool|int
     */
    public function createMsg($params){
        if(!is_array($params)){
            return false;
        }
        if(!isset($params['date'])){ //创建时间
            $params['date'] = time();
        }
        if(!isset($params['sendtype'])){ //默认发送类型为1对1
            $params['sendtype'] = 1;
        }
        $ids = explode(',',$params['uids']);
        unset($params['uids']);
        $rst = $this->import('messege')->create($params);//添加到消息表
        if($rst){
            if($params['sendtype']!=3){
                //添加到消息用户关联表
                $flag = true;
                foreach($ids as $id){
                    $temp = array();
                    $temp['mid'] = $rst;
                    $temp['uid'] = $id;
                    $temp['date'] = time();
                    $res = $this->import('messege_user')->create($temp);//添加到关联表中
                    if(!$res){
                        $flag = false;
                        break;
                    }
                }
                if($flag){
                    return $rst;
                }else{
                    return 0;
                }
            }
        }
        return $rst;

    }

    /**
     * 用户将对全员发送的消息取回(登录时操作,排除非活跃用户造成的数据及性能影响)
     * @param $uid int 用户id
     * @return bool
     */
    public function createSelfMsg($uid=0){
        //得到该用户的上次更新时间
        $r = array();
        if(!$uid){
            $uid = UID;
        }
        $r['eq']['id'] = $uid;
        $r['col'] = array('mupdate');
        $res = $this->import('user')->find($r);
        if(!$res){
            return false;
        }
        //得到所有的对全员的群发消息
        $r = array();
        $r['col'] = array('id');
        $r['raw'] = '`date`> '.$res['mupdate'].' and `sendtype`=3';//时间需大于改用户上次的更新时间
        $r['limit'] = $this->limit;
        $rst1 = $this->import('messege')->find($r);
        $flag = true;
        if($rst1){
            //将公共消息保存到对应的用户消息关联表中
            foreach($rst1 as $item){
                $temp = array();
                $temp['mid'] = $item['id'];
                $temp['uid'] = $uid;
                $temp['date'] = time();
                $res = $this->import('messege_user')->create($temp);//添加到关联表中
                if(!$res){ //保存失败,终止操作
                    $flag = false;
                    break;
                }
            }
        }
        //更新用户表对应的用户同步消息的时间
        if($flag){ //操作成功才更新用户表的最新更细时间
            $r = array();
            $r['eq']['id'] = $uid;
            $ttt = array('mupdate'=>time());
            $this->import('user')->modify($ttt,$r);
        }
        return true;
    }

    /**
     * 删除登录用户的指定站内信
     * @param $mid
     * @return bool
     */
    public function deleteMsg($mid){
        $this->begin('messege');
        $flag = true;
        //删除关联表的信息
        $r = array();
        $r['eq']['uid'] = UID;
        $r['eq']['mid'] = $mid;
        $rst = $this->import('messege_user')->remove($r);
        if($rst){
            //删除对应的消息表
            $r = array();
            $r['eq']['id'] = $mid;
            $r['col'] = array('sendtype');
            $rst2 = $this->import('messege')->find($r);
            if($rst2){
                if($rst2['sendtype'] == 1){//1对1的消息---删除
                    $res = $this->import('messege')->remove(array('eq'=>array('id'=>$mid)));
                    if(!$res){
                        $flag = false;
                    }
                }else if($rst2['sendtype'] == 2){//多对一(群发)消息
                    //查看是否是最后一个该消息的对象----删除
                    $r = array();
                    $r['eq']['mid'] = $mid;
                    $r['col'] = array('id');
                    $rst3 = $this->import('messege_user')->find($r);
                    if(!$rst3){
                        $res = $this->import('messege')->remove(array('eq'=>array('id'=>$mid)));
                        if(!$res){
                            $flag = false;
                        }
                    }
                }
            }
        }else{
            $flag = false;
        }
        if($flag){
            $this->commit('messege');
        }else{
            $this->rollBack('messege');
        }
        return $rst;
    }

    /**
     * 修改站内信(默认修改站内信已读功能)
     * @param $mid
     * @param $data array 修改的数组
     * @return bool
     */
    public function modifyMsg($mid,$data = array()){
        $r = array();
        $r['eq']['uid'] = UID;
        $r['eq']['mid'] = $mid;
        if(!$data){
            $data = array('status'=>1);
        }
        return $this->import('messege_user')->modify($data,$r);
    }

    /**
     * 得到指定用户所有站内信--结果按未读-时间排序
     * @param $p
     * @param $size
     * @return array
     */
    public function getMsg($p,$size){
        //得到总数
        $r = array();
        $r['eq']['uid'] = UID;
        $total = $this->import('messege_user')->count($r);
        //得到分页的用户-信息表中的数据
        $r = array();
        $r['index'] = array($p,$size);
        $r['eq']['uid'] = UID;
        $r['col'] = array('mid','status');
        $r['order'] = array('status'=>'asc','date'=>'desc');
        $rst = $this->import('messege_user')->find($r);
        if($rst){
            //得到对应的用户详情信息
            $mids = arrayColumn($rst,'mid');
            $r = array();
            $r['in']['id'] = $mids;
            $r['limit'] = $this->limit;
            $rst2 = $this->import('messege')->find($r);
            //合并两个数组
            $mids = arrayColumn($rst2,'id');
            $data = array();
            foreach($rst as $v){
                $index = array_search($v['mid'],$mids);
                $data[] = $v + $rst2[$index];
            }
            return array('total'=>$total,'data'=>$data);
        }else{
            return array('total'=>0);
        }
    }

    /**
     * 浏览指定的站内信
     * @param $mid
     * @return array
     */
    public function viewMsg($mid){
        //修改状态
        $this->modifyMsg(UID,$mid);
        //返回结果
        $r = array();
        $r['eq']['id'] = $mid;
        return  $this->import('messege')->find($r);
    }

    /**
     * 得到监控行为的数据信息
     * @return array
     */
    public function getMonitor(){
        //得到监控的信息
        $data = $this->com('redisHtml')->get('messege_monitor');//获得缓存
        if(empty($data)){
            $r = array();
            $r['limit'] = $this->limit;
            $data = $this->import('messege_monitor')->find($r);
            $this->com('redisHtml')->set('messege_monitor', $data, 0);//设置缓存
        }
        return $data;
    }

    /**
     * 得到用户未读的站内信数目
     */
    public function getMsgNum(){
        $r['eq']['uid'] = UID;
        $r['eq']['status'] = 0;
        return $this->import('messege_user')->count($r);
    }
}