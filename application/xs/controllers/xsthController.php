<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售退货(XSTH)
 * 作成者：孙宏志
 * 作成日：2011/01/06
 * 更新履历：
 *********************************/
class xs_xsthController extends xs_controllers_baseController {
	private $idx_SHPBH = 2; 	//商品编号
	private $idx_PIHAO = 6; 	//批号
	private $idx_SHCHRQ = 7; 	//生产日期	
	/*
	 * 销售开票初始页面
	 */
	public function indexAction() {
    	$Model = new xs_models_xsth();
    	$kpy = $Model->getKYPInfo();
		$this->_view->assign ( "kpymch", $kpy['YGXM'] ); //员工姓名
		$this->_view->assign ( "kpyid", $kpy['YGBH'] ); //员工编号
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "title", "销售管理-销售退货" ); //标题
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门名称
		$this->_view->display ( "xsth_01.php" );
	}
	
	/*
	 * 销售单明细选择页面
	 */
	public function xsmxlistAction() {
		$this->_view->assign ( "title", "销售管理-销售明细列表" ); 			//标题
		$this->_view->display ( "xsth_02.php" );
	}
	
	/**
     * 取得销售订单信息
     */
	public function getxsdinfoAction(){
    	$filter ['bh'] = $this->_getParam('bh');
 		$Model = new xs_models_xsth();
	    echo Common_Tool::json_encode($Model->getxsdInfo($filter));
	}
	
	/**
     * 取得销售单明细信息
     */
	public function getxsdmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');
 		$Model = new xs_models_xsth();
 		echo $Model->getxsdmingxi($filter);

	    //echo Common_Tool::json_encode($Model->getxsdmingxi($filter));
	}
	
	/**
     * 取得退货单信息
     */
	public function getthdinfoAction(){
    	$filter ['xshdbh'] = $this->_getParam('xshdbh');
    	$filter ['shpbh'] = $this->_getParam('shpbh');
    	$filter ['pihao'] = $this->_getParam('pihao');
    	$filter ['shchrq'] = $this->_getParam('shchrq');    	
 		$Model = new xs_models_xsth();
	    echo Common_Tool::json_encode($Model->getthdInfo($filter));
	}
	
	/*
	 * 退货单保存操作
	 */
	public function saveAction() {
		$result['status'] = '0';
		try {
			$Model = new xs_models_xsth();
			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';        //必须输入项验证错误
			}else{
			    $Model->beginTransaction();	    //开始一个事务
			    //退货单编号取得
			    $thdbh = Common_Tool::getDanhao('XST',$_POST['KPRQ']);	 

			    	$Model->saveThdMain($thdbh);	 //退货单信息保存
			    	$Model->saveThdMingxi($thdbh);	 //退货单明细保存
			    	//$Model->updatexsdzht();          //更新DB:销售订单信息（H01DB012201）的出库状态为已出库
				if($_POST['THLX'] == '2'){
					$Model->savePcthcl($thdbh);			//赔偿退货时，赔偿退货处理
				}
			    if($result['status'] == '0'){	         //保存成功
			    	$result['thdbh'] = $thdbh;
				    Common_Logger::logToDb("销售退货，退货单编号：".$result['thdbh']);
				    $Model->commit();
			    }else{
				    $Model->rollBack();     //有错误发生,事务回滚
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ){
			$Model->rollBack ();		 //事务回滚
     		throw $e;
		}
	}
}