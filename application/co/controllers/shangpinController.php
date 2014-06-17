<?php
/*********************************
 * 模块：    共通模块(CO)
 * 机能：    商品选择画面
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class co_shangpinController extends co_controllers_baseController {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	
	}
	
	/*
	 * 商品选择弹出画面
	 */
	public function listAction()
	{
		$searchkey = $this->_getParam("searchkey",'');
		$this->_view->assign("searchkey",$searchkey);
		$this->_view->display ( "co_shangpin_01.php" );
		
	}
	
	/*
	 * 商品选择弹出画面数据取得
	 */
	public function getlistdataAction()
	{
    	$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['searchkey'] = $this->_getParam("searchkey",'');
		$filter ['flbm'] = $this->_getParam("flbm",'');
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
			
		$shangpin_model = new co_models_shangpin();
		header("Content-type:text/xml");
		echo $shangpin_model->getXmlData($filter);

	}
	
    /**
     * 通过商品编号取得商品信息
     *
     */
	public function getsingledataAction()
	{
    	$searchkey = $this->_getParam("searchkey","");
		$shangpin_model = new co_models_shangpin();
		
	    echo json_encode($shangpin_model->getSingleData($searchkey));
	
	}

}