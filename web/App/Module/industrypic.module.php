<?

/**
 * 通栏设置
 *
 *
 *
 * @package    Module
 * @author     Alexey
 * @since      2016年2月18日11:10:07
 */
class IndustryPicModule extends AppModule
{
	public $models = array(
		'industrypic' => 'industrypic',
	);

	//info 多条
	public function getInfo($id)
	{
		$r['eq']    = array(
			'industryId' => $id
		);
		$r['limit'] = 100;
		$r['order'] = array('sort' => 'desc');
		$res        = $this->import('industrypic')->findAll($r);
		return $res;
	}

	//info 单条
	public function getInfoOne($id)
	{
		$r['eq']    = array(
			'id' => $id
		);
		$r['limit'] = 1;
		$res        = $this->import('industrypic')->find($r);
		return $res;
	}
	public function  del($id)
	{
		$r['eq'] = array('id' => $id);
		return $this->import('industrypic')->remove($r);
	}
}

?>