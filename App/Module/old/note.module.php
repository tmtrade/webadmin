<?php 
class NoteModule extends AppModule
{
	
	/**
	* 引用业务模型
	*/
	public $models = array(
		'note' => 'Note',
	);
	
	/**
	 * 发送站内信
	 * @author	garrett
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	array	$param	参数
	 * @return	array
	 */
	public function setNote( $param )
	{
		$data = array(
			'content'    => $param['content'],
			'createTime' => time(),
			'updateTime' => time(),
			'sendMan'    => isset($param['sendMan']) ? $param['sendMan'] : 99 ,
			'acceptMan'  => $param['acceptMan'],
			'status'     => 1,
		);
		return $this->import('note')->add($data);
	}
	
	/**
	 * 修改站内信状态
	 * @author	garrett
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	array	$status	状态ID 
	 * @param   int     $Id     修改ID
	 * @return	array
	 */
	public function upNote( $status  , $id , $userId)
	{
		$data = array(
			'status' => $status,
		);
		return $this->import('note')->update($data , $id , $userId);
	}
	
	/**
	 * 获取站内信
	 * @author	garrett
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int	$id	接收人
	 * @return	array
	 */
	public function getNote( $id , $rowNum ,$p)
	{
		return $this->import('note')->getIdRows( $id ,$rowNum , $p);
	}
	
	/**
	 * 检索站内信
	 * @author	garrett
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int	$id	接收人
	 * @param   string $content 检索内容
	 * @return	array
	 */
	public function search(  $id , $content , $rowNum , $p)
	{
		$r['eq']    = array('acceptMan' => $id );
		$r['like']  = array('content' => $content);
		$r['raw']   = "status != 3 ";
		$r['limit'] = $rowNum;
		$r['page']  = $p;
		$r['order'] = array("createTime"=>"desc");
		
		return $this->import('note')->getSearch($r);	
	}
	
	/**
	 * 检索站内信
	 * @author	garrett
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int	$id	接收人
	 * @param   string $content 检索内容
	 * @return	array
	 */
	public function getConut( $userId )
	{
		$r['eq']    = array('status' => 1 ,'acceptMan' => $userId);
		
		return $this->import('note')->getcounts($r);
	}
}
?>