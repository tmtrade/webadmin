<?
/**
 * 出售接口
 *
 * 获取出售信息，添加出售信息
 * 
 * @package	Module
 * @author	void
 * @since	2015-06-11
 */
class DataModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'member'		=> 'member',
		'proposer'		=> 'proposer',
        'saletrademark' => 'saletrademark',
        'buy'			=> 'buy',
    );
	

	public function buyInfoError(){
		$r['limit']  =100000;
		$r['page']   = 1;
		$data = $this->import("buy")->find($r);
		foreach($data as $item){
			
			$t['eq']	= array('buyId'=>$item['id']);
			$t['limit'] = 1;
			$t['page']  = 1;
			$t['order'] = array("id"=>"asc");
			$track		= $this->import("track")->find($t);
			if( !empty($track) ){
				$buy ['eq'] = array('id' => $item['id'] );
				$buyinfo    = array();

				if(($track['date'] - 600) < $item['date']){
					$buyinfo['allotDate'] = $item['date'];
				}else{
					$buyinfo['allotDate'] = $track['date'] - 600;
				}
				$buyinfo['firstDealDate'] = $track['date'];
				$buyinfo['changeSale']	  = 1;

				$this->import("buy")->modify($buyinfo, $buy);
			}

		}
	}
}
?>