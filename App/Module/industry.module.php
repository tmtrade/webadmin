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
class IndustryModule extends AppModule
{
	public $models = array(
		'sale'     => 'sale',
		'contact'  => 'saleContact',
		'tminfo'   => 'saleTminfo',
		'history'  => 'saleHistory',
		'industry' => 'industry',
	);

	//添加分类
	public function addIndustry($data)
	{
		$res = $this->import('industry')->create($data);
		if ($res) {
			$dataNew['sort'] = $res;
			$r['eq']         = array('id' => $res);
			$res             = $this->import('industry')->modify($dataNew, $r);
		}
		return $res;
	}

	//index 列表
	public function getList($page, $limit = 20)
	{
		$r          = array();
		$r['page']  = $page;
		$r['limit'] = $limit;
		$r['order'] = array('sort' => 'desc');
		$res        = $this->import('industry')->findAll($r);
		return $res;
	}

	//设置排序  t=1 降、t=2升。
	public function setSort($sort, $t)
	{
		$dataName = "trade_new";
		$t == 1 ? $where = " sort <=" . $sort : $where = " sort >=" . $sort;
		$t == 1 ? $order = " order by sort desc " : $order = " order by sort asc ";
		$limit = " limit 2 ";
		$sql   = "select id,sort from t_industry where" . $where . $order . $limit;
		$res   = $this->load("industry")->fetchAll($dataName, $sql);
		if (count($res) < 2) {
			return "0";//已经无法在升降了。
		} else {
			$data         = array();
			$r            = array();
			$data['sort'] = $res[0]['sort'];
			$r['eq']      = array('id' => $res[1]['id']);
			$this->update($data, $r);
			$data         = array();
			$r            = array();
			$data['sort'] = $res[1]['sort'];
			$r['eq']      = array('id' => $res[0]['id']);
			return $this->update($data, $r);
		}
	}
	public function update($data, $r)
	{
		return $this->import('industry')->modify($data, $r);
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