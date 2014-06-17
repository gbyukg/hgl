<?php
/**********************************************************
 * 模块：    销售模块(XS)
 * 机能：    网上销售订单(WSXSDD)
 * 作成者：LiuCong
 * 作成日：2011/10/21
 * 更新履历：
 **********************************************************/
class xs_wsxsddController extends xs_controllers_baseController {
	/*
	 * 销售开票初始页面
	 */
	public function indexAction() {
    	$xskpModel = new xs_models_wsxsdd();
    	$this->_view->assign ( "title", "销售管理-网上销售订单" );              //标题
		$this->_view->assign ( "kprq", date("Y-m-d") );                        //开票日期
		$this->_view->assign ( "kpymch", $_SESSION ['auth']->userName );       //开票员
		$this->_view->assign ( "rec", $xskpModel->getDanweiInfo() );
		$this->_view->display ( "wsxsdd_01.php" );
	}
	
	
	/*
	 * 销售订单数据保存
	 */
	public function saveAction() {
		$result['status'] = '0';  //处理结果
		try {
			$xskpModel = new xs_models_wsxsdd();
			
			//必须输入项验证
			if(!$xskpModel->inputCheck($_POST)){
				$result['status'] = '1';  //必须输入项验证错误
//			}elseif(!$xskpModel->logicCheck($_POST)){
//				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $xskpModel->beginTransaction ();
			    
			    //网上销售单编号取得
			    $xshdbh = Common_Tool::getDanhao('WXD');
			    $result['xshdbh'] = $xshdbh;
			    
                //生成销售订单(销售单，销售单明细)
			    $xskpModel->createXshd($xshdbh,$_POST);

			    //正常开票完成或者有部分需要审批
			    if($result['status'] == '0' || $result['status'] == '3'){
				    $xskpModel->commit();
				    Common_Logger::logToDb("新建网上销售订单：".$xshdbh);
			    }else{
				    $xskpModel->rollBack();//有错误发生
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$xskpModel->rollBack();
     		throw $e;
		}
	}
	
	
    /*
     * 通过商品编号取得商品相关信息
     */
	public function getshangpininfoAction(){
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$xskpModel = new xs_models_wsxsdd();
		
	    echo json_encode($xskpModel->getShangpinInfo($filter));
	}
	
	
	/*
	 * 检查账期是否超期
	 */
	public function checkxdqAction(){
		$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_wsxsdd();
		
	    echo $xskpModel->checkXdq($filter);
	}

	
	//价格信息
	public function getjiageinfoAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$xskpModel = new xs_models_wsxsdd();
 		//特价一品多价信息
 		$result = $xskpModel->getJiageInfo($filter);
     	echo json_encode($result);
 	}
 
}