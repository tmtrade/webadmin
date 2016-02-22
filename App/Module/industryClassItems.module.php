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
class IndustryClassItemsModule extends AppModule
{
	public $models = array(
		'industryclassitems' => 'industryclassitems',
	);

	public function  del($id)
	{
		$r['eq'] = array('classId' => $id);
		return $this->import('industryclassitems')->remove($r);
	}
}

?>