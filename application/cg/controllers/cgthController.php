<?php
/*********************************
 * 模块：    采购模块(CG)
 * 机能：    采购退货(CGTH)
 * 作成者：刘枞
 * 作成日：2011/01/11
 * 更新履历：
 *********************************/
class cg_cgthController extends cg_controllers_baseController {
	/*
	 * 采购退货出库初始页面
	 */
	public function indexAction(){
		$this->_view->assign ( "kprq", date("Y-m-d"));         //开票日期
		$this->_view->assign ( "title", "采购管理-采购退货" ); //标题
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->display ( "cgth_01.php" );
	}
	
	
	/*
	 * 入库单选择页面
	 */
	public function thdlistAction(){
		$this->_view->assign ( "title", "入库单选择" ); 		//标题
		$this->_view->display ( "cgth_02.php" );
	}
	
	
	/*
	 * 入库单列表xml数据取得(入库单选择页面)
	 */
	public function getthdlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	     //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' );      //单位名称
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cg_cgth_searchParams'] = $_POST;
				unset($_SESSION['cg_cgth_filterParams']);       //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cg_cgth_filterParams'] = $_POST;
				unset($_SESSION['cg_cgth_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cg_cgth_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cg_cgth_searchParams'];           //固定查询条件
		
		$model = new cg_models_cgth();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 明细列表xml数据取得(入库单选择页面)
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgth();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/**
     * 取得单位信息
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cg_models_cgth();		
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
	
	/**
     * 取得退货信息
     */
//	public function getkthslAction(){
//    	$filter ['shpbh'] = $this->_getParam('shpbh');   //单位编号
//    	$filter ['rkdbh'] = $this->_getParam('rkdbh');   //单位编号
//    	$filter ['pihao'] = $this->_getParam('pihao');   //单位编号
//    	$filter ['scrq'] = $this->_getParam('scrq');     //单位编号
// 		$model = new cg_models_cgth();		
//	    echo Common_Tool::json_encode($model->getkthsl($filter));
//	}
	
	
	/*
	 * 采购退货保存操作
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$Model = new cg_models_cgth();
			
			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';             //必须输入项验证错误
			}elseif(!$Model->logicCheck($_POST)){
				$result['status'] = '2';             //项目合法性验证错误
			}else{
			    $Model->beginTransaction ();		 //开始一个事务
			    $bh = Common_Tool::getDanhao('CGT',$_POST['KPRQ']);	   //出库单编号取得
		    	$Model->updateKC();	                 //变更在库商品可销及冻结信息
		    	$Model->saveMain($bh);		         //退货单信息保存
		    	$Model->saveMingxi($bh);	         //退货单明细保存
			    if($result['status'] == '0'){	     //保存成功
			    	$result['bh'] = $bh;
				    $Model->commit();
			    }else{
				    $Model->rollBack();     //有错误发生,事务回滚
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ){
			$Model->rollBack();		        //回滚
     		throw $e;
		}
	}
	
	
	/**
     * 取得入库单信息
     *
     */
	public function getinfoAction(){
    	$filter ['bh'] = $this->_getParam('bh');   //单位编号
 		$Model = new cg_models_cgth();
	    echo Common_Tool::json_encode($Model->getInfo($filter));
	}
	
	
	/**
     * 取得入库单明细信息
     *
     */
	public function getmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');   //单位编号
 		$Model = new cg_models_cgth();
	    echo Common_Tool::json_encode($Model->getmingxi($filter));
	}
	
	
	/*
	 * 入库单商品明细选择页面
	 */
	public function thmxlistAction() {
		$this->_view->assign("bh",$this->_getParam("bh"));	    //单据编号
		$this->_view->assign ( "title", "入库单商品明细" ); 		//标题
		$this->_view->display ( "cgth_03.php" );
	}
	
	
	/*
	 * 退货单明细列表xml数据取得(商品明细选择页面)
	 */
	public function getrkdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['rkdbh'] = $this->_getParam ( "rkdbh", '' ); 	    //单据编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgth_rkdmxlist_searchParams'] = $_POST;
				unset($_SESSION['cgth_rkdmxlist_filterParams']);       //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgth_rkdmxlist_filterParams'] = $_POST;
				unset($_SESSION['cgth_rkdmxlist_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cgth_rkdmxlist_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgth_rkdmxlist_searchParams'];                 //固定查询条件
		
		$model = new cg_models_cgth();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getRKDMingxiData( $filter );
	}
	
	
	/*
	 * 入库单编号自动填充
	 */
	public function rkdbhautocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');   //检索项目值
        $model = new cg_models_cgth();
	    $result = $model->rkdbhAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
}