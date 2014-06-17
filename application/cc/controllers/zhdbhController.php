<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    指定补货(ZHDBH)
 * 作成者：DLTT_LiuCong
 * 作成日：2011/06/23
 * 更新履历：
 *********************************/
class cc_zhdbhController extends cc_controllers_baseController {
	
	/*
	 * 指定补货初始页面
	 */
	public function indexAction() {
    	$this->_view->assign ( "title", "仓储管理-指定补货" ); //标题
		$this->_view->display ( "zhdbh_01.php" );
	}
	
	
	/*
	 * 库存选择弹出画面
	 */
	public function listAction(){
		$this->_view->assign('shpbh',$this->_getParam("shpbh"));//商品编号
		$this->_view->display ( "zhdbh_02.php" );
	}
	
	
	/*
	 * 库位批号选择画面数据取得
	 */
	public function getlistdataAction()	{
		$filter ['shpbh'] = $this->_getParam("shpbh"); //商品编号
		$model = new cc_models_zhdbh();
		header("Content-type:text/xml");
		echo $model->getListData($filter);		
	}
	
	
	/*
	 * 根据整货库位批号等信息自动计算对应零散库位
	 */
	public function getlskwAction()	{
		$filter ['SHPBH'] = $this->_getParam("shpbh");            //商品编号
		$filter ['ZHJCKBH'] = $this->_getParam("zhjckbh");        //整件仓库编号
		$filter ['ZHJKQBH'] = $this->_getParam("zhjkqbh");        //整件库区编号
		$filter ['ZHJKWBH'] = $this->_getParam("zhjkwbh");        //整件库位编号
		$filter ['PIHAO'] = $this->_getParam("pihao");            //批号
        $filter ['BZHDWBH'] = $this->_getParam("bzhdwbh");        //包装单位编号
        $filter ['RKDBH'] = $this->_getParam("rkdbh");            //入库单编号
        $filter ['ZKZHT'] = $this->_getParam("zkzht");            //在库状态
        $filter ['SHCHRQ'] = $this->_getParam("shchrq");          //生产日期
            
		$model = new cc_models_zhdbh();
		$result = $model->getlskw($filter);
		echo Common_Tool::json_encode($result);	
	}
	
	
	/*
     * 获取商品信息
     */
	public function getshpxxAction() {
		$model = new cc_models_zhdbh();
		$result = $model->getshpxx($this->_getParam ('shpbh'));
		echo Common_Tool::json_encode($result);
	}
	
	
	/*
	 * 补货保存
	 */
	public function saveAction() {
		$result['status'] = '0';  //处理结果
		try {
			$Model = new cc_models_zhdbh();
			
			//必须输入项验证
			if(!$Model->inputCheck($_POST)){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$Model->logicCheck($_POST)){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $Model->beginTransaction ();
			    
			    $bhdbh = Common_Tool::getDanhao("BHD");//补货单编号
			    $result['bhdbh'] = $bhdbh;
			    
			    //商品补货处理
			    $save = $Model->doSave($bhdbh);
			    
			    //库存有问题
			    if($save['status']!='0'){
			       $result['status'] = '4';            //库存不足
			       $result['data'] = $save['data'];    //库存数据
			    }

			    if($result['status'] == '0' || $result['status'] == '3'){
				    $Model->commit ();
				    Common_Logger::logToDb("指定补货，补货单编号：".$bhdbh);
			    }else{
				    $Model->rollBack ();//有错误发生
			    }
			}
			
			echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$Model->rollBack ();
     		throw $e;
		}
	}
	
	
	
    /**
     * 通过商品编号取得商品相关信息
     */
	public function getshangpininfoAction(){
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$Model = new cc_models_zhdbh();
	    echo json_encode($Model->getShangpinInfo($filter));
	}


}