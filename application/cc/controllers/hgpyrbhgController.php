<?php
/*********************************
 * 模块：    仓储模块(cc)
 * 机能：    合格品移入不合格品区(CGKP)
 * 作成者：姚磊
 * 作成日：2011/08/13
 * 更新履历：
 *********************************/
class cc_hgpyrbhgController extends cg_controllers_baseController {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1;// 商品编号
	private $idx_SHPMCH = 2;// 商品名称
	private $idx_GUIGE = 3;// 商品规格
	private $idx_BZHDWM = 4;// 包装单位
	private $idx_JLGG = 5;// 计量规格
	private $idx_DCKUW = 6;//调出库位 
	private $idx_PIHAO = 7;//批号
	private $idx_SHCHRQ = 8;//生成日期
	private $idx_BZHQZH = 9;//保质期至
	private $idx_BZHSHL = 10;// 包装数量
	private $idx_LSSHL = 11;// 零散数量
	private $idx_SHULIANG = 12;// 数量
	private $idx_CHANDI = 13;// 产地
	private $idx_BEIZHU = 14;// 备注
	private $idx_CKBH = 15;//仓库编号
	private $idx_KQBH = 16;//库区编号
	private $idx_KWBH = 17;//库位编号
	private $idx_KCSHUL= 18;//库存数量
	private $idx_DANJIA = 19;// 单价
	private $idx_HSHJ = 20;// 含税价
	private $idx_KOULV = 21;// 扣率
	private $idx_SHUILV = 22;// 税率
	private $idx_HSHJE = 23;// 含税金额
	private $idx_JINE = 24; // 金额
	private $idx_SHUIE = 25;// 税额
	private $idx_SHFSHKW = 26; // 是否零散库位
	private $idx_TONGYONGMING = 27;//通用名
	private $idx_BZHDWBH = 28;//包装单位编号
	private $idx_TYMCH = 29;//通用名
	private $idx_KWZT = 30;//库位状态
	
	
	/*
	 * 合格品移入不合格品区初始页面
	 */
	public function indexAction() { 
		$this->_view->assign ( 'action', 'index' ); 
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "kpybh", $_SESSION ["auth"]->userName );  //开票员编号，待换成名称
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$this->_view->assign ( "title", "仓储管理-合格品移入不合格品区" ); //标题
		$this->_view->display ( "hgpyrbhg_01.php" );
	}
	/*
	 * 合格品移入不合格品区 - 在库商品选择
	 */
	public function zaikushpxxAction(){
		$this->_view->assign ( "title", "仓储管理-在库商品选择" ); //标题
		$rkdh = $this->_getParam('flg');   //获取参数 1 首次选择在库商品 /入库单号,再次选择在库商品
		$this->_view->assign ( "rkdh", $rkdh ); //入库单号
		$this->_view->display ( "hgpyrbhg_02.php" );
	}

	/*
	 * 保存合格品入库数据
	 */
	function saveAction(){
		$result['status'] = '0'; 
		try{
			$Model = new cc_models_hgpyrbhg();
		    	$Model->beginTransaction ();
			    //合格品移入不合格品区单编号取得
			    $ckdbh = Common_Tool::getDanhao('CKD',$_POST['KPRQ']); //出库单据号
			    $rkdbh = Common_Tool::getDanhao('BHR',$_POST['KPRQ']); //入库单据号
			    //保存出库信息
			    $Model->saveMain($ckdbh,$rkdbh);
			    //保存不合格品入库信息
			    $Model->savebhgMain($rkdbh);
			    //保存出库单明细
			    $Model->saveMingxi($ckdbh);
			    //在库商品,移动履历
			    $rec = $Model->getYdlvli($ckdbh);
			     if($rec['status']!='0'){
			     	$result['status'] = '3';//其他库出库,错误信息
			     	$Model->rollBack ();
			     }else{
			    //保存不合格品信息 明细信息
			    $Model->savebhgxx($rkdbh);    			    
			    //更新不合格品在库信息
			    $Model->updatebhgxx($rkdbh);  
				$result['data']=$_POST ['RKDBH'];
			    $Model->commit ();
			     }

				echo json_encode($result);
		}catch( Exception $e){
		//回滚
			$Model->rollBack ();
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
 		$cgkpModel = new cc_models_hgpyrbhg();
		
	    echo json_encode($cgkpModel->getShangpinInfo($filter));
	}
	
	/**
	 * 获取入库限定数量
	 */
	public function getrkxzhshlAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$rkxzhshl = new cc_models_hgpyrbhg();
		echo Common_Tool::json_encode($rkxzhshl->getRkxzhshlInfo($filter));
	}
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new cc_models_hgpyrbhg ( );
 		$result['dwinfo']=$xskpModel->DanweiInfo($filter);
		//$ywyModel = new gt_models_ywy();
 		$filter ['flg'] = '0'; //采购
 		$result['ywyinfo'] =  $xskpModel->getData($filter);
 		 echo Common_Tool::json_encode($result);

	}
	
	/*
	 * 检查账期是否超期
	 */
	public function checkxdqAction(){
		
		$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_xskp ( );
		
	    echo $xskpModel->checkXdq($filter);
	}

 	/*
 	 * 在库商品第一次展开查询
 	 */
 	public function getshpxxAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量					
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		$rkdh = $this->_getParam ( "flg" );
		if($rkdh == 'undefined'){
			$rkdh = NULL;
		}
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hgpyrbhg_searchParams'] = $_POST;
				unset($_SESSION['hgpyrbhg_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hgpyrbhg_filterParams'] = $_POST;
				unset($_SESSION['hgpyrbhg_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['hgpyrbhg_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['hgpyrbhg_searchParams'];  //固定查询条件
		$model = new cc_models_hgpyrbhg();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getfristGridData ($filter,$rkdh);
		
 	}
 	
 	/*
 	 * 获取grid返回值商品信息
 	 * 
 	 */
 	public function getshpxxaAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量					
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		$filter['SHPBH'] =  $this->_getParam ( "shpbh" ); //商品编号
		$filter['RKDBH'] =  $this->_getParam ( "rkdbh" ); //商品编号
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hgpyrbhg_searchParams'] = $_POST;
				unset($_SESSION['hgpyrbhg_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hgpyrbhg_filterParams'] = $_POST;
				unset($_SESSION['hgpyrbhg_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['hgpyrbhg_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['hgpyrbhg_searchParams'];  //固定查询条件
		$model = new cc_models_hgpyrbhg();
		$result =  $model->getafristGridData ($filter );
		echo Common_Tool::json_encode($result);
 	}
 	


 	

 	
 	
 	
}