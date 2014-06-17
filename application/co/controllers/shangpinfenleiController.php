<?php
/*********************************
 * 模块：    共通模块(CO)
 * 机能：   商品分类门选择画面
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class co_shangpinfenleiController extends co_controllers_baseController {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	
	}
	
	/**
	 * 商品分类树形列表xml
	 *
	 */
	public function getlistdataAction()
	{
		$shangpinfenlei_model = new co_models_shangpinfenlei ( );
		header("Content-type:text/xml");
		echo $shangpinfenlei_model->getXmlData();
		
	}
	


}