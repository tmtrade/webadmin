<?php
/**
 * 需求管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/22 0022
 * Time: 上午 10:31
 */
class RequireAction extends AppAction{

    private $list_num = 30;

    /**
     * 原始需求信息列表(页面)
     */
    public function raw(){
        //获得参数
        $param = array();
        $param['page'] = $this->input('page', 'int', '1');
        //查询数据
        $list = $this->load('require')->rawList($param,$this->list_num);
        //分页条
        $pager = $this->pager($list['total'], $this->list_num);
        $pageBar = empty($list['rows']) ? '' : getPageBar($pager);
        //渲染页面
        $this->set('list',$list['rows']);
        $this->set('total',$list['total']);
        $this->set('pageBar',$pageBar);
        $this->set('t_type',2);
        $this->display();
    }

    /**
     * 需求信息列表(页面)
     */
    public function index(){
        //获得参数
        $param = array();
        $param['page'] = $this->input('page', 'int', '1');
        //查询数据
        $list = $this->load('require')->requireList($param,$this->list_num);
        //分页条
        $pager = $this->pager($list['total'], $this->list_num);
        $pageBar = empty($list['rows']) ? '' : getPageBar($pager);
        //渲染页面
        $this->set('list',$list['rows']);
        $this->set('total',$list['total']);
        $this->set('isNew',$list['isNew']);
        $this->set('pageBar',$pageBar);
        $this->set('t_type',1);
        $this->display();
    }

    /**
     * 将原始需求转化为平台需求信息
     */
    public function addToRequire(){
        $id = $this->input('id','int');
        if(!$id) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        //得到基础信息
        $data = $this->load('require')->getBasicRequire($id);
        if(!$data) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        //创建需求信息
        $res = array(
            'mobile'=>$data['mobile'],
            'name'=>$data['name'],
            'desc'=>$data['remarks'],
            'date'=>time(),
        );
        $this->handleRequire($res);
    }

    /**
     * 添加需求信息(页面)
     */
    public function addrequire(){
        $this->display();
    }

    /**
     * 删除需求信息
     */
    public function removeRequire(){
        $id = $this->input('id','string');
        $ids = explode(',',$id);
        $ids = array_filter($ids);//去空
        if(!$ids) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        //删除数据
        $res = $this->load('require')->delete($ids);
        //返回结果
        if($res){
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'操作失败'));
        }
    }

    /**
     * 编辑需求信息(页面)
     */
    public function edit(){
        $id = $this->input('id','int',0);
        $type = $this->input('type','int',1);
        //得到数量和状态
        $tips = $this->load('require')->getTips($id);
        $this->set('tips',$tips);
        //查询数据
        if($type==1){ //需求信息
            $require = $this->load('require')->getRequire($id);
            $this->set('require',$require);
        }else{ //需求竞标信息
            $page = $this->input('page','int',1);
            $list = $this->load('require')->getBids($id,$this->list_num,$page);
            //分页条
            $pager = $this->pager($list['total'], $this->list_num);
            $pageBar = empty($list['rows']) ? '' : getPageBar($pager);
            $this->set('bids',$list);
            $this->set('pageBar',$pageBar);
            //得到numbers
            $numbers = $this->load('require')->getNumbers($id);
            if($numbers){
                $url = SELLER_URL.'quotation/addgoods/?numbers='.$numbers;
            }else{
                $url = SELLER_URL.'quotation/addgoods/';
            }
            $this->set('quotation_url',$url);
        }
        //渲染页面
        $this->set('id',$id);
        $this->set('type',$type);
        $this->display();
    }

    /**
     * 处理需求信息
     * @param $data array 需求信息 (用于手动调用)
     * @param $id int 需求信息id
     */
    public function handleRequire($data=array(),$id=0){
        //获取信息
        if(!$data){
            $id = $this->input('id','int',0);
            $mobile = $this->input('mobile','string');
            $name = $this->input('name','string');
            $date = $this->input('date','string');
            $desc = $this->input('desc','string');
            $status = $this->input('status','int',0);
            if(!$mobile) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
            $data = array(
                'mobile'=>$mobile,
                'name'=>$name,
                'status'=>$status,
            );
            if($id){
                if(!$data) $data = time();
                $data['date'] = $date;
            }
            if($desc){
                $data['desc'] = $desc;
            }
        }
        //处理数据
        if($id){//编辑
            $res = $this->load('require')->edit($data,$id);
        }else{//添加
            $res = $this->load('require')->add($data);
        }
        //返回结果
        if($res){
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'操作失败'));
        }
    }

    /**
     * 需求竞价状态改变
     */
    public function changeBid(){
        $id = $this->input('id','int',0);
        $type = $this->input('type','int',0);
        $res = $this->load('require')->changeBid($id,$type);
        if($res){
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'操作失败'));
        }
    }
}