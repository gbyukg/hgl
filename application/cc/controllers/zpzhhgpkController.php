<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    赠品转合格品库(ZPZHHGPK)
 * 作成者：苏迅
 * 作成日：2011/7/18
 * 更新履历：
 *********************************/
class cc_zpzhhgpkController extends cc_controllers_baseController {
	
	/*
	 * 赠品转合格品库初始画面
	 */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  			//部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh); 	  			//部门编号
		$this->_view->assign ( "kpymch", $_SESSION ["auth"]->userName ); 	//开票员
		$this->_view->assign ( "kprq", date("Y-m-d"));  					//开票日期
		$this->_view->assign ( "title", "仓储管理-赠品转合格品库" ); 			//标题
		$this->_view->display ( "zpzhhgpk_01.php" );
	}
	
	/*
	 * 在库赠品选择画面
	 */
	public function zpkucunlistAction() {	
		$this->_view->assign ( "title", "在库赠品选择" ); 					//标题
		$this->_view->display ( "zpzhhgpk_02.php" );
	}
	
	
	/*
	 * 在库赠品选择弹出画面数据取得
	 */
	public function getzaikudataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		//检索条件
		$filter ['searchkey'] = $this->_getParam("searchkey","");
		
		$zaiku_model = new cc_models_zpzhhgpk();
		header("Content-type:text/xml");
		echo $zaiku_model->getZaikuListData($filter);
	}	
			
	/*
	 * 赠品转合格品库信息保存
	 */
	public function saveAction() {
		//返回值状态('0'：正常)
		$result['status'] = '0'; 
		try {
			$model = new cc_models_zpzhhgpk();			
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				$shpchkgxx = $model->getshpchkg();//取得大包装信息
				if($shpchkgxx['status'] != '0'){  //商品无效或大包装信息不全
					 $result = $shpchkgxx;		  //$result['status']=='4'商品无效   $result['status'] =='3'包装信息错误
				}
				if($result['status'] == '0'){
					//开始一个事务
				    $model->beginTransaction ();
				    //新生成赠品出库单编号
				    $zpckdbh = Common_Tool::getDanhao('ZPC',$_POST['KPRQ']);
				    //新生成合格品入库单编号
				    $rkdbh = Common_Tool::getDanhao('RKD',$_POST['KPRQ']);
				    //赠品出库单信息保存
				    $model->saveZpChkd($zpckdbh,$rkdbh);
				    //合格品入库单信息保存
				    $model->saveHgpRkd($zpckdbh,$rkdbh);
				    //赠品出库明细信息保存--**赠品库不涉及库位、入库单号,只需处理画面grid明细即可**
				    $model->saveZpchkdmx($zpckdbh);
					//库存相关数据更新，以及合格品入库明细信息保存--**库位自动分配时，入库明细需要分解画面grid明细**
				    $returnValue =  $model->updateKucun($rkdbh,$zpckdbh);
				    if($returnValue['status']!='0'){
				       $result['status'] = '5'; //库存不足	
				       $result['data'] = $returnValue['data']; //库存数据
				    }
				    //保存成功
				    if($result['status'] == '0'){
				    	$result['data'] = $rkdbh;//新生成的合格品入库单编号
					    $model->commit ();
					    Common_Logger::logToDb("赠品转合格品库  赠品出库单编号:".$zpckdbh." 合格品入库单编号：".$rkdbh);
				    }else{
					    $model->rollBack ();//有错误发生
				    }
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
	 * check商品大包装长宽高信息
	 * 如果大包装长宽高信息不存在，无法自动分配货位
	 */
	public function checkchkgAction() {
		$model = new cc_models_zpzhhgpk ();
		$shpbh = $this->_getParam ("shpbh"); 	//商品编号
		$result = $model->getshpchkg($shpbh);
		echo Common_Tool::json_encode($result);		
	}
	
	
}