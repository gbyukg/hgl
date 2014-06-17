<?php
/*********************************
 * 模块：    共通模块(CO)
 * 机能：    库位选择画面
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class co_kuweiController extends co_controllers_baseController {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	
	}
	
	/*
	 * 库位选择弹出画面
	 */
	public function listAction()
	{
		$_SESSION["shpbh"] =  $this->_getParam("shpbh",'');//商品编号
		$this->_view->display ( "co_kuwei_01.php" );
		
	}
	
	/*
	 * 库位选择弹出画面数据取得
	 */
	public function getlistdataAction()
	{
    	$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",100);
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['searchkey'] = $this->_getParam("searchkey",'');
		$filter ['shpbh'] = $_SESSION["shpbh"];
		$filter ['orderby'] = $this->_getParam("orderby",3);//批号
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
			
		$kuwei_model = new co_models_kuwei();
		
		header("Content-type:text/xml");
		echo $kuwei_model->getXmlData($filter);
		
	}
	
	public function getkucundataAction()
	{
    	
		$shpbh = $this->_getParam("shpbh","");
		$shfshkw = $this->_getParam("shfshkw","");
			
		$kuwei_model = new co_models_kuwei();

		echo json_encode($kuwei_model->getKucunData($shpbh,$shfshkw));
		
	}
	
    
	

}