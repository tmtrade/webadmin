<?

header("Content-type: text/html; charset=utf-8");

/**
 * 首页模块设置
 *
 * @package	Action
 * @author	dower
 * @since	2016-03-21
 */
class caseAction extends AppAction {

    //	public $debug = true;
    public $rowNum = 10;

    /**
     * 成功案例列表
     * @throws SpringException
     */
    public function index() {
        //参数
        $params = array();
        $page = $this->input('page', 'int', '1');
        //得到列表
        $res = $this->load('case')->getList($params, $page, $this->rowNum);
        //分页
        $total = empty($res['total']) ? 0 : $res['total'];
        $list = empty($res['rows']) ? array() : $res['rows'];
        $pager = $this->pager($total, $this->rowNum);
        $pageBar = empty($list) ? '' : getPageBar($pager);
        //分配数据
        $this->set('total', $total);
        $this->set("pageBar", $pageBar);
        $this->set('s', $params);
        $this->set('list', $res['rows']);
        $this->display();
    }

    /**
     * 创建案例的弹窗及处理
     * @throws SpringException
     */
    public function add() {
        //get方式渲染页面
        if (!$this->isPost()) {
            $this->display();
            exit;
        }
        //post方式添加案例
        $title = $this->input('title', 'string', '');
        if (empty($title)) {
            $this->returnAjax(array('code' => 2, 'msg' => '请填写标题'));
        }
        $data = array(
            'title' => $title,
            'created' => time(),
            'desc' => '',
            'date' => time(),
        );
        $res = $this->load('case')->addCase($data);
        if ($res) {
            $this->returnAjax(array('code' => 1, 'id' => $res));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '创建失败'));
    }

    /**
     * 编辑成功案例
     * @throws SpringException
     */
    public function edit() {
        //获得参数
        $id = $this->input('id', 'int', '0');
        $case = array();
        //得到数据
        if ($id) {
            $case = $this->load('case')->getCaseInfo($id);
        }

        //获取SEO设置
        $seoInfo = $this->load('seo')->getInfoByType(12, $id);
        $this->set('seoInfo', $seoInfo);
        //分配数据
        $this->set('case', $case);
        $this->display();
    }

    /**
     * 修改成功案例
     * @throws SpringException
     */
    public function setCase() {
        //参数
        $params = $this->getFormData();
        if ($params['id'] <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        $id = $params['id'];
        unset($params['id']);
        $res = $this->load('case')->updateCase($params, $id);
        if ($res) {
            //设置SEO的信息
            $sid = $this->input('sid', 'int', '0');
            $data['vid'] = $id;
            $data['seotitle'] = $this->input('seo_title', 'string', '');
            $data['keyword'] = $this->input('seo_keyword', 'string', '');
            $data['description'] = $this->input('seo_description', 'string', '');
            $data['isUse'] = $this->input('seo_isUse', 'int', '1');
            $reArr = $this->load('seo')->viewSetSeo($sid, $data, 12);
            $this->returnAjax($reArr);
        }
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
    }

    /**
     * 上传图片
     * @throws SpringException
     */
    public function ajaxUploadPic() {
        //获取参数
        $msg = array(
            'code' => 0,
            'msg' => '',
            'img' => '',
        );
        if (empty($_FILES) || empty($_FILES['fileName'])) {
            $msg['msg'] = '请上传图片';
            $this->returnAjax($msg);
        }
        //设置最大图片
        $size = 1024000; //1M
        $obj = $this->load('upload')->uploadAdPic('fileName', $size, 'img');
        if ($obj->_imgUrl_) {
            $msg['code'] = 1;
            $msg['img'] = $obj->_imgUrl_;
        } else {
            $msg['msg'] = $obj->msg;
        }
        $this->returnAjax($msg);
    }

    /**
     * 删除成功案例
     * @throws SpringException
     */
    public function delCase() {
        //获得id
        $id = $this->input('id', 'int', '0');
        //执行操作
        $res = $this->load('case')->delCase($id);
        //返回结果
        if ($res) {
            $this->returnAjax(array('code' => 1));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '删除错误'));
    }

    /**
     * 列表排序功能
     * @throws SpringException
     */
    public function sortChaneg() {
        //获得参数
        $id = $this->input('id', 'int', 0);
        $updown = $this->input('updown', 'int', 0); //1上，2下
        $caseId = $this->input('caseId', 'int', 0);
        $where = '';
        if ($caseId) {
            $where = array('caseId' => $caseId);
        }
        if ($id <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        //排序处理
        $res = $this->load('case')->sortUpDown($id, $updown, $where);
        //返回结果
        if ($res) {
            $this->returnAjax(array('code' => 1));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '排序失败'));
    }

}
