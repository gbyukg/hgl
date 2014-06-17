<?php
/*********************************
 * 模块：   采购模块(CG)
 * 机能：   返利协议商品(FLXYS)
 * 作成者：侯殊佳 
 * 作成日：2011/05/29
 * 更新履历：

 *********************************/
class cg_flxysController extends cg_controllers_baseController
{
	/*
	 * index页
	 */
	public function indexAction()
	{
		$this->_view->assign ( 'action', 'new' );
		$this->_view->assign ( "kprq", date("Y-m-d"));  
		$this->_view->assign("title","采购管理-返利协议登录");			//标题
		$model = new cg_models_flxys ();
		$ywybm = $model->getywybm ();
		$this->_view->assign ( "ywybm", $ywybm);
		$this->_view->display("flxys_01.php"); 
		
	}
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$flxysModel = new cg_models_flxys ( );
		
	    echo Common_Tool::json_encode($flxysModel->getDanweiInfo($filter));
	}
	
   /**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$flxysModel = new cg_models_flxys();
		
	    echo json_encode($flxysModel->getShangpinInfo($filter));
	}

	
	/*
	 * 保存订单生成数据
	 */
	function savecgAction(){
		
		
		try{
			$flxyModel = new cg_models_flxys();
			$xybh = $_POST['XSHDH']; 
			
			
		    	$flxyModel->beginTransaction ();
		    	//新建 
		    	if ($_POST ['action'] == 'new') {
			    //返利订单编号取得
			    $flxybh = Common_Tool::getDanhao('CFX',$_POST['KPRQ']); //采购单据号
			    //返利订单生成保存
			    $flxyModel->saveFlxyMain($flxybh);
			    //返利订单商品明细保存
			    $flxyModel->saveFlxyMingxi($flxybh);
			    
			    $result['status'] = '1';
			    
				Common_Logger::logToDb ("返利协议生成 单据号：".$flxybh);
				$result['data'] = $flxybh;
				}
				else{
					//修改
					//$recs = $flxyModel->getMingxiData($xybh);
					$xybh = $_POST['XSHDH'];
					$flxyModel->updateFlxyMain($xybh);
					$flxyModel->updateFlxyMingxi($xybh);
					$result['status'] = '0';
					$result['data'] = $xybh;
					
				}
				$flxyModel->commit();
			
			//返回处理结果
			echo json_encode ( $result );
		}catch( Exception $e){
		//回滚
			$flxyModel->rollBack ();
     		throw $e;
		}
		}
}
?>