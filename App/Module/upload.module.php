<?php
/**
 * 图片上传
 *
 * 
 * @package	Module
 * @author	garrett
 * @since	2015-07-22
 */
class UploadModule extends AppModule
{
	 /**
     * 附件基路径
     */
    public $base      = array();

    /**
     * 允许上传文件的类型(扩张名)
     */
    public $type      = 'jpeg|jpg|gif|png|txt|doc|docx|ppt|pptx|xlsx|xls|rar|zip|pdf|bmp';

    /**
     * 上传文件的最大字节数(默认10M)
     */
    public $maxSize   = 10000000;
    
    /**
     * 存在路径
     */
    public $path      = 'upload';
    
    /**
	 * 图片上传 
	 *
	 * @return
	 *
	 * @author   
	 * @since     
	 * @copyright
	 */
    public function upload($name, $type='all')
    {
        if ($type == 'img') $this->type = 'jpeg|jpg|gif|png|bmp';
    	$filename    = $_FILES[$name]['name'];
        $up          = $this->com('upload');
        $up->maxSize = $this->maxSize;
        $up->path    = './'.StaticDir.$this->path.'/bao';//包装图片
        $up->upType  = $this->type;
        $up->upload($_FILES[$name]);
        if ( empty($up->msg) ){
            $up->_imgUrl_ = StaticDir.$this->path.'/'.$up->upFile;
        }else{
            $up->_imgUrl_ = '';
        }        
        return $up;
    }
	
	
	/**
	 * excel上传 
	 *
	 * @return
	 *
	 * @author   
	 * @since     
	 * @copyright
	 */
    public function uploadExcel($name, $type='all')
    {
        if ($type == 'excel') $this->type = 'xlsx|xls|xlsm';
    	$filename    = $_FILES[$name]['name'];
        $up          = $this->com('upload');
        $up->maxSize = $this->maxSize;
        $up->path    = './'.StaticDir.$this->path.'/excel/';
        $up->upType  = $this->type;
        $up->upload($_FILES[$name]);
		
        if ( empty($up->msg) ){
            $up->fileurl = StaticDir.$this->path.'/excel/'.$up->upFile;
        }else{
            $up->fileurl = '';
        }        
        return $up;
    }
}
?>