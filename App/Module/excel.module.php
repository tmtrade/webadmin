<?php

class ExcelModule extends AppModule
{

	public $source = array();
	public $deal = array();

	/**
	 * 引用业务模型
	 */
	public $models = array(
		'sale'          => 'sale',
	);

	/**
	 * 初始化变量值
	 * @author    martin
	 * @since     2015-07-22
	 */
	public function __construct()
	{
		$this->source = C("SOURCE");
		$this->deal   = C("DEAL_STATUS");
	}
	
	/**
	 * 把EXCEL里面的数据导出到一个数组中
	 * @author    Jeany
	 * @since     2016-1-21
	 */
	public function PHPExcelToArr($filePath){
		require_once(FILEDIR."/App/Util/PHPExcel.php");	
		$filePath = FILEDIR.$filePath;
		//建立reader对象  
		$PHPReader = new PHPExcel_Reader_Excel2007(); 
		if(!$PHPReader->canRead($filePath)){  
			$PHPReader = new PHPExcel_Reader_Excel5();  
			if(!$PHPReader->canRead($filePath)){  
				echo 'no Excel';  
				return ;  
			}  
		} 
		
		//建立excel对象，此时你即可以通过excel对象读取文件，也可以通过它写入文件  
		$PHPExcel = $PHPReader->load($filePath);  
		  
		/**读取excel文件中的第一个工作表*/  
		$currentSheet = $PHPExcel->getSheet(0);  
		/**取得最大的列号*/  
		$allColumn = $currentSheet->getHighestColumn();  
		/**取得一共有多少行*/  
		$allRow = $currentSheet->getHighestRow();  
		
		$sbArr = array();
		 
		//循环读取每个单元格的内容。注意行从1开始，列从A开始  
		for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){  
			$Adata = $currentSheet->getCell('A'.$rowIndex)->getValue();
			if($Adata){
				for($colIndex='A';$colIndex<=$allColumn;$colIndex++){  
					$addr = $colIndex.$rowIndex;  
					$cell = $currentSheet->getCell($addr)->getValue();  
					
					if($cell instanceof PHPExcel_RichText){
						//富文本转换字符串  
						$cell = $cell->__toString(); 
					}
					$key = $rowIndex-2;
					
					switch($colIndex){
						case 'A' :
							$sbArr[$key]['number'] = trim($cell);
							break;
						case 'B' :
							$sbArr[$key]['name'] = $cell;
							break;
						case 'C' :
							$sbArr[$key]['phone'] = $cell;
							break;
						case 'D' :
							$sbArr[$key]['price'] = $cell;
							break;
						case 'E' :
							$sbArr[$key]['advisor'] = $cell;
							break;
						case 'F' :
							$sbArr[$key]['department'] = $cell;
							break;
						case 'G' :
							$sbArr[$key]['memo'] = $cell;
							break;
					}
				} 
			}
		}	
		return $sbArr;
	}
	
	
	/**
	 * 导出excel  上传失败的
	 * @author    Jeany
	 * @since     2016/1/22
	 * @access    public
	 * @param    array $data 求购信息
	 * @return   array
	 */
	public function upErrorExcel($saleExists, $saleNotHas, $numSucess, $saleError)
	{
		require_once(FILEDIR."/App/Util/PHPExcel.php");	
		$PHPExcel = new PHPExcel();
		$PHPExcel->getProperties()->setCreator("chofn")
			->setLastModifiedBy("chofn")
			->setTitle("chofn")
			->setSubject("超凡商标导入统计")
			->setDescription("")
			->setKeywords("商标导入统计")
			->setCategory("");
		$PHPExcel->setActiveSheetIndex(0);
		$PHPExcel->getActiveSheet()->setTitle('商标导入信息');
		$PHPExcel->setActiveSheetIndex(0);
		//合并单元格
		$PHPExcel->getActiveSheet()->mergeCells('B1:G1');
		$PHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$PHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->getStartColor()->setRGB('e86b1d');
		//设置居中
		$PHPExcel->getActiveSheet()->getStyle('A1:B1')->getAlignment()->setHorizontal(
			PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		);
		//所有垂直居中
		$PHPExcel->getActiveSheet()->getStyle('A1:B1')->getAlignment()->setVertical(
			PHPExcel_Style_Alignment::VERTICAL_CENTER
		);
		$PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('微软雅黑');
		$PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//字体颜色
		$PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		$PHPExcel->getActiveSheet()->setCellValue('A1', " 超凡-商标导入信息");
		$PHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(
			PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		);
		$PHPExcel->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		$PHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setName('微软雅黑');
		$PHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(9);
		$PHPExcel->getActiveSheet()->setCellValue('B1',
			"报告编号：" . date('Ymd', time()) . randCode(4, 'NUMBER') . '  数据截止时间：' . date(
				'Y/m/d',
				time()
			) . '  导出时间：' . date('Y/m/d', time()) . "  "
		);
		
		//第二行-----------------------------------------------------------
		$PHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$PHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('微软雅黑');
		$PHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(16);
		$PHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$PHPExcel->getActiveSheet()->getStyle('A2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		//设置居中
		$PHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(
			PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		);
		//所有垂直居中
		$PHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(
			PHPExcel_Style_Alignment::VERTICAL_CENTER
		);
		$numExists = count($saleExists);
		$numNotHas = count($saleNotHas);
		$numNError = count($saleError);
		$error = $numExists+$numNotHas+$numNError;
		$PHPExcel->getActiveSheet()->setCellValue('A2',
			"导入成功".$numSucess."条   共导入失败".$error."条  数据写入失败".$numNError."条  数据表已存在商标".$numExists."条  不存在的商标".$numNotHas."条"
		);
		//----------------全局---------------------------------------------
		//设置单元格宽度
		$PHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$PHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$PHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$PHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$PHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		//设置单元格高度
		$PHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(35);
		//单元格样式
		$style_obj   = new PHPExcel_Style();
		$style_array = array(
			'font'      => array(
				'size' => 10.5,
				'name' => '微软雅黑'
			),
			'borders'   => array(
				'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		$style_obj->applyFromArray($style_array);
		$PHPExcel->getActiveSheet()->setCellValue('A3', "商标号");
		$PHPExcel->getActiveSheet()->setCellValue('B3', "联系人");
		$PHPExcel->getActiveSheet()->setCellValue('C3', "联系电话");
		$PHPExcel->getActiveSheet()->setCellValue('D3', "底价");
		$PHPExcel->getActiveSheet()->setCellValue('E3', "顾问");
		$PHPExcel->getActiveSheet()->setCellValue('F3', "顾问部门");
		$PHPExcel->getActiveSheet()->setCellValue('G3', "备注");
		//第三行--------------------------------------------------------
		$num = 3 ;
		if($saleExists){
			$num = $num+1;	
			$PHPExcel->getActiveSheet()->mergeCells('A'.$num.':G'.$num);
			$PHPExcel->getActiveSheet()->setCellValue('A'.$num, "数据表已存在商标");
			foreach($saleExists as $k => $item ){
				$num ++;
				$PHPExcel->getActiveSheet()->setCellValue('A'.$num, $item['number']);
				$PHPExcel->getActiveSheet()->setCellValue('B'.$num, $item['name']);
				$PHPExcel->getActiveSheet()->setCellValue('C'.$num, $item['phone']);
				$PHPExcel->getActiveSheet()->setCellValue('D'.$num, $item['price']);
				$PHPExcel->getActiveSheet()->setCellValue('E'.$num, $item['advisor']);
				$PHPExcel->getActiveSheet()->setCellValue('F'.$num, $item['department']);
				$PHPExcel->getActiveSheet()->setCellValue('G'.$num, $item['memo']);
			}
		}
		if($saleNotHas){
			$num = $num+1;
			$PHPExcel->getActiveSheet()->mergeCells('A'.$num.':G'.$num);
			$PHPExcel->getActiveSheet()->setCellValue('A'.$num, "该商标号错误、无对应商标号");
			foreach($saleNotHas as $k => $item ){
				$num ++;
				$PHPExcel->getActiveSheet()->setCellValue('A'.$num, $item['number']);
				$PHPExcel->getActiveSheet()->setCellValue('B'.$num, $item['name']);
				$PHPExcel->getActiveSheet()->setCellValue('C'.$num, $item['phone']);
				$PHPExcel->getActiveSheet()->setCellValue('D'.$num, $item['price']);
				$PHPExcel->getActiveSheet()->setCellValue('E'.$num, $item['advisor']);
				$PHPExcel->getActiveSheet()->setCellValue('F'.$num, $item['department']);
				$PHPExcel->getActiveSheet()->setCellValue('G'.$num, $item['memo']);
			}
		}
		if($saleNotHas){
			$num = $num+1;
			$PHPExcel->getActiveSheet()->mergeCells('A'.$num.':G'.$num);
			$PHPExcel->getActiveSheet()->setCellValue('A'.$num, "数据存入错误商标");
			foreach($saleError as $k => $item ){
				$num ++;
				$PHPExcel->getActiveSheet()->setCellValue('A'.$num, $item['number']);
				$PHPExcel->getActiveSheet()->setCellValue('B'.$num, $item['name']);
				$PHPExcel->getActiveSheet()->setCellValue('C'.$num, $item['phone']);
				$PHPExcel->getActiveSheet()->setCellValue('D'.$num, $item['price']);
				$PHPExcel->getActiveSheet()->setCellValue('E'.$num, $item['advisor']);
				$PHPExcel->getActiveSheet()->setCellValue('F'.$num, $item['department']);
				$PHPExcel->getActiveSheet()->setCellValue('G'.$num, $item['memo']);
			}
		}
		
		//---------------------------------------------------------------------------
		$PHPExcel->setActiveSheetIndex(0);

		$filename  = iconv('utf-8', 'gbk', "商标导入信息");
		//$filenames = $filename . date('Ymd', time()) . $code; //防止乱码
		$filenames = "errorexcel" . date('Ymd', time()) . $code; //防止乱码
		$objWriter = new PHPExcel_Writer_Excel5($PHPExcel);
		header("Content-type:application/octet-stream");
		header("Accept-Ranges:bytes");
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=" . $filenames . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		$savepath = UPLOADEXCEL.$filenames . ".xls";
		$pathfile = UPLOADEXCELED.$filenames . ".xls";
		$objWriter->save($savepath);
		return $pathfile;
	}
	
}

?>