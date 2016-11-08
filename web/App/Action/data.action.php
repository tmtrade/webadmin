<?
/**
 * 数据处理
 *
 * @package	Action
 * @author	Jeany
 * @since	2015-11-04
 */
class DataAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 存储包装图片
	 * 
	 * @author	Jeany
	 * @since	2015-12-14
	 *
	 * @access	public
	 * @return	void
	 */
	public function uploadBzpic()
	{	
		$dir = '.'.StaticDir."upload/20160111/";  //要获取的目录
		$num = 0;
		$errorArr = array();
		//先判断指定的路径是不是一个文件夹
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while (($file = readdir($dh))!= false){
					//文件名的全路径 包含文件名
					if($file != ".." && $file != "."){
						$arrPic = explode(".",$file);
						if(is_array($arrPic) && (int)$arrPic[0] > 0){
							$filePath = substr($dir.$file,1);
							
							$data = $this->load('internal')->getSaleContactByNum($arrPic[0]);
							if($data){
								$picArr = array("bzpic"=>$filePath);
								$result = $this->load('internal')->updateContactBZpic($arrPic[0],$picArr);
								$num ++ ;
							}else{
								$errorArr[] = $arrPic[0];
							}
						}
					}
				}
				closedir($dh);
				echo "over 写入".$num."条";
				var_dump($errorArr);
			}
		}
	}
}
?>