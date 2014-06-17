<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：  配送订单生成(psdwh)
 * 作成者：梁兆新
 * 作成日：2011/1/20
 * 更新履历：
 *********************************/
class ps_psdwhController extends ps_controllers_baseController {
	/*
     * 配送单维护信息列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '配送单维护' );
		$this->_view->assign ( "kprq", date("Y-m-d"));  //获取当前时间
		$this->_view->display ( 'psdwh_01.php' );
	}
	public  function psdhzqrAction(){
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		$this->_view->assign ( 'title', '配送单回执确认' );
		$this->_view->assign ( "kprq", date("Y-m-d"));  //获取当前时间
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			$this->_view->assign ( 'lsit1', $model->getpseditlist($filter));
		}
		$this->_view->display ( 'psdwh_04.php' );
	}
	/*
     * 得到回执确认的 出库单列表  信息
     */
	function gethzqrlistAction(){
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			header ( "Content-type:text/xml" ); //返回数据格式xml
			
			echo $model->gethzqrlist($filter);
		}
		exit();
	}
	/*
     * 配送单维护信息列表画面显示
     */
	public function psdeditAction() {
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		$this->_view->assign ( 'title', '配送单修改' );
		$this->_view->assign ( "kprq", date("Y-m-d"));  //获取当前时间
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			$this->_view->assign ( 'lsit1', $model->getpseditlist($filter));
		}
		$this->_view->display ( 'psdwh_03.php' );
		
	}
	
	/*
	 * 获取配送单信息列表
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 10 ); //默认显示数量
		$filter ['serchstime'] = $this->_getParam ( "serchstime" );//开始时间
		$filter ['serchetime'] = $this->_getParam ( "serchetime" );//终止时间
		$filter ['serchchphm'] = $this->_getParam ( "serchchphm" );//车牌号
		$filter ['serchchyrmch'] = $this->_getParam ( "serchchyrmch" );//承运人
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new ps_models_psdwh ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
    /*
	 * 删除信息
	 */
	public function delAction() {
		$filter ['psdh'] = $this->_getParam ( "psdh", 0 ); //排序方式		
		$model = new ps_models_psdwh ( );
		echo $model->del ( $filter );
	}
	
	/*
     * 配送单维护信息列表画面显示
     */
	public function psdxqAction() {			
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			$this->_view->assign ( 'lsit1', $model->getpsxxlist1($filter));
		}
		
		$this->_view->display ( 'psdwh_02.php' );
	}	
	
	/*
     * 配送单维护信息列表画面显示
     */
	public function getxxlist2Action() {			
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			header ( "Content-type:text/xml" ); //返回数据格式xml
			echo $model->getxxlist2($filter);
		}
		exit();
	}	
	/*
     * 配送单维护-修改功能 -出库单列表详细
     */
	function getpseditlist2Action(){
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			header ( "Content-type:text/xml" ); //返回数据格式xml
			echo $model->getpseditlist2($filter);
		}
		exit();
	}
		/*
     * 配送单维护-修改功能 -货物清单 列表详细
     */
	function getpseditlist3Action(){
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			header ( "Content-type:text/xml" ); //返回数据格式xml
			echo $model->getpseditlist3($filter);
		}
		exit();
	}
	/*
     * 配送单维护信息列表画面显示
     */
	public function getxxlist3Action() {			
		$filter ['psdh'] = $this->_getParam ( 'psdh', 0 ); //获得配送单号
		if(!empty($filter ['psdh'])){
			$model = new ps_models_psdwh ( );
			header ( "Content-type:text/xml" ); //返回数据格式xml
			echo $model->getxxlist3($filter);
		}
		exit();
	}	
	/*
     * 保存修改
     */
	public function saveeditAction(){
		$result['status'] = '0'; 
		try {

			$model = new ps_models_psdwh();
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $model->beginTransaction ();
			    //配送单编号取得
			    $psdscbh =$_POST['PSDH'];
			    //删除订单的原始数据
			    $model->delold_date($psdscbh);
			    //配送订单保存
			   $model->savePshdMain($psdscbh);
			    //配送单明细保存
			   $model->savePsMingX($psdscbh);
			   //配送单商品明细保存
			   $model->savePsspMingX($psdscbh);
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $psdscbh;
				    $model->commit ();
				    Common_Logger::logToDb("修改配送单  单据号：".$result['data']);
			    }else{
				    $model->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
     		throw $e;
		}	
	}
	
	/*
     *回执确认
     */
	public function savehzhqrAction(){
		$result['status'] = '0'; 
		try {

			$model = new ps_models_psdwh();
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $model->beginTransaction ();
			    //配送单编号取得
			    $psdscbh =$_POST['PSDH'];
			    //更新配送单
			   $model->updaehzqr_psd($psdscbh);
//			    //配送单明细更新
			   $model->updaehzqr_psdmx($psdscbh);
//			   //更新销售单
			   $model->updaehzqr_saleorder($psdscbh);
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $psdscbh;
				    $model->commit ();
				    Common_Logger::logToDb("配送订单已经确认   单据号：".$result['data']);
			    }else{
				    $model->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
     		throw $e;
		}
		
	}
	
}
?>
