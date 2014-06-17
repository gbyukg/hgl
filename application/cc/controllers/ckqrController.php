<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    出库确认(CKQR)
 * 作成者：刘枞
 * 作成日：2011/07/21
 * 更新履历：
 *********************************/
class cc_ckqrController extends cc_controllers_baseController {
	
	/*
	 * 出库确认初始页面
	 */
	public function indexAction() {

		$this->_view->display ( "ckqr_01.php" );
	}
	
	
	/*
	 * GRID列表xml数据取得
	 */
	public function getdataAction() {
		//取得列表参数
		$dytm = $this->_getParam ( "dytm", '' ); 	         //商品编号
		
		$model = new cc_models_ckqr();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridData( $dytm );
	}
	
	
	/*
	 * 出库确认操作
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$Model = new cc_models_ckqr();

			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';             //必须输入项验证错误
			}else{
			    $Model->beginTransaction ();			   //开始一个事务
			    
			    $Model->updateZht();        //确认操作
				$returnValue = $Model->selectZht();        //判断销售单下的所有明细的状态
			    if($returnValue['status'] == '1'){
			       //$result['status'] = '2';                //出库单明细没完全出库
			       //$result['data'] = $returnValue['data']; //出库单号
			    }
			    
			    if($result['status'] == '0'){	     //保存成功
				    $Model->commit();
			    }else{
				    $Model->rollBack();     //有错误发生,事务回滚
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ){
			$Model->rollBack();		       //回滚
     		throw $e;
		}
	}
	
}