<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    出库确认新(CKQRX)
 * 作成者：刘枞
 * 作成日：2011/09/21
 * 更新履历：
 *********************************/
class cc_ckqrxController extends cc_controllers_baseController {
	
	/*
	 * 出库确认初始页面
	 */
	public function indexAction() {
		$this->_view->display ( "ckqrx_01.php" );
	}
	
	
	/*
	 * 根据画面中的对应条码获取相应的单据编号
	 */
	public function getdjbhAction(){
		//取得列表参数
		$dytm = $this->_getParam ( "dytm", '' ); 	  //单据编号
		
		$model = new cc_models_ckqrx();
		
		header ( "Content-type:text/xml" );           //返回数据格式xml
		
		echo Common_Tool::json_encode( $model->getDJBH( $dytm ) );
		
	}
	
	
	/*
	 * GRID列表xml数据取得
	 */
	public function getdataAction() {
		//取得列表参数
		$djbh = $this->_getParam ( "djbh", '' ); 	  //单据编号
		
		$model = new cc_models_ckqrx();
		
		header ( "Content-type:text/xml" );           //返回数据格式xml
		
		echo $model->getGridData( $djbh );
	}
	
	
	/*
	 * 确认操作
	 */
	public function querenAction() {
		
		$filter ['dytm'] = $this->_getParam ( "dytm", '' ); 	            //对应条码
		$filter ['xianghao'] = $this->_getParam ( "xianghao", '' ); 	    //箱号
		$filter ['djbh'] = $this->_getParam ( "djbh", '' ); 	            //单据编号
		
		try {
			$Model = new cc_models_ckqrx();
			
		    $Model->beginTransaction ();		//开始一个事务
		    
			$Model->updateShl($filter);        	//更新出库单已出库确认商品的数量
			$Model->updateZht($filter);        	//确认修改当前箱状态
			$Model->selectZht();                //判断出库单下的所有明细的出库状态
			
			$Model->commit();

		} catch ( Exception $e ){
			$Model->rollBack();		       //回滚
     		throw $e;
		}
	}

}