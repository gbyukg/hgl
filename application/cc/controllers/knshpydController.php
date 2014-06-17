<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    库内商品移动(KNSHPYD)
 * 作成者：苏迅
 * 作成日：2010/1/10
 * 更新履历：
 *********************************/
class cc_knshpydController extends cc_controllers_baseController {
	
	/*
	 * 库内商品移动
	 */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh); 	  //部门编号
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "title", "仓储管理-库内商品移动" ); 	//标题
		$this->_view->display ( "knshpyd_01.php" );
	}
	
	/*
	 * 调入库位,调出库位选择页面
	 */
	public function kuweiAction() {
/*		$this->_view->assign("ckbh",$this->_getParam("ckbh"));   //仓库编号
		$this->_view->assign("ckmch",$this->_getParam("ckmch")); //仓库名称
		$this->_view->assign("ckdz",$this->_getParam("ckdz"));   //仓库地址*/
		$this->_view->assign ( "title", "库位选择" ); 			//标题
		$this->_view->display ( "knshpyd_02.php" );
	}
	
	/*
	 * 在库商品选择画面
	 */
	public function kucunlistAction() {
/*		$this->_view->assign("ckbh",$this->_getParam("ckbh"));	//仓库编号
		$this->_view->assign("kqbh",$this->_getParam("kqbh"));	//库区编号
		$this->_view->assign("kwbh",$this->_getParam("kwbh"));	//库位编号*/		
		$this->_view->assign ( "title", "在库商品选择" ); 			//标题
		$this->_view->display ( "knshpyd_03.php" );
	}
	
	/*
	 * 调出库位，调入库位选择弹出画面数据取得
	 */
	public function getkuweidataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
		//业务相关参数
		$filter ['ckbh'] = $this->_getParam("ckbh","");
			
	    $kuwei_model = new cc_models_knshpyd();
		header("Content-type:text/xml");
		echo $kuwei_model->getListData($filter);
	}
	
	/*
	 * 在库商品选择弹出画面数据取得
	 */
	public function getzaikudataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		//$filter ['orderby'] = $this->_getParam("orderby",1);
		//$filter ['direction'] = $this->_getParam("direction",'ASC');
		//检索条件
		$filter ['searchkey'] = $this->_getParam("searchkey","");
		$filter ['ckbh'] = $this->_getParam("ckbh","");
		$filter ['kqbh'] = $this->_getParam("kqbh","");
		$filter ['kwbh'] = $this->_getParam("kwbh","");
		
		$zaiku_model = new cc_models_knshpyd();
		header("Content-type:text/xml");
		echo $zaiku_model->getZaikuListData($filter);
	}
		
	
	/*
	 * 取得库位信息(check仓库/库区/库位状态是否为已删除或冻结)
	 */
	public function checkkuweiAction() {
		$model = new cc_models_knshpyd ();
		$ckbh = $this->_getParam ( "ckbh"); 	//仓库编号
		$kqbh = $this->_getParam ( "kqbh"); 	//仓库编号
		$kwbh = $this->_getParam ( "kwbh"); 	//库位编号
		$result = $model->getkwzht($ckbh,$kqbh,$kwbh);
		echo Common_Tool::json_encode($result);		
	}
	
			
	/*
	 * 退货入库信息保存
	 */
	public function saveAction() {
		//返回值状态('0'：正常)
		$result['status'] = '0'; 
		try {
			$model = new cc_models_knshpyd();
			
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $model->beginTransaction ();
			    //库内商品移动单取得
			    $yddbh = Common_Tool::getDanhao('DBN',$_POST['KPRQ']);
			    //库内移动信息保存
			    $model->saveKuneiXinxi($yddbh);
			    //库内移动明细信息保存
			    $model->saveKuneiMingxi($yddbh);
				//库存相关数据更新
			    $returnValue =  $model->updateKucun($yddbh);
			    if($returnValue['status']!='0'){
			       $result['status'] = '3'; //库存不足	
			       $result['data'] = $returnValue['data']; //库存数据
			    }
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $yddbh;//新生成的退货入库单编号
				    $model->commit ();
				    Common_Logger::logToDb("库内商品移动 单据编号：".$yddbh);
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