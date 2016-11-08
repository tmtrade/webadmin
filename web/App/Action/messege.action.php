<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/8 0008
 * Time: 上午 10:03
 */
class MessegeAction extends AppAction{

    /**
     * 得到站内信模板列表
     */
    public function index(){
        //得到数据
        $data = $this->load('tmessege')->getList();
        //渲染页面
        $this->set('data',$data);
        $this->display();
    }

    /**
     * 渲染添加模板页面
     */
    public function addpage(){
        $config = $this->load('tmessege')->getConfig();
        $this->set('config',$config);
        $this->display();
        exit;
    }

    /**
     * 添加新的模板
     */
    public function add(){
        //收集数据
        $params = array();
        $params['url'] = $this->input('url','string');
        $params['title'] = $this->input('title','string');
        $params['type'] = $this->input('type','int');
        $params['content'] = $this->input('content','text');
        //简单验证不为空
        if(!$params['url'] || !$params['title'] || !$params['type']){
            $this->returnAjax(array('code'=>3,'msg'=>'数据不完整'));
        }
        //添加数据
        $rst = $this->load('tmessege')->add($params);
        if($rst==1){
            $this->clearMonitorCache();
            $this->returnAjax(array('code'=>0));
        }else if($rst==-1){
            $this->returnAjax(array('code'=>1,'msg'=>'已经有相同的触发条件'));
        }else{
            $this->returnAjax(array('code'=>2,'msg'=>'添加失败'));
        }
    }

    /**
     * 渲染编辑模板页面
     */
    public function editpage(){
        //获得参数
        $id = $this->input('id','int');
        if($id<=0){
            return false;
        }
        //配置文件
        $config = $this->load('tmessege')->getConfig();
        $this->set('config',$config);
        //查询结果并渲染页面
        $monitor = $this->load('tmessege')->getDetail($id);
        $this->set('monitor',$monitor);
        $this->display();
    }

    /**
     * 编辑模板
     */
    public function edit(){
        //收集数据
        $params = array();
        $params['url'] = $this->input('url','string');
        $params['id'] = $this->input('id','int');
        $params['title'] = $this->input('title','string');
        $params['type'] = $this->input('type','int');
        $params['content'] = $this->input('content','text');
        //简单验证不为空
        if(!$params['url'] || !$params['title'] || !$params['type'] || !$params['id']){
            $this->returnAjax(array('code'=>3,'msg'=>'数据不完整'));
        }
        //编辑数据
        $rst = $this->load('tmessege')->edit($params);
        if($rst==1){
            $this->clearMonitorCache();
            $this->returnAjax(array('code'=>0));
        }else if($rst==-1){
            $this->returnAjax(array('code'=>1,'msg'=>'已经有相同的触发条件'));
        }else{
            $this->returnAjax(array('code'=>2,'msg'=>'编辑失败'));
        }
    }

    /**
     * 删除站内信模板
     */
    public function delete(){
        $id = $this->input('id','int');
        $rst = $this->load('tmessege')->drop($id);
        if($rst){
            $this->clearMonitorCache();
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'删除失败'));
        }
    }

    /**
     * 删除站内信监控的redis缓存
     */
    private function clearMonitorCache(){
        $this->com('redisHtml')->remove('messege_monitor');//删除缓存
    }
}