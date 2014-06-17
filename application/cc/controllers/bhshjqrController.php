<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    补货上架确认(BHSHJQR)
 * 作成者：刘枞
 * 作成日：2011/07/19
 * 更新履历：
 *********************************/
class cc_bhshjqrController extends cc_controllers_baseController {
	
	/*
	 * 补货上架确认初始页面
	 */
	public function indexAction() {
		$Model = new cc_models_bhshjqr();
		$this->_view->assign ( "title", "仓储管理-补货上架确认" );     //标题
		$this->_view->assign ( "chsdchk", $Model->getCHSDCHK() );     //取得发货区数据，并传到画面
		$this->_view->display ( "bhshjqr_01.php" );
	}
	
	
	/*
	 * 确认操作
	 */
	public function querenAction() {
		
		$filter ['bhdbh'] = $this->_getParam ( "bhdbh", '' ); 	    //商品编号
		$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); 	    //商品编号
		$filter ['pihao'] = $this->_getParam ( "pihao", '' ); 	    //商品批号
		$filter ['yrck'] = $this->_getParam ( "yrck", '' ); 	    //成本计算方式
		$filter ['yrkw'] = $this->_getParam ( "yrkw", '' ); 	    //成本计算方式
		
		$model = new cc_models_bhshjqr();
		$result = $model->queren($filter);
		
		echo Common_Tool::json_encode($result);	
	}
	
	
	/*
	 * 毛利查询GRID列表xml数据取得
	 */
	public function getdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); 	         //商品编号
		$filter ['chsdchk'] = $this->_getParam ( "chsdchk", '' );        //传送带出口
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		$model = new cc_models_bhshjqr();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
}