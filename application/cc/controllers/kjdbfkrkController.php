<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    库间调拨返库入库(kjdbfkrk)
 * 作成者：苏迅
 * 作成日：2011/01/26
 * 更新履历：
 *********************************/
class cc_kjdbfkrkController extends cc_controllers_baseController {
	
	/*
	 *  库间调拨返库入库页面
	 */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh); 	  //部门编号
		$this->_view->assign ( "djbh", $this->_getParam ("djbh","请双击选择"));  			//
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "title", "仓储管理-库间调拨返库入库" ); 	//标题
		$this->_view->display ( "kjdbfkrk_01.php" );
	}	
	
	/*
	 * 返库单选择页面
	 */
	public function fkdlistAction() {
		$this->_view->assign ( "title", "仓储管理-调拨返库单选择" ); 				//标题
		$this->_view->display ( "kjdbfkrk_02.php" );
	}
	
	public function autocompleteAction(){
    	$filter ['searchkey'] = $this->_getParam('q'); //查找字符串  
        $cangku_model = new cc_models_kjdbfkrk ( );
	    $result = $cangku_model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	/*
	 * 返库单列表xml数据取得(返库单选择页面)
	 */
	public function getfkdlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
/*		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	//开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	//终止日期
		$filter ['dcckbhkey'] = $this->_getParam ( "dcckbhkey", '' ); 	//调出仓库编号
		$filter ['drckbhkey'] = $this->_getParam ( "drckbhkey", '' ); //调入仓库编号
		$filter ['fkdjh'] = $this->_getParam ( "fkdjh", '' ); 	//返库单据号
		$filter ['dbckd'] = $this->_getParam ( "dbckd", '' ); //对应调拨出库单号*/
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'DESC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_kjdbfkrk_searchParams'] = $_POST;
				unset($_SESSION['kjdbfkrk_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kjdbfkrk_filterParams'] = $_POST;
				unset($_SESSION['cc_kjdbfkrk_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['kjdbfkrk_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_kjdbfkrk_searchParams'];  //固定查询条件
		
		$model = new cc_models_kjdbfkrk ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData ( $filter );
	}
	
	/*
	 * 返库单明细列表xml数据取得(返库单选择页面)
	 */
	public function getfkdmxlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['fkdbh'] = $this->_getParam ( "fkdbh", '' ); 
		//$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		//$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_kjdbfkrk ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridMingxiData ( $filter );
	}
	
	/**
     * 取得退货单信息,退货单双击选择后触发(返库入库页面)
     *
     */
	public function getfkdinfoAction() {
		$model = new cc_models_kjdbfkrk ();
		echo Common_Tool::json_encode($model->getSingleFkdInfo($this->_getParam ( "fkdbh", '' )));
		
	}
		
	/*
	 * 退货单明细列表数据取得(返库入库页面)
	 */
	
	public function getfkdmingxiAction() {
		$model = new cc_models_kjdbfkrk ();
		echo Common_Tool::json_encode($model->getfkdmingxi($this->_getParam ( "fkdbh", '' )));
		
	}
	
	/*
	 * 库位选择页面
	 */
	public function kuweiAction() {
		$model = new cc_models_kjdbfkrk ( );
		$ckbh = $this->_getParam("ckbh");   //仓库编号
		$rec = $model->getCkxx($ckbh);
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( "title", "仓储管理-库位选择" ); 	//标题	
		$this->_view->display ( "kjdbfkrk_03.php" );
	}
	
	/*
	 * 库位选择弹出画面数据取得
	 */
	public function getkuweidataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
		//业务相关参数
		$filter ['ckbh'] = $this->_getParam("ckbh","");
			
	    $kuwei_model = new cc_models_kjdbfkrk();
		header("Content-type:text/xml");
		echo $kuwei_model->getListData($filter);
	}
	
	/*
	 * 返库单明细选择页面
	 */
	public function fkdmxlistAction() {
		$this->_view->assign("fkdbh",$this->_getParam("fkdbh"));	//退货单编号
		$this->_view->assign ( "title", "仓储管理-调拨返库明细列表" ); 			//标题
		$this->_view->display ( "kjdbfkrk_04.php" );
	}
	
	/*
	 * check返库单状态
	 */
	public function checkfkzhtAction() {
		$model = new cc_models_kjdbfkrk ();
		$result = $model->getfkzht($this->_getParam ( "fkdbh"));
		echo Common_Tool::json_encode($result);		
	}
	
	/*
	 * 取得库位信息(check仓库/库区/库位状态是否为已删除或冻结)
	 */
	public function checkkuweiAction() {
		$model = new cc_models_kjdbfkrk ();
		$ckbh = $this->_getParam ( "ckbh"); 	//仓库编号
		$kqbh = $this->_getParam ( "kqbh"); 	//库区编号
		$kwbh = $this->_getParam ( "kwbh"); 	//库位编号
		$result = $model->getkuweizht($ckbh,$kqbh,$kwbh);
		echo Common_Tool::json_encode($result);		
	}
	
	/*
	 * 返库入库信息保存
	 */
	public function saveAction() {
		//返回值状态('0'：正常)
		$result['status'] = '0'; 
		try {
			$model = new cc_models_kjdbfkrk ();
			
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $model->beginTransaction ();
			    //入库单编号取得
			    $rkdbh = Common_Tool::getDanhao('DFR',$_POST['KPRQ']);
			    //入库单信息保存
			    $model->saveRukudan($rkdbh);
			    //循环读取明细信息,退货入库更新操作
				$returnValue = $model->executeMingxi($rkdbh);
				//返回值不为0,返库入库数量超过了返库数量
			    if($returnValue['status']!='0'){
			       $result['status'] = '3'; //返库入库数量超过了返库数量,error,返回页面
			       $result['data'] = $returnValue['data'];
			    }
			    //保存成功
			    if($result['status'] == '0'){
			    	//库间调拨出库单信息（H01DB012411)的出库单状态更新
			    	$model->updateChukuZht();
			    	//库间调拨返库单信息（H01DB012423)的退货单状态状态更新
			    	$model->updateFankuZht();
			    	$result['data'] = $rkdbh;//新生成的退货入库单编号
			    	//操作成功时,提交事务
				    $model->commit ();
				    //操作成功时，写入log。Log信息为【库间调拨入库确认 单据编号：xxxx】
				    Common_Logger::logToDb("库间调拨入库确认 单据编号：".$rkdbh);
			    }else{
				    $model->rollBack ();//必须输入项，项目合法性错误，或者返库入库数量超过了返库数量，事务回滚
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//db出错，事务回滚
			$model->rollBack ();
     		throw $e;
		}
	
	}
	
}