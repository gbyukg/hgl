<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：  配送订单生成(psdsc)
 * 作成者：梁兆新
 * 作成日：2011/1/7
 * 更新履历：
 *********************************/
class ps_psdscController extends ps_controllers_baseController {
	/*
     * 配送单信息列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '配送单生成' );
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->display ( 'psdsc_01.php' );
		
	}
	/*
     * 配送单信-【配送车载】息列表画面显示
     */
	public function psczAction() {
		$psdsc_model = new ps_models_psdsc();
		$this->_view->assign ( 'title', '配载装车' );
		$this->_view->assign ( 'doaction', $this->_getParam("doaction"),'');
		$this->_view->assign ( 'fhqlist',$psdsc_model->getquhao());//得到发货区信息
		$this->_view->display ( 'psdsc_02.php' );
	}
	 /*
	 * 出库单列表信息
	 */
	public function getlistdataAction(){
    	$filter ['fhqbh'] = $this->_getParam("fhqbh",0); //出库编号
        $filter ['stime'] = $this->_getParam("stime",0); //开始日期
		$filter ['etime'] = $this->_getParam("etime",0); //终止日期
		
		$psdsc_model = new ps_models_psdsc();
		header("Content-type:text/xml");
		echo $psdsc_model->getListData($filter);
	}
	/*
	 * 待配出库单明细信息
	 */
	public function getdpslistdataAction(){
    	$filter ['chdh'] = $this->_getParam("chdh",0); //出库单号
    	$filter ['dwmch'] = $this->_getParam("dwmch",0); //单位名称
    	$filter ['dizhi'] = $this->_getParam("dizhi",0); //地址
    	$filter['dianhua'] = $this->_getParam("dianhua",0); //电话
    	$filter['chkrq'] = $this->_getParam("chkrq",0); //出库日期
    	$filter['xshdh']= $this->_getParam("xshdh",0); //销售单号
    	$filter['xshrq']= $this->_getParam("xshrq",0); //销售日期
    	
    	
		$psdsc_model = new ps_models_psdsc();
		header("Content-type:text/xml");
		echo $psdsc_model->getdpsListData($filter);
	}
	/*
	 * 派送订单生成
	 */

	public function saveAction() {		
		$result['status'] = '0'; 
		try {
			$psdscModel = new ps_models_psdsc( );
			//必须输入项验证
			if(!$psdscModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$psdscModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $psdscModel->beginTransaction ();
			    //配送单编号取得
			    $psdscbh = Common_Tool::getDanhao('PSD',$_POST['PSRQ']);
			    //配送订单保存
			  $psdscModel->savePshdMain($psdscbh);
			    //配送单明细保存
			  $psdscModel->savePsMingX($psdscbh);
			  //配送单商品明细保存
			  $psdscModel->savePsspMingX($psdscbh);
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $psdscbh;
				    $psdscModel->commit ();
				    Common_Logger::logToDb("配送单生成    单据号：".$result['data']);
			    }else{
				    $psdscModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$psdscModel->rollBack ();
     		throw $e;
		}	
	}
	
	
	
}
