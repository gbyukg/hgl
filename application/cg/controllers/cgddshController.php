<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购订单审核(CGDDSH)
 * 作成者：姚磊
 * 作成日：2011/1/20
 * 更新履历：
 *********************************/
class cg_cgddshController extends cg_controllers_baseController {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_BZHSHL = 6; // 包装数量
	private $idx_LSSHL = 7; // 零散数量
	private $idx_SHULIANG = 8; // 数量
	private $idx_CHANDI = 9; // 产地
	private $idx_BEIZHU =10; // 备注	
	
	
	/*
	 * 采购开票维护初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "title", "采购管理-采购计划审核" ); //标题
		$this->_view->display ( "cgddsh_01.php" );
	}	
	
	/*
	 * 采购开票维护详情页面初始化
	 */
	public function xiangqingAction(){
		$model = new cg_models_cgddsh();		
		$cgkpdbh = $this->_getParam ( "cgkpdbh" );		
		$rec = $model->getCgdxx ( $cgkpdbh, '', 'current' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //排序列
		
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //排序方式
		
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign("title","采购管理-采购计划详情");
		$this->_view->display ( "cgddsh_02.php" );
	}
	
	/*
	 * 采购开票维护详情明细
	 */
	function xiangqingmingxiAction(){
		$model = new cg_models_cgddsh();	
		$cgkpdbh = $this->_getParam ( "cgkpdbh" );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->xiangxiMingxi($cgkpdbh );
	}	
    /**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$cgkpModel = new cg_models_cgddsh();
		
	    echo json_encode($cgkpModel->getShangpinInfo($filter));
	}
 	
 	/*
 	 *  获取采购开票维护单据信息列表
 	 */
 	public function getlistdataAction(){
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new cg_models_cgddsh();
		
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ($filter);

 	}
 	
 	/*
 	 *  获取采购开票维护明细信息列表
 	 */
 	
 	public function getmingxilistdataAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$cggzhdbh = $this->_getParam ( "flg" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new cg_models_cgddwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getMingxiGridData ($cggzhdbh,$filter );
 	}
 	
 	/*
 	 *  采购单数据入账信息页面赋值
 	 */
 	public function  getdanjuhaoinfoAction(){
 		
 		$flg = $this->_getParam("flg");
 		$model = new cg_models_cgddsh();
 		echo Common_Tool::json_encode($model->getcgGridData($flg));		
 				
 	}
 	
 	/*
 	 * 删除采购入账单据信息
 	 */
 	public function deleteruzhangAction(){
 		$model = new cg_models_cgddsh();
 		$flg = $this->_getParam("flg");		//获取页面的采购单号
 		$model->upCgdata($flg);
 			
 	}
 	
	 /**
     * 	更新采购订单信息表					
     *  审核通过
     *
     */
	public function upcgddxxAction(){
		$result['status'] = '0'; 		
    	$cgddbh = $this->_getParam("flg");		//获取页面的采购订单单据号
    	$result['data']=$cgddbh;
    	$shyj = $this->_getParam("shyj");			//审核意见
 		$cgddxxModel = new cg_models_cgddsh() ;		
	 	$cgddxxModel->upcgdd($cgddbh,$shyj);
	 	echo json_encode($result);
	}
 	
	 /**
     * 	更新采购订单信息表					
     *  审核不通过
     *
     */
	public function upcgddxxbtgAction(){
		$result['status'] = '1'; 
    	$cgddbh = $this->_getParam("flg");		//获取页面的采购订单单据号
    	$result['data']=$cgddbh;
    	$shyj = $this->_getParam("shyj");			//审核意见
 		$cgddxxModel = new cg_models_cgddsh() ;		
	 	$cgddxxModel->upcgddbtg($cgddbh,$shyj);
	 	echo json_encode($result);
	}
	
 	/*
 	 * 上一条 下一条
 	 */
 	public function getcgdxxAction(){
 		
 			//检索条件
		$cgdbh = $this->_getParam ( "cgdbh" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列

		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$flg = $this->_getParam ( 'flg', 'current' ); //检索方向
		
 		$model = new cg_models_cgddsh( );
		$rec = $model->getCgdxx ( $cgdbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "cgddsh_02.php" ) );
		}
 	}
}