<?
header("Content-type: text/html; charset=utf-8");

/**
 * 商标分类
 *
 * @package    Action
 * @author     Alexey
 * @since      2016年2月17日14:26:46
 */
class tmclassAction extends AppAction
{
	//public $debug = true;
	/**
	 * 商标分类
	 *
	 * @author    Alexey
	 * @since     2016年2月17日
	 * @access    public
	 * @return    void
	 */
	public function index()
	{
		$res = $this->load('tmclass')->getList();
		$this->set('list', $res['rows']);
		$this->display();
	}
	//编辑分类
	public function edit()
	{
		$id = $this->input('id', 'int', 0);
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		$tmclass = $this->load('tmclass')->getInfo($id);
		//群组
		$ginfo = $this->load('tmclass')->getGroupInfo($id);
		$this->set('ginfo', $ginfo);
		$this->set('tmclass', $tmclass);
		$this->display();
	}
	//移除标签
	public function removeLabel()
	{
		$id    = $this->input('id', 'int', 0);
		$lalel = $this->input('lalel', 'text', '');
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		$res = $this->load('tmclass')->removeLabel($id, $lalel);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '移除失败。'));
		} else {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功。'));
		}
	}
	//添加标签
	public function addLabel()
	{
		$id    = $this->input('id', 'int', 0);
		$label = $this->input('label', 'text', '');
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		if ($label == '') {
			MessageBox::halt('标签不能为空');
		}
		$res = $this->load('tmclass')->addLabel($id, $label);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '操作失败。'));
		} elseif ($res == 1) {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功。'));
		} elseif ($res == 2) {
			$this->returnAjax(array('code' => 0, 'msg' => '不能添加重复标签。'));
		}
	}
    //编辑标题
	public function editTitle()
	{
		$id    = $this->input('id', 'int', 0);
		$title = $this->input('typeName', 'text', '');
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		if ($title == '') {
			MessageBox::halt('标签不能为空');
		}
		$res = $this->load('tmclass')->editTitle($id, $title);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '操作失败。'));
		} elseif ($res == 1) {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功。'));
		} elseif ($res == 2) {
			$this->returnAjax(array('code' => 0, 'msg' => '不能添加重复标签。'));
		}
	}
	//编辑群组
	public function editGroup()
	{
		$id         = $this->input('id', 'int', '0');
		$tmclass      = "";
		if ($id != 0) {
			$tmclass = $this->load('tmclass')->getInfo($id);
		}
		$this->set('tmclass', $tmclass);
		$this->display();
	}
	/**
	 * 设置排序
	 *
	 * @author    Alexey
	 * @since     2016年2月18日16:37:15
	 *
	 * @access    public
	 * @return    void
	 */
	//
	public function setClassSort()
	{
		$sort = trim($this->input("s"));
		$t    = trim($this->input("t"));
		$p    = trim($this->input("p"));
		$res  = $this->load("tmclass")->setClassSort($sort, $t,$p);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '已经无法升降了。'));
		} else {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功.'));
		}

	}
}

?>