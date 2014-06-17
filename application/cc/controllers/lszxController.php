<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  零散装箱(lszx)
 * 作成者：    姚磊
 * 作成日：    2011/03/29
 * 更新履历：
 **********************************************************/
class cc_lszxController extends cc_controllers_baseController {
		private $idx_ROWNUM =0;     // 行号
		private $idx_SHPBH =1;  // 商品编号
		private $idx_SHPMCH=2;      // 商品名称
		private $idx_SHULIANG=3;     // 数量
		private $idx_DWMCH=4;      // 单位名称
		private $idx_PIHAO=5 ;		//批号
		private $idx_FJBF=6;       // 复检不符
		private $idx_CKBH=7;      // 仓库编号
		private $idx_KQBH=8;     // 库区编号
		private $idx_KWBH=9;	//库位编号
		private $idx_BGZH = 10;	  // 变更者
	/*
	 * 零散装箱初始化页
	 */
	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-零散装箱" ); //标题
		$this->_view->assign ( "userid", $_SESSION ['auth']->userId ); //登陆者
		$model = new cc_models_lszx();	 	
	 	$rec = $model->getChsd($_SESSION ['auth']->userId);//获取传送带出口
	 	$this->_view->assign ( "rec", $rec );
		$this->_view->display ( "lszx_01.php" );
	}

	/*
	 * 查询零散装箱信息 返回xml格式
	 */
	public function getdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['zhzhxh'] = $this->_getParam ( "zhzhxh", '' ); 	     //周转分箱号
		$filter ['djbh'] = $this->_getParam ( "djbh", '' ); 	     	 //单据编号
		$filter ['chsdchk'] = $this->_getParam ( "chsdchk", '' ); 	     //传送带出口
		$filter ['ckbh'] = $this->_getParam ( "ckbh", '' ); 	     	 //仓库编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_lszx();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
		
	/*
	 * 判断对应暂存区是否存在
	 * 
	 */
	public function getzhzhxhAction(){
		
		$model = new cc_models_lszx();
		$rec = $model->getzhzhxh( $this->_getParam('zhzhxh'));	
		if ( $rec == FALSE) {
			echo json_encode('0'); //
		} else {
			echo json_encode($rec); //
		}
	}
	
	/**
	 *  打包处理 获取对应条码和周转箱号对应的零散拣货信息状态
	 */
	
	 public function getzhtaiAction(){
	 	try {
	 	$model = new cc_models_lszx();
	 	$model->beginTransaction ();
		$rec = $model->getzhtai( $this->_getParam('zhzhxh'),$this->_getParam('djbh'));
		if($rec['ZHUANGTAI']== 0 || $rec['ZHUANGTAI']== 1){
			$model->updatezhuant($this->_getParam('zhzhxh'),$this->_getParam('djbh'),$this->_getParam ( "ckbh" ),$this->_getParam ( "chsdchk" )); //设置对应状态为 已装箱
					
			$num = $model->getnum($this->_getParam('zhzhxh'),$this->_getParam('djbh'),$this->_getParam ( "ckbh" ));
			if($num == 0){
			$model->updatezhatai($this->_getParam('zhzhxh'),$this->_getParam('djbh'));
			}
			$model->commit ();
			echo json_encode('1');//状态是已装箱
		}else{
			echo json_encode('0');//状态不能装箱
			$model->rollBack ();//有错误发生
		}
	 	
	 	
	 	
	 	
	 	}catch (Exception $e){
	 		$model->rollBack ();
     		throw $e;
	 	}
	 	
	 } 
	 /*
	  * 更改拣货状态
	  */
	public function upztvalAction(){
		$model = new cc_models_lszx();
	 	$model->upjhval($this->_getParam('zhzhxh'),$this->_getParam('djbh'));//拣货状态
	}
	
	 /*
	  * 更改复检状态
	  */
	
	 public function upfjvalAction(){
	 	$model = new cc_models_lszx();	 	
	 	$model->upfjval($_POST['ZHZHXH'],$_POST['DJBH']);//复检状态
	 }
	
	
	
}
