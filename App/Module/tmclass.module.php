<?

/**
 * 通栏设置
 *
 * 国内商标商品创建，修改，删除等
 *
 * @package    Module
 * @author     Alexey
 * @since      2016年2月18日11:10:07
 */
class TmclassModule extends AppModule
{
	public $models = array(
		'tmclass'            => 'tmclass',
		'group'            => 'group',
	);
    //添加分类数据
	/*public  function test(){
		$r['limit'] = 1000;
		$res        = $this->import('group')->findAll($r);
		foreach($res['rows'] as $k=>$val){
			$rg['eq'] = array("class"=>$val['id']);
			$rg['limit'] = 1000;
			$resg        = $this->import('group')->findAll($rg);
			foreach($resg['rows'] as $kg=>$gv){
				$data['parent'] = $val['id'];
				$data['number'] = $gv['group'];
				$data['name'] = $gv['cn_name'];
				//$rm['eq']      = array('id' =>$k);
				$res          = $this->import('tmclass')->create($data);
			}
		}
	}*/
	//index 列表
	public function getList()
	{
		$r          = array();
		$r['eq']    = array('parent' => "0");
		$r['limit'] = 45;
		$r['order'] = array('sort' => 'asc');
		$res        = $this->import('tmclass')->findAll($r);
		foreach($res['rows'] as $k=>$val){
			$rg['eq']    = array('parent' => $val['id']);
			$resg        = $this->import('tmclass')->findAll($rg);
			$res['rows'][$k]['counts'] = $resg['total'];
		}
		return $res;
	}
	//info 单条
	public function getInfo($id)
	{
		$r['eq']    = array(
			'id' => $id
		);
		$r['limit'] = 1;
		$res        = $this->import('tmclass')->find($r);
		if (!empty($res['label'])) {
			$res['labelArr'] = explode(";", $res['label']);
		}
		return $res;
	}
	//群组
	public function getGroupInfo($id)
	{
		$r['eq']    = array(
			'parent' => $id
		);
		$r['order'] = array('sort' => 'asc');
		$r['limit'] = 100;
		$res        = $this->import('tmclass')->findAll($r);
		if (!empty($res['label'])) {
			$res['labelArr'] = explode(";", $res['label']);
		}
		return $res;
	}
	//移除标签
	public function removeLabel($id, $label)
	{
		$mres       = 0;
		$r['eq']    = array(
			'id' => $id
		);
		$r['limit'] = 1;
		$res        = $this->import('tmclass')->find($r);
		if (!empty($res['label'])) {
			$res['labelArr'] = explode(";", $res['label']);
			unset($res['labelArr'][array_search($label, $res['labelArr'])]);
			$data['label'] = implode(";", $res['labelArr']);
			$rm['eq']      = array('id' => $res['id']);
			$mres          = $this->import('tmclass')->modify($data, $rm);
		}
		return $mres;
	}
	//编辑标题
	public function editTitle($id, $title)
	{
		$r['eq']    = array(
			'id' => $id
		);
		$data['title']=$title;
		$mres          = $this->import('tmclass')->modify($data, $r);
		return $mres;
	}
	//添加标签
	public function addLabel($id, $label)
	{
		$mres       = 0;
		$r['eq']    = array(
			'id' => $id
		);
		$r['limit'] = 1;
		$res        = $this->import('tmclass')->find($r);
		if (!empty($res['label'])) {
			$res['labelArr'] = explode(",", $res['label']);
			if (in_array($label, $res['labelArr'])) {
				$mres = 2; //有重复的了。
			} else {
				$res['labelArr'][] = $label;
				$data['label']     = implode(",", $res['labelArr']);
				$rm['eq']          = array('id' => $res['id']);
				$this->import('tmclass')->modify($data, $rm);
				$mres = 1;
			}
		}else{
			$data['label']     = $label;
			$rm['eq']          = array('id' => $id);
			$this->import('tmclass')->modify($data, $rm);
			$mres = 1;
		}
		return $mres;
	}
	//设置排序  t=1 降、t=2升。  $y=1 第一层 class  $y=2 第二层 group
	public function setClassSort($sort, $t, $p)
	{
		$table    = "tmclass";
		$dataName = "trade_new";
		$t == 1 ? $where = " sort <=" . $sort : $where = " sort >=" . $sort;
		$t == 1 ? $order = " order by sort desc " : $order = " order by sort asc ";
		$where .= " and parent=".$p;
		//$y == 1 ? $where = " parent = 0 "  : $where = " parent =" . $sort;
		$limit = " limit 2 ";
		$sql   = "select id,sort from t_class_group where" . $where . $order . $limit;

		$res   = $this->load("tmclass")->fetchAll($dataName, $sql);
		if (count($res) < 2) {
			return "0";//已经无法在升降了。
		} else {
			$data         = array();
			$r            = array();
			$data['sort'] = $res[0]['sort'];
			$r['eq']      = array('id' => $res[1]['id']);
			$this->update($table, $data, $r);
			$data         = array();
			$r            = array();
			$data['sort'] = $res[1]['sort'];
			$r['eq']      = array('id' => $res[0]['id']);
			return $this->update($table, $data, $r);
		}
	}
	public function update($table, $data, $r)
	{
		return $this->import($table)->modify($data, $r);
	}
	/**
	 * 直接执行sql
	 *
	 * @author    martin
	 * @since     2015/10/21
	 *
	 * @access    public
	 *
	 * @param     string $dbName 字符串
	 *
	 * @return    void
	 */
	public function fetchAll($dbName, $sql)
	{
		static $db = null;
		if ($db == null) {
			$db = new DbQuery($dbName);
		}
		return $db->fetchAll($sql);
	}

}
?>