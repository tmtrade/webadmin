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
		'sale'               => 'sale',
		'contact'            => 'saleContact',
		'tminfo'             => 'saleTminfo',
		'history'            => 'saleHistory',
		'industry'           => 'industry',
		'industryclass'      => 'industryclass',
		'industryclassitems' => 'industryclassitems',
		'industrypic'        => 'industrypic',

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

	//添加子分类
	public function addIndustryClass($data)
	{
		$res = $this->import('industryclass')->create($data);
		if ($res) {
			$dataNew['sort'] = $res;
			$r['eq']         = array('id' => $res);
			$this->import('industryclass')->modify($dataNew, $r);
		}
		return $res;
	}


	//添加子分类 edit
	public function editIndustryClass($data,$id)
	{
		$rc['eq']         = array('id' => $id);
		$res = $this->import('industryclass')->modify($data,$rc);
		return $res;
	}

	//添加子分类 del  对应 items
	public function delIndustryClassItems($id)
	{
		$rc['eq']         = array('classId' => $id);
		$res = $this->import('industryclassitems')->remove($rc);
		return $res;
	}

	//添加子分类 行业分类子类信息表
	public function addIndustryClassItems($data)
	{
		$res = $this->import('industryclassitems')->create($data);
		if ($res) {
			$dataNew['sort'] = $res;
			$r['eq']         = array('id' => $res);
			$this->import('industryclassitems')->modify($dataNew, $r);
		}
		return $res;
	}

	//编辑分类
	public function editIndustry($data, $id)
	{
		$r['eq'] = array('id' => $id);
		$res     = $this->import('industry')->modify($data, $r);
		return $res;
	}

	//添加分类 图片
	public function addIndustryPic($data)
	{
		$res = $this->import('industrypic')->create($data);
		if ($res) {
			$dataNew['sort'] = $res;
			$r['eq']         = array('id' => $res);
			$res             = $this->import('industrypic')->modify($dataNew, $r);
		}
		return $res;
	}

	//编辑分类 图片
	public function editIndustryPic($data, $id)
	{
		$r['eq'] = array('id' => $id);
		$res     = $this->import('industrypic')->modify($data, $r);
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

	//info 单条
	public function getInfo($id)
	{
		$r['eq']    = array(
			'id' => $id
		);
		$r['limit'] = 1;
		$res        = $this->import('industry')->find($r);
		return $res;
	}

	//设置排序  t=1 降、t=2升。
	public function setSort($sort, $t)
	{
		$table    = "industry";
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
			$this->update($table, $data, $r);
			$data         = array();
			$r            = array();
			$data['sort'] = $res[1]['sort'];
			$r['eq']      = array('id' => $res[0]['id']);
			return $this->update($table, $data, $r);
		}
	}

	//设置排序 pic  t=1 降、t=2升。
	public function setPicSort($sort, $t)
	{
		$dataName = "trade_new";
		$table    = "industrypic";
		$t == 1 ? $where = " sort <=" . $sort : $where = " sort >=" . $sort;
		$t == 1 ? $order = " order by sort desc " : $order = " order by sort asc ";
		$limit = " limit 2 ";
		$sql   = "select id,sort from t_industry_pic where" . $where . $order . $limit;
		$res   = $this->load("industry")->fetchAll($dataName, $sql);
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

	//设置排序 子分类  t=1 降、t=2升。
	public function setItemsSort($sort, $t)
	{
		$dataName = "trade_new";
		$table    = "industryclass";
		$t == 1 ? $where = " sort <=" . $sort : $where = " sort >=" . $sort;
		$t == 1 ? $order = " order by sort desc " : $order = " order by sort asc ";
		$limit = " limit 2 ";
		$sql   = "select id,sort from t_industry_class where" . $where . $order . $limit;
		$res   = $this->load("industry")->fetchAll($dataName, $sql);
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