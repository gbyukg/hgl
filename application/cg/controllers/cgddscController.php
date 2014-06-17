<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购订单生成(CGDDSC)
 * 作成者：姚磊
 * 作成日：2011/1/12
 * 更新履历：
 *********************************/
class cg_cgddscController extends cg_controllers_baseController {

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
	private $idx_BEIZHU = 10; // 备注
	private $idx_TONGYONGMING = 11; // 通用名	
	private $idx_BZHDWBH = 12; // 包装单位编号
	private $idx_XUHAO = 13; // 序号
	
	
	/*
	 * 采购订单生成初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "kpybh", $_SESSION ["auth"]->userName );  //开票员编号，待换成名称
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$this->_view->assign ( "title", "采购管理-采购计划生成" ); //标题				
		$this->_view->display ( "cgddsc_01.php" );
	}				
	/*
	 * 保存订单生成数据
	 */
	function savecgAction(){
		$result['status'] = '0'; 
		try{
			$cgkpModel = new cg_models_cgddsc();
		    	$cgkpModel->beginTransaction ();
			    //采购订单编号取得
			    $cgkpbh = Common_Tool::getDanhao('CDD',$_POST['KPRQ']); //采购单据号
			    //采购开票生成保存
			    $cgkpModel->saveCgkpMain($cgkpbh);
			    $cgkpModel->saveCgkpMingxi($cgkpbh);
			    //采购开票订单明细保存
			    $cgkpModel->commit ();
				Common_Logger::logToDb ("采购计划生成 单据号：".$cgkpbh);
				$result['data'] = $cgkpbh;
				echo json_encode($result);
		}catch( Exception $e){
		//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
		}

		
		
		
	
    /**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$cgkpModel = new cg_models_cgddsc();
		
	    echo json_encode($cgkpModel->getShangpinInfo($filter));
	}
	
	/**
	 * 获取入库限定数量
	 */
	public function getrkxzhshlAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$rkxzhshl = new cg_models_cgkp();
		echo Common_Tool::json_encode($rkxzhshl->getRkxzhshlInfo($filter));
	}
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new cg_models_cgddsc ( );
		
	    echo Common_Tool::json_encode($xskpModel->getDanweiInfo($filter));
	}
	
	/*
	 * 检查账期是否超期
	 */
	public function checkxdqAction(){
		
		$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_xskp ( );
		
	    echo $xskpModel->checkXdq($filter);
	}



 	

 	
 	
 	
 	
 	
}