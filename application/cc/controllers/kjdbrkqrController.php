<?php
/**********************************************************
 * 模块：    仓储模块(CC)
 * 机能：    库间调拨入库确认(KJDBRKQR)
 * 作成者：刘枞
 * 作成日：2011/01/20
 * 更新履历：
 **********************************************************/
class cc_kjdbrkqrController extends cc_controllers_baseController {
	/*
	 * 库间调拨入库确认初始页面
	 */
	public function indexAction(){
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门编号
		$bh = $this->_getParam( "bh" );        //库间调拨出库单查询画面传递过来的单据编号
		$model = new cc_models_kjdbrkqr();
		$rec = $model->getinfoData( $bh );
		$this->_view->assign ( "rec", $rec ); 
		$this->_view->assign ( "kprq", date("Y-m-d") );                //开票日期
		$this->_view->assign ( "title", "仓储管理-库间调拨入库确认" );  //标题
		$this->_view->display ( "kjdbrkqr_01.php" );
	}
	
    /*
     * 库间调拨入库确认初始页面
     */
    public function loadAction(){
    	$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
        $bh = $this->_getParam( "bh" );        //库间调拨出库单查询画面传递过来的单据编号
        $model = new cc_models_kjdbrkqr();
        $rec = $model->getinfoData( $bh );
        $this->_view->assign ( "rec", $rec ); 
        $this->_view->assign ( "kprq", date("Y-m-d") );                //开票日期
        $this->_view->assign ( "title", "仓储管理-库间调拨入库确认" );  //标题
        $this->_view->display ( "kjdbrkqr_01.php" );
    }
	
	/*
	 * 库位选择页面显示
	 */
	public function kuweiAction(){
		//取得参数，并传送到新页面
		$this->_view->assign ( "ckbh", $this->_getParam( "ckbh" ) );  //仓库编号
		$this->_view->assign ( "title", "库位选择" ); 			      //标题
		$this->_view->display ( "kjdbrkqr_02.php" );
	}
	
	
	/*
	 * 明细选择页面
	 */
	public function mxlistAction(){
		$this->_view->assign("bh",$this->_getParam("bh"));	    //编号
		$this->_view->assign ( "title", "明细选择" ); 		    //标题
		$this->_view->display ( "kjdbrkqr_03.php" );
	}
	
	/*
	 * 明细选择画面GRID-XML数据取得
	 */
	public function getmxlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_kjdbrkqr();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/*
	 * check单据状态
	 */
	public function checkzhtAction(){
		$model = new cc_models_kjdbrkqr();
		$result = $model->getzht($this->_getParam("bh"));
		echo Common_Tool::json_encode($result);		
	}
	
	
	/*
	 * 取得库位信息(check仓库/库区/库位状态是否为已删除或冻结)
	 */
	public function checkkuweiAction() {
		$model = new cc_models_kjdbrkqr();
		$ckbh = $this->_getParam ( "ckbh"); 	//仓库编号
		$kqbh = $this->_getParam ( "kqbh"); 	//库区编号
		$kwbh = $this->_getParam ( "kwbh"); 	//库位编号
		$result = $model->getkuweizht($ckbh,$kqbh,$kwbh);
		echo Common_Tool::json_encode($result);
	}

	
	/**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cc_models_kjdbrkqr();
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
	
	/*
	 * 库间调拨入库确认操作
	 */
	public function saveAction(){
		$result['status'] = '0'; 
		try {
			$Model = new cc_models_kjdbrkqr();
			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';         //必须输入项验证错误
			}elseif(!$Model->timeCheck()){
				$result['status'] = '2';         //调拨出库单有新的修改，请刷新画面后重试。    
			}else{
				$returnSl = $Model->slCheck();
				if($returnSl['status']!='0'){
			        $result['status'] = '4';                //商品入库数量大于未收货数量，请重新输入。
			        $result['data'] = $returnSl['data'];    //出问题数据
				}else{
					$returnKqlx = $Model->kqlxCheck();
					if($returnKqlx['status']!='0'){
				        $result['status'] = '5';                //明细信息的库区类型同该商品的指定库区类型不一致，请选择xxx。
				        $result['data'] = $returnKqlx['data'];  //出问题数据
				    }else{
					    $Model->beginTransaction();			    //开始一个事务
					    $bh = Common_Tool::getDanhao('DBR',$_POST['KPRQ']);	   //出库单编号取得

					    //库存相关数据更新（库存数量验证，库存数量更新，商品移动履历）
					    $returnKucun = $Model->updateKucun($bh);
					    if($returnKucun['status']!='0'){
					        $result['status'] = '3';                //入库数量超过了调拨出库数量，请确认	
					        $result['data'] = $returnKucun['data']; //数据
					    }else{
					    	$Model->saveMain($bh);		       //信息保存
					    	$Model->saveMingxi($bh);	       //明细保存
					    	$Model->updateCgthzht();           //更新单据状态
					    }

					    if($result['status'] == '0'){	       //保存成功
					    	$result['bh'] = $bh;
						    Common_Logger::logToDb("库间调拨入库确认，库间调拨入库单编号：".$result['bh']);
						    $Model->commit();
					    }else{
						    $Model->rollBack();          //有错误发生,事务回滚
					    }
				    }
				}
			}
			echo json_encode($result);
		} catch ( Exception $e ){
			$Model->rollBack();		        //回滚
     		throw $e;
		}
	}
	
	
	/*
	 * 明细信息商品编号自动填充
	 */
	public function shangpinautocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');   //检索项目值
    	$filter ['bh'] = $this->_getParam('bh');         //编号
        $shangpin_model = new cc_models_kjdbrkqr();
	    $result = $shangpin_model->getshangpinAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}


	/**
     * 取得库间调拨出库单明细信息
     */
	public function getmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');     //单位编号
 		$Model = new cc_models_kjdbrkqr();
	    echo Common_Tool::json_encode($Model->getmingxi($filter));

//		$filter ['bh'] = $this->_getParam('bh');
//		$model = new cc_models_kjdbrkqr();
//        header ( "Content-type:text/xml" ); //返回数据格式xml     
//        echo $model->getmingxi ( $filter );
	}
	
	/*
	 * 库位选择页面数据取得
	 */
	public function getkuweidataAction(){
		$ckbh = $this->_getParam("ckbh");     //仓库编号
		$model = new cc_models_kjdbrkqr();
		header("Content-type:text/xml");
		echo $model->getkuweiData($ckbh);	
	}
}