<?php
/*********************************
 * 模块：    共通模块(CO)
 * 机能：    单位选择画面
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class co_danweiController extends co_controllers_baseController {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	
	}
	
	/*
	 * 单位选择弹出画面
	 */
	public function listAction()
	{
		$searchkey = $this->_getParam("searchkey",'');
		$this->_view->assign("searchkey",$searchkey);
		$this->_view->display ( "co_danwei_01.php" );
		
	}
	
	/*
	 * 单位选择弹出画面数据取得
	 */
	public function getlistdataAction()
	{
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",30);
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['searchkey'] = $this->_getParam("searchkey",'');
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
			
		$danwei_model = new co_models_danwei();
		
		header("Content-type:text/xml");
		echo $danwei_model->getXmlData($filter);

	}
	
    /**
     * 取得单条单位信息
     *
     */
	public function getsingledataAction()
	{
    	$searchkey = $this->_getParam("searchkey","");
		$danwei_model = new co_models_danwei();
		
	    echo json_encode($danwei_model->getSingleData($searchkey));
	
	}
}