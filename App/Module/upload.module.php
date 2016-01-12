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
	 * @return void
	 *
	 * @author    Alexey
	 * @since     2013年11月1日17:23:43
	 * @copyright CHOFN
	 */
    public function upload($name)
    {
    	$filename    = $_FILES[$name]['name'];
        $up          = $this->com('upload');
        $up->maxSize = $this->maxSize;
        $up->path    = './'.StaticDir.$this->path;
        $up->upType  = 'jpeg|jpg|gif|png|bmp';
        $up->upload($_FILES[$name]);
        $name = $up->msg ? '' : StaticDir.$this->path.'/'.$up->upFile;
        return $name;
    }

	public function uploadList($name)
    {
    	$filename    = $_FILES[$name]['name'];
        $up          = $this->com('upload');
        $up->maxSize = $this->maxSize;
        $up->path    = './'.StaticDir.$this->path;
        $up->upType  = 'jpeg|jpg|gif|png|bmp';
        $img		 = $up->uploadMore($_FILES[$name]);
        return $img;
    }

}
?>