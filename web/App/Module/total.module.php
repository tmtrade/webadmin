<?php
/**
 * 用户积分（蝉豆管理）
 * Created by PhpStorm.
 * User: Far
 * Date: 2016/6/27 0007
 * Time: 上午 11:53
 */
class TotalModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'total'         => 'total',
        'totalLog'      => 'totalLog',
        'passSale'      => 'passSale',
    );

    /**
     * 变更用户的蝉豆数
     * @param type $uid
     * @param type $type 1:增加 2：减少
     * @param type $amount
     * @return type
     */
    public function upTotal($uid,$type,$amount,$note){
        $r['eq'] = array('uid'=>$uid);
        if($type==1){
            $record = array(
            'amount'    => array('amount', $amount),
            );
        }else{
            $record = array(
            'amount'    => array('amount', -$amount),
            );
        }
        $this->begin('total');
        $res     = $this->import("total")->modify($record, $r);
        if($res){
            $date = array(
            'uid'       => $uid,
            'types'     => $type,
            'amount'    => $amount,
            'note'      => $note,
            );
            $res1 = $this->addExchangeLog($date);
            if($res1){
                $this->checkMsg($uid,"user/totalchange/");
                $this->commit('total');
                return $res1;
            }else{
                $this->rollBack('total');
            }
        }
        return $res;
    }
    
     /**
     *  变更用户的商品审核通过数
     * @param type $uid
     * @param type $type 1:增加 2：减少
     * @return $res
     */
    public function updatePassCount($uid,$type,$number){
        $r['eq'] = array('uid'=>$uid);
        $list = $this->import('total')->find($r);
        if(empty($list['uid'])){
            $data = array(
                'uid'       => $uid,
                'amount'    => 0,
                'pass_count'=> 0,
                );
            $this->addTotal($data);
        }
        if($type==1){
            //判断原始有无通过
            $p['eq'] = array('uid'=>$uid,'number'=>$number);
            $pass_count = $this->import('passSale')->count($p);
            if( $pass_count>0 ){
                return true;
            }else{
                $data= array(
                    'uid'       => $uid,
                    'number'    => $number,
                );
                $this->addPassSale($data);
            }           
            $count = $list['pass_count']+1;
            if( $count==10 || (($count)%10==0 && $count >= 0)){
                $record = array(
                    'pass_count'    =>0,
                );
            }else{                
                $record = array(
                    'pass_count'    => array('pass_count', 1),
                );
            }
        }else{
            if( $list['pass_count'] <= 0 ){ 
                $record = array(
                    'pass_count'    => 0,
                );                
            }else{                
                $record = array(
                    'pass_count'    => array('pass_count', -1),
                );
            }
        }
        
        $res     = $this->import("total")->modify($record, $r); 
        if( $type == 1 && ($count)%10 == 0 && $count > 0 ){//每通过10条信息加10个蝉豆
            $res2 = $this->upTotal($uid, 1, 10, "审核通过第{$count}条商品");//修改用户豆豆
        }
        return ($res && $res2) ? true : false;
    }     
    
    /**
     * 添加兑换信息记录
     * @param array $data
     */
    public function addExchangeLog($data){
        $res = $this->import('totalLog')->create($data);
        return $res;
    }
    
        
    /**
     * 添加用户信息
     * @param array $data
     */
    public function addTotal($data){
        $res = $this->import('total')->create($data);
        return $res;
    }
    
    /**
     * 添加用户通过商品记录
     * @param array $data
     */
    public function addPassSale($data){
        $res = $this->import('passSale')->create($data);
        return $res;
    }
}